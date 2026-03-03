<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UsersModel;
use App\Models\AccessTokenModel;
use App\Models\RemittanceWalletModel;
use App\Models\RemittanceTransactionModel;
use App\Models\RemittanceSenderInfoModel;
use Exception;

class RemittanceController extends ResourceController
{
    protected $format = 'json';
    protected $userModel;
    protected $tokenModel;
    protected $walletModel;
    protected $transactionModel;
    protected $senderInfoModel;

    public function __construct()
    {
        $this->userModel = new UsersModel();
        $this->tokenModel = new AccessTokenModel();
        $this->walletModel = new RemittanceWalletModel();
        $this->transactionModel = new RemittanceTransactionModel();
        $this->senderInfoModel = new RemittanceSenderInfoModel();
    }

    public function getToken()
    {
        $json = $this->request->getJSON(true);

        if (empty($json['username']) || empty($json['password'])) {
            return $this->respond([
                'statusCode' => 400,
                'message' => 'Username and password are required',
                'data' => null
            ], 400);
        }
        
        $user = $this->userModel->where('username', $json['username'])
                                ->where('status', 'active')
                                ->first();
        
        if (!$user) {
            return $this->respond([
                'statusCode' => 401,
                'message' => 'Credentials not matched!',
                'data' => null
            ], 401);
        }

        if (!password_verify($json['password'], $user['password'])) {
            return $this->respond([
                'statusCode' => 401,
                'message' => 'Credentials not matched!',
                'data' => null
            ], 401);
        }

        // Generate token
        $tokenData = $this->tokenModel->generateToken($user['user_id'], $user['username']);
        
        return $this->respond([
            'statusCode' => 200,
            'message' => 'SUCCESS',
            'data' => [
                'accessToken' => $tokenData['token'],
                'expiryDateTime' => date('Y-m-d\TH:i:s.u\Z', strtotime($tokenData['expires_at']))
            ]
        ], 200);
    }

    public function validateUser()
    {
        $json = $this->request->getJSON(true);
        
        if (empty($json['WalletNumber'])) {
            return $this->respond([
                'statusCode' => 400,
                'message' => 'WalletNumber is required',
                'data' => null
            ], 400);
        }

        $wallet = $this->walletModel->validateWallet($json['WalletNumber']);
        
        if (!$wallet) {
            return $this->respond([
                'statusCode' => 100,
                'message' => 'INVALID WALLET!',
                'data' => null
            ], 200);
        }

        return $this->respond([
            'statusCode' => 200,
            'message' => 'SUCCESS',
            'data' => [
                'status' => $wallet['status'],
                'name' => $wallet['name']
            ]
        ], 200);
    }
    
