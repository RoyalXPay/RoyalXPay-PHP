<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\TapDepositModel;
use App\Models\RemittanceWalletModel;
use CodeIgniter\HTTP\ResponseInterface;

class TapWebhookController extends BaseController
{
    protected $depositModel;
    protected $walletModel;

    public function __construct()
    {
        $this->depositModel = new TapDepositModel();
        $this->walletModel = new RemittanceWalletModel();
    }

    /**
     * Single endpoint to receive TAP deposit webhook
     * POST /api/tap/deposit-webhook
     * 
     * This endpoint:
     * 1. Receives deposit notification from TAP
     * 2. HMAC signature is verified by TapWebhookFilter
     * 3. Checks idempotency (prevents duplicates)
     * 4. Processes deposit and updates wallet
     * 5. Returns standardized response
     */
    public function receiveDeposit()
    {
        try {
            // Get raw payload (HMAC already verified by TapWebhookFilter)
            $rawPayload = $this->request->getBody();
            $payload = json_decode($rawPayload, true);

            // Validate payload structure
            if (!$this->validatePayloadStructure($payload)) {
                log_message('error', 'TAP Webhook: Invalid payload structure - ' . json_encode($payload));
                return $this->standardResponse(400, 'Invalid payload structure', null);
            }

            // Extract TAP event details
            $tapEventId = $payload['id'] ?? $payload['event_id'] ?? null;
            $eventType = $payload['event'] ?? $payload['event_type'] ?? null;
            $chargeData = $payload['data'] ?? $payload;

            // Check idempotency - prevent duplicate processing
            if ($this->depositModel->eventExists($tapEventId)) {
                log_message('info', "TAP Webhook: Event {$tapEventId} already processed");
                return $this->standardResponse(200, 'Event already processed', [
                    'event_id' => $tapEventId,
                    'status' => 'duplicate'
                ]);
            }

            // Extract transaction details
            $transactionId = $chargeData['id'] ?? null;
            $amount = $chargeData['amount'] ?? 0;
            $currency = strtoupper($chargeData['currency'] ?? 'BDT');
            $tapStatus = $chargeData['status'] ?? 'pending';
            $customerPhone = $chargeData['customer']['phone']['number'] ?? null;
            $metadata = $chargeData['metadata'] ?? [];
            $userId = $metadata['user_id'] ?? null;

            // Validate required fields
            if (!$transactionId || !$amount || !$customerPhone) {
                log_message('error', 'TAP Webhook: Missing required fields');
                return $this->standardResponse(400, 'Missing required transaction fields', null);
            }

            // Find wallet by phone number
            $wallet = $this->walletModel->where('wallet_number', $customerPhone)->first();

            if (!$wallet) {
                log_message('error', "TAP Webhook: Wallet not found for phone: {$customerPhone}");
                
                // Store failed event
                $this->depositModel->insert([
                    'event_id' => $tapEventId,
                    'event_type' => 'wallet.deposit.failed',
                    'transaction_id' => $transactionId,
                    'external_reference' => $tapEventId,
                    'user_id' => $userId,
                    'amount' => $amount,
                    'currency' => $currency,
                    'status' => 'failed',
                    'error_message' => 'Wallet not found',
                    'processed_by' => 'DITSL',
                    'raw_payload' => $rawPayload,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                return $this->standardResponse(400, 'Wallet not found', null);
            }

            // Determine deposit status
            $depositStatus = ($tapStatus === 'CAPTURED' || $tapStatus === 'completed') ? 'completed' : 'pending';

            // Start database transaction
            $db = \Config\Database::connect();
            $db->transStart();

            // Update wallet balance (only for completed deposits)
            if ($depositStatus === 'completed') {
                $newBalance = $wallet['balance'] + $amount;
                $this->walletModel->update($wallet['id'], [
                    'balance' => $newBalance,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Generate unique DITSL event ID
            $ditslEventId = 'DITSL_' . time() . '_' . uniqid();

            // Store deposit event
            $this->depositModel->insert([
                'event_id' => $ditslEventId,
                'event_type' => 'wallet.deposit.' . $depositStatus,
                'transaction_id' => $transactionId,
                'external_reference' => $tapEventId,
                'user_id' => $userId ?? $wallet['id'],
                'wallet_id' => $wallet['id'],
                'wallet_number' => $customerPhone,
                'amount' => $amount,
                'currency' => $currency,
                'status' => $depositStatus,
                'processed_by' => 'DITSL',
                'raw_payload' => $rawPayload,
                'processed_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', 'TAP Webhook: Database transaction failed');
                return $this->standardResponse(500, 'Internal server error', null);
            }

            log_message('info', "TAP Webhook: Deposit processed successfully. Event: {$ditslEventId}, Wallet: {$customerPhone}, Amount: {$amount}");

            // Get updated wallet balance after deposit
            $updatedWallet = $this->walletModel->find($wallet['id']);
            $currentBalance = $updatedWallet['balance'] ?? $wallet['balance'];

            // Return standardized response
            return $this->standardResponse(200, 'Deposit processed successfully', [
                'event_id' => $ditslEventId,
                'event_type' => 'wallet.deposit.' . $depositStatus,
                'timestamp' => date('c'), // ISO 8601 format
                'transaction_id' => $transactionId,
                'external_reference' => $tapEventId,
                'user_id' => $userId ?? $wallet['id'],
                'amount' => $amount,
                'currency' => $currency,
                'status' => $depositStatus,
                'processed_by' => 'DITSL',
                'current_balance' => $currentBalance // Include current balance in response
            ]);

        } catch (\Exception $e) {
            log_message('error', 'TAP Webhook Exception: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());
            return $this->standardResponse(500, 'Internal server error', null);
        }
    }

    /**
     * Validate payload structure
     */
    private function validatePayloadStructure($payload)
    {
        if (!is_array($payload)) {
            return false;
        }

        // Check for required TAP fields
        $hasEventId = isset($payload['id']) || isset($payload['event_id']);
        $hasEventType = isset($payload['event']) || isset($payload['event_type']);
        $hasChargeData = isset($payload['data']) || isset($payload['amount']);

        return $hasEventId && ($hasEventType || $hasChargeData);
    }

    /**
     * Return standardized response format
     */
    private function standardResponse($statusCode, $message, $data)
    {
        return $this->response->setJSON([
            'statusCode' => $statusCode,
            'message' => $message,
            'data' => $data
        ])->setStatusCode($statusCode);
    }
}
