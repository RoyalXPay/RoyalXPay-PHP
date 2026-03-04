<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\TapDepositModel;
use App\Models\RemittanceWalletModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * TAP Transaction Enquiry & Reconciliation Controller
 * 
 * Provides endpoints for:
 * 1. Single transaction enquiry by transaction_id or external_reference
 * 2. Bulk reconciliation queries by date range
 * 
 * Security: Requires HMAC signature authentication (TapWebhookFilter)
 */
class TapTransactionEnquiryController extends BaseController
{
    protected $depositModel;
    protected $walletModel;
    protected $auditModel;

    public function __construct()
    {
        $this->depositModel = new TapDepositModel();
        $this->walletModel = new RemittanceWalletModel();
        $this->auditModel = new \App\Models\TapApiAuditModel();
    }

    /**
     * Single Transaction Enquiry
     * POST /api/tap/transaction-enquiry
     * 
     * Query Parameters:
     * - transaction_id (mandatory for single enquiry)
     * - external_reference (optional but recommended)
     * 
     * @return ResponseInterface
     */
    public function enquiry()
    {
        $startTime = microtime(true);
        $auditData = [
            'endpoint' => 'transaction-enquiry',
            'request_method' => 'POST',
            'request_ip' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->__toString(),
        ];

        try {
            $payload = $this->request->getJSON(true);
            $auditData['request_payload'] = json_encode($payload);
            
            // Validate request
            $transactionId = $payload['transaction_id'] ?? null;
            $externalReference = $payload['external_reference'] ?? null;

            $auditData['transaction_id'] = $transactionId;
            $auditData['external_reference'] = $externalReference;

            if (empty($transactionId) && empty($externalReference)) {
                log_message('error', 'TAP Transaction Enquiry: No identifier provided');
                
                $auditData['response_status'] = 400;
                $auditData['response_message'] = 'Either transaction_id or external_reference is required';
                $auditData['execution_time'] = microtime(true) - $startTime;
                $this->auditModel->logApiCall($auditData);
                
                return $this->standardResponse(400, 'Either transaction_id or external_reference is required', null);
            }

            // Log the enquiry request
            log_message('info', "TAP Transaction Enquiry: transaction_id={$transactionId}, external_reference={$externalReference}");

            // Query transaction
            $transaction = null;
            if (!empty($transactionId)) {
                $transaction = $this->depositModel->where('transaction_id', $transactionId)->first();
            }
            
            if (!$transaction && !empty($externalReference)) {
                $transaction = $this->depositModel->where('external_reference', $externalReference)->first();
            }

            if (!$transaction) {
                log_message('warning', "TAP Transaction Enquiry: Transaction not found - transaction_id={$transactionId}, external_reference={$externalReference}");
                
                $auditData['response_status'] = 404;
                $auditData['response_message'] = 'Transaction not found';
                $auditData['execution_time'] = microtime(true) - $startTime;
                $this->auditModel->logApiCall($auditData);
                
                return $this->standardResponse(404, 'Transaction not found', null);
            }

            // Get wallet details if available
            $wallet = null;
            if (!empty($transaction['wallet_id'])) {
                $wallet = $this->walletModel->find($transaction['wallet_id']);
            }

            // Format response according to specification
            $responseData = $this->formatTransactionResponse($transaction, $wallet);

            log_message('info', "TAP Transaction Enquiry: Success - transaction_id={$transaction['transaction_id']}, status={$transaction['status']}");

            $auditData['response_status'] = 200;
            $auditData['response_message'] = 'Transaction found';
            $auditData['records_returned'] = 1;
            $auditData['execution_time'] = microtime(true) - $startTime;
            $this->auditModel->logApiCall($auditData);

            return $this->standardResponse(200, 'Transaction found', $responseData);

        } catch (\Exception $e) {
            log_message('error', 'TAP Transaction Enquiry Error: ' . $e->getMessage());
            
            $auditData['response_status'] = 500;
            $auditData['response_message'] = 'Internal server error';
            $auditData['error_message'] = $e->getMessage();
            $auditData['execution_time'] = microtime(true) - $startTime;
            $this->auditModel->logApiCall($auditData);
            
            return $this->standardResponse(500, 'Internal server error', null);
        }
    }