    /**
     * Push Transaction Request
     * POST /api/v1/Remittance/push-request-txn
     * Headers: x-api-key, Authorization: Bearer {token}, Content-Type: application/json
     */
    public function pushRequestTxn()
    {
        $json = $this->request->getJSON(true);

        // Validate required top-level fields
        $requiredFields = ['orgId', 'processId', 'billerCode', 'channel', 'signature', 'notificationNo', 'data'];
        foreach ($requiredFields as $field) {
            if (empty($json[$field])) {
                return $this->respond([
                    'statusCode' => 400,
                    'message' => "Field '{$field}' is required",
                    'data' => null
                ], 400);
            }
        }

        // Validate required data fields
        $dataFields = ['referenceId', 'type', 'account', 'amount', 'remarks', 'principalRefId'];
        foreach ($dataFields as $field) {
            if (!isset($json['data'][$field])) {
                return $this->respond([
                    'statusCode' => 400,
                    'message' => "Data field '{$field}' is required",
                    'data' => null
                ], 400);
            }
        }

        // Validate senderInfo fields
        if (empty($json['data']['senderInfo'])) {
            return $this->respond([
                'statusCode' => 400,
                'message' => "Field 'senderInfo' is required",
                'data' => null
            ], 400);
        }

        $senderFields = [
            'senderFirstName', 'senderLastName', 'senderCountryCode', 'senderEmail', 
            'senderMobile', 'senderCurrencyCode', 'senderAddress', 'senderAccountOrCard',
            'senderDOB', 'senderBirthCountry', 'senderIDType', 'senderIDNumber', 'senderAmount'
        ];
        foreach ($senderFields as $field) {
            if (!isset($json['data']['senderInfo'][$field])) {
                return $this->respond([
                    'statusCode' => 400,
                    'message' => "SenderInfo field '{$field}' is required",
                    'data' => null
                ], 400);
            }
        }
        
        // Validate wallet
        $wallet = $this->walletModel->validateWallet($json['data']['account']);
        
        if (!$wallet) {
            return $this->respond([
                'statusCode' => 100,
                'message' => 'FAILED',
                'trxRefNo' => '',
                'data' => null
            ], 200);
        }
        
        if ($wallet['status'] !== 'Active') {
            return $this->respond([
                'statusCode' => 100,
                'message' => 'FAILED - Wallet is not active',
                'trxRefNo' => '',
                'data' => null
            ], 200);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();
            
            // Create transaction with new structure
            $result = $this->transactionModel->createTransaction($json);
            
            if (!$result['success']) {
                $db->transRollback();
                return $this->respond([
                    'statusCode' => 100,
                    'message' => 'FAILED - ' . $result['message'],
                    'trxRefNo' => '',
                    'data' => null
                ], 200);
            }

            $trxRefNo = $result['trx_ref_no'];
            $transactionId = $result['transaction_id'];
            
            // Save sender information to separate table
            if (isset($json['data']['senderInfo'])) {
                $this->senderInfoModel->createSenderInfo($transactionId, $json['data']['senderInfo']);
            }
            
            // Update wallet balance
            $amount = floatval($json['data']['amount']);
            $newBalance = $this->walletModel->updateBalance($json['data']['account'], $amount);
            
            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->respond([
                    'statusCode' => 100,
                    'message' => 'FAILED',
                    'trxRefNo' => '',
                    'data' => null
                ], 200);
            }
            
            // Success response message
            $message = sprintf(
                "An amount of Tk. %.2f Transferred to %s. Your current balance is Tk. %.2f. TxID: %s",
                $amount,
                $json['data']['account'],
                $newBalance,
                $trxRefNo
            );

            return $this->respond([
                'statusCode' => 200,
                'message' => $message,
                'trxRefNo' => $trxRefNo,
                'data' => null
            ], 200);

        } catch (Exception $e) {
            log_message('error', 'Remittance transaction failed: ' . $e->getMessage());
            return $this->respond([
                'statusCode' => 100,
                'message' => 'FAILED',
                'trxRefNo' => '',
                'data' => null
            ], 200);
        }
    }
    
    public function txnEnquiry()
    {
        $json = $this->request->getJSON(true);
        
        if (empty($json['TxnRefId'])) {
            return $this->respond([
                'statusCode' => 400,
                'message' => 'TxnRefId is required',
                'data' => null
            ], 400);
        }

        $transaction = $this->transactionModel->getTransactionByRefId($json['TxnRefId']);
        
        if (!$transaction) {
            return $this->respond([
                'statusCode' => 100,
                'message' => 'No Data Found!',
                'data' => null
            ], 200);
        }

        return $this->respond([
            'statusCode' => 200,
            'message' => 'COMPLETED',
            'data' => [
                'trxRefNo' => $transaction['trx_ref_no'],
                'transactionDate' => date('Y-m-d\TH:i:s.u', strtotime($transaction['transaction_date']))
            ]
        ], 200);
    }
    
    /**
     * Balance Enquiry
     * POST /api/v1/Remittance/BalanceEnquiry
     * Headers: x-api-key, Authorization: Bearer {token}, Content-Type: application/json
     */
    public function balanceEnquiry()
    {
        $json = $this->request->getJSON(true);
        
        // Validate required field
        if (empty($json['accountNo'])) {
            return $this->respond([
                'statusCode' => 400,
                'message' => 'accountNo is required',
                'data' => null
            ], 400);
        }

        $accountNo = $json['accountNo'];

        try {
            // Get wallet details by wallet_number (accountNo)
            $wallet = $this->walletModel->validateWallet($accountNo);
            
            if (!$wallet) {
                return $this->respond([
                    'statusCode' => 100,
                    'message' => 'Account not found!',
                    'data' => null
                ], 200);
            }

            // Check if wallet is active
            if ($wallet['status'] !== 'Active') {
                return $this->respond([
                    'statusCode' => 100,
                    'message' => 'Account is not active!',
                    'data' => null
                ], 200);
            }

            // Return success response with wallet details
            return $this->respond([
                'statusCode' => 200,
                'message' => 'Success',
                'data' => [
                    'accountName' => $wallet['name'],
                    'currency' => $wallet['currency'] ?? 'BDT',
                    'drawableBalance' => number_format($wallet['balance'], 2, '.', '')
                ]
            ], 200);

        } catch (Exception $e) {
            log_message('error', 'Balance Enquiry failed: ' . $e->getMessage());
            return $this->respond([
                'statusCode' => 100,
                'message' => 'Something went wrong!',
                'data' => null
            ], 200);
        }
    }

    /**
     * Get Account Statement
     * POST /api/v1/Remittance/GetAccountStatement
     * Headers: x-api-key, Authorization: Bearer {token}, Content-Type: application/json
     */
    public function getAccountStatement()
    {
        $json = $this->request->getJSON(true);

        // Validate required fields
        if (empty($json['accountNo'])) {
            return $this->respond([
                'statusCode' => 100,
                'message' => 'Field accountNo is required',
                'data' => null
            ], 200);
        }

        if (empty($json['fromDate'])) {
            return $this->respond([
                'statusCode' => 100,
                'message' => 'Field fromDate is required',
                'data' => null
            ], 200);
        }

        if (empty($json['toDate'])) {
            return $this->respond([
                'statusCode' => 100,
                'message' => 'Field toDate is required',
                'data' => null
            ], 200);
        }

        $accountNo = $json['accountNo'];
        $fromDate = $json['fromDate'];
        $toDate = $json['toDate'];

        // Validate date format (dd/MM/yyyy)
        if (!$this->validateDateFormat($fromDate) || !$this->validateDateFormat($toDate)) {
            return $this->respond([
                'statusCode' => 100,
                'message' => 'Invalid date format. Please use dd/MM/yyyy',
                'data' => null
            ], 200);
        }

        try {
            // Convert dates from dd/MM/yyyy to yyyy-MM-dd for database query
            $fromDateDb = \DateTime::createFromFormat('d/m/Y', $fromDate);
            $toDateDb = \DateTime::createFromFormat('d/m/Y', $toDate);

            if (!$fromDateDb || !$toDateDb) {
                return $this->respond([
                    'statusCode' => 100,
                    'message' => 'Invalid date values',
                    'data' => null
                ], 200);
            }

            // Check if fromDate is after toDate
            if ($fromDateDb > $toDateDb) {
                return $this->respond([
                    'statusCode' => 100,
                    'message' => 'fromDate cannot be after toDate',
                    'data' => null
                ], 200);
            }

            // Get wallet details
            $wallet = $this->walletModel->validateWallet($accountNo);
            
            if (!$wallet) {
                return $this->respond([
                    'statusCode' => 100,
                    'message' => 'Account not found!',
                    'data' => null
                ], 200);
            }

            if ($wallet['status'] !== 'Active') {
                return $this->respond([
                    'statusCode' => 100,
                    'message' => 'Account is not active!',
                    'data' => null
                ], 200);
            }

            // Get transactions for the date range
            $transactions = $this->transactionModel->getAccountStatement(
                $wallet['id'],
                $fromDateDb->format('Y-m-d'),
                $toDateDb->format('Y-m-d')
            );

            // Format response data
            $statementData = [];
            foreach ($transactions as $txn) {
                $statementData[] = [
                    'acctName' => $wallet['name'],
                    'traceNo' => $txn['trx_ref_no'] ?? '0',
                    'trnDate' => date('d/m/Y', strtotime($txn['transaction_date'])),
                    'valueDate' => date('d/m/Y', strtotime($txn['transaction_date'])),
                    'transactionType' => 'Credit',
                    'amount' => number_format($txn['amount'], 2, '.', ''),
                    'balance' => number_format($txn['running_balance'], 2, '.', ''),
                    'particulars' => $txn['remarks'] ?? 'Remittance Credit',
                    'currCode' => $wallet['currency'] ?? 'BDT',
                    'acctNumber' => $accountNo
                ];
            }

            return $this->respond([
                'statusCode' => 200,
                'message' => 'Success',
                'data' => $statementData
            ], 200);

        } catch (Exception $e) {
            log_message('error', 'Get Account Statement failed: ' . $e->getMessage());
            return $this->respond([
                'statusCode' => 100,
                'message' => 'Something went wrong!',
                'data' => null
            ], 200);
        }
    }

    /**
     * Validate date format dd/MM/yyyy
     */
    private function validateDateFormat($date)
    {
        $d = \DateTime::createFromFormat('d/m/Y', $date);
        return $d && $d->format('d/m/Y') === $date;
    }
}