    /**
     * Bulk Reconciliation Query
     * POST /api/tap/reconciliation
     * 
     * Query Parameters:
     * - from_date (ISO 8601 format, mandatory)
     * - to_date (ISO 8601 format, mandatory)
     * - status (optional: completed, failed, pending)
     * - limit (optional: default 100, max 1000)
     * - offset (optional: default 0)
     * 
     * @return ResponseInterface
     */
    public function reconciliation()
    {
        $startTime = microtime(true);
        $auditData = [
            'endpoint' => 'reconciliation',
            'request_method' => 'POST',
            'request_ip' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->__toString(),
        ];

        try {
            $payload = $this->request->getJSON(true);
            $auditData['request_payload'] = json_encode($payload);

            // Validate date range
            $fromDate = $payload['from_date'] ?? null;
            $toDate = $payload['to_date'] ?? null;
            $status = $payload['status'] ?? null;
            $limit = min((int)($payload['limit'] ?? 100), 1000); // Max 1000
            $offset = (int)($payload['offset'] ?? 0);

            $auditData['status_filter'] = $status;

            if (empty($fromDate) || empty($toDate)) {
                log_message('error', 'TAP Reconciliation: Missing date range');
                
                $auditData['response_status'] = 400;
                $auditData['response_message'] = 'Both from_date and to_date are required (ISO 8601 format)';
                $auditData['execution_time'] = microtime(true) - $startTime;
                $this->auditModel->logApiCall($auditData);
                
                return $this->standardResponse(400, 'Both from_date and to_date are required (ISO 8601 format)', null);
            }

            // Validate date format (ISO 8601 or simple date)
            if (!$this->isValidIso8601Date($fromDate) || !$this->isValidIso8601Date($toDate)) {
                log_message('error', 'TAP Reconciliation: Invalid date format');
                
                $auditData['response_status'] = 400;
                $auditData['response_message'] = 'Invalid date format. Use ISO 8601 (2026-03-01T00:00:00Z) or date format (2026-03-01)';
                $auditData['execution_time'] = microtime(true) - $startTime;
                $this->auditModel->logApiCall($auditData);
                
                return $this->standardResponse(400, 'Invalid date format. Use ISO 8601 (2026-03-01T00:00:00Z) or date format (2026-03-01)', null);
            }

            // Convert ISO 8601 to MySQL datetime
            // Handle both date-only (2026-03-01) and full ISO 8601 (2026-03-01T00:00:00Z)
            $fromDateMysql = date('Y-m-d 00:00:00', strtotime($fromDate));
            $toDateMysql = date('Y-m-d 23:59:59', strtotime($toDate));

            $auditData['from_date'] = $fromDateMysql;
            $auditData['to_date'] = $toDateMysql;

            // Log reconciliation request
            log_message('info', "TAP Reconciliation: from={$fromDate}, to={$toDate}, status={$status}, limit={$limit}, offset={$offset}");
            log_message('debug', "TAP Reconciliation: MySQL query range: {$fromDateMysql} to {$toDateMysql}");

            // Build query
            $builder = $this->depositModel->builder();
            $builder->where('created_at >=', $fromDateMysql)
                    ->where('created_at <=', $toDateMysql);

            if (!empty($status) && in_array($status, ['completed', 'failed', 'pending'])) {
                $builder->where('status', $status);
            }

            // Get total count for pagination
            $totalCount = $builder->countAllResults(false);

            // Get transactions
            $transactions = $builder->orderBy('created_at', 'ASC')
                                   ->limit($limit, $offset)
                                   ->get()
                                   ->getResultArray();

            // Format response
            $formattedTransactions = [];
            foreach ($transactions as $transaction) {
                $wallet = null;
                if (!empty($transaction['wallet_id'])) {
                    $wallet = $this->walletModel->find($transaction['wallet_id']);
                }
                $formattedTransactions[] = $this->formatTransactionResponse($transaction, $wallet);
            }

            log_message('info', "TAP Reconciliation: Found {$totalCount} transactions, returned " . count($formattedTransactions));

            $auditData['response_status'] = 200;
            $auditData['response_message'] = 'Reconciliation data retrieved';
            $auditData['records_returned'] = count($formattedTransactions);
            $auditData['total_records'] = $totalCount;
            $auditData['execution_time'] = microtime(true) - $startTime;
            $this->auditModel->logApiCall($auditData);

            return $this->standardResponse(200, 'Reconciliation data retrieved', [
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'total_count' => $totalCount,
                'returned_count' => count($formattedTransactions),
                'limit' => $limit,
                'offset' => $offset,
                'transactions' => $formattedTransactions
            ]);

        } catch (\Exception $e) {
            log_message('error', 'TAP Reconciliation Error: ' . $e->getMessage());
            
            $auditData['response_status'] = 500;
            $auditData['response_message'] = 'Internal server error';
            $auditData['error_message'] = $e->getMessage();
            $auditData['execution_time'] = microtime(true) - $startTime;
            $this->auditModel->logApiCall($auditData);
            
            return $this->standardResponse(500, 'Internal server error', null);
        }
    }

    /**
     * Format transaction response according to specification
     * 
     * @param array $transaction
     * @param array|null $wallet
     * @return array
     */
    private function formatTransactionResponse($transaction, $wallet = null)
    {
        // Determine finality
        $isFinal = in_array($transaction['status'], ['completed', 'failed']);

        return [
            'transaction_id' => $transaction['transaction_id'],
            'external_reference' => $transaction['external_reference'] ?? $transaction['event_id'],
            'user_id' => $transaction['user_id'],
            'wallet_number' => $transaction['wallet_number'],
            'amount' => (float)$transaction['amount'],
            'currency' => $transaction['currency'],
            'status' => $transaction['status'], // completed / failed / pending
            'processed_at' => date('c', strtotime($transaction['processed_at'] ?? $transaction['created_at'])), // ISO 8601
            'failure_reason' => $transaction['error_message'], // Nullable
            'final' => $isFinal, // Boolean indicating finality
            'current_balance' => $wallet ? (float)$wallet['balance'] : null,
            'event_id' => $transaction['event_id'],
            'event_type' => $transaction['event_type']
        ];
    }

    /**
     * Validate ISO 8601 date format
     * 
     * @param string $date
     * @return bool
     */
    private function isValidIso8601Date($date)
    {
        // Try to parse the date
        $timestamp = strtotime($date);
        return $timestamp !== false;
    }

    /**
     * Standard API response format
     * 
     * @param int $statusCode
     * @param string $message
     * @param mixed $data
     * @return ResponseInterface
     */
    private function standardResponse(int $statusCode, string $message, $data = null)
    {
        return $this->response->setJSON([
            'statusCode' => $statusCode,
            'message' => $message,
            'data' => $data
        ])->setStatusCode($statusCode);
    }
}
