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
                'statusCode' => 100,
                'message' => 'Credentials not matched!',
                'data' => null
            ], 100);
        }

        if (!password_verify($json['password'], $user['password'])) {
            return $this->respond([
                'statusCode' => 100,
                'message' => 'Credentials not matched!',
                'data' => null
            ], 100);
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

        // Store wallet_number in the token for future use
        $authHeader = $this->request->getHeaderLine('Authorization');
        if (!empty($authHeader)) {
            $token = str_replace('Bearer ', '', $authHeader);
            $this->tokenModel->updateWalletNumber($token, $json['WalletNumber']);
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
     * 
     * Request Parameters:
     * - orgId: string(30) [Required] - Organization ID (Provided by TAP)
     * - processId: string(50) [Required] - Transaction Request ID (unique Id)
     * - billerCode: string(50) [Required] - Payee Organization (Provided by TAP)
     * - channel: string(50) [Required] - Channel (Provided by TAP)
     * - signature: string [Optional] - Request signature
     * - notificationNo: string [Optional] - Notification number
     * - data: Object [Required] - Request Data Object
     * 
     * Data Object Parameters:
     * - referenceId: string(50) [Required] - Transaction Unique ID. Alphanumeric, no special characters
     * - type: string [Required] - PRINCIPAL / INCENTIVE
     * - account: string [Required] - Wallet/account number to credit
     * - amount: Decimal(15,2) [Required] - Transaction amount
     * - remarks: string(64) [Required] - Remarks or description for transaction
     * - principalRefId: string [Optional] - Principal Ref. Id for tracking
     * - senderInfo: Object [Required] - Sender information object
     * 
     * SenderInfo Object Parameters:
     * - senderFirstName: string [Required] - Sender first name
     * - senderLastName: string [Required] - Sender last name
     * - senderCountryCode: string [Required] - Sender country code
     * - senderEmail: string [Required] - Sender email address
     * - senderMobile: string [Required] - Sender mobile number
     * - senderCurrencyCode: string [Required] - Sender currency code
     * - senderAddress: string [Required] - Sender address
     * - senderAccountOrCard: string [Required] - Sender account or card number
     * - senderDOB: DateTime [Optional] - Sender date of birth
     * - senderBirthCountry: string [Required] - Sender birth country
     * - senderIDType: int [Optional] - Sender ID type code
     * - senderIDNumber: string [Required] - Sender ID number
     * - senderAmount: decimal [Optional] - Sender transaction amount
     */
    public function pushRequestTxn()
    {
        $json = $this->request->getJSON(true);

        // Validate required top-level fields
        $requiredTopFields = [
            'orgId' => ['type' => 'string', 'maxLength' => 30],
            'processId' => ['type' => 'string', 'maxLength' => 50],
            'billerCode' => ['type' => 'string', 'maxLength' => 50],
            'channel' => ['type' => 'string', 'maxLength' => 50],
            'data' => ['type' => 'object']
        ];

        foreach ($requiredTopFields as $field => $rules) {
            if (empty($json[$field])) {
                return $this->respond([
                    'statusCode' => 400,
                    'message' => "Field '{$field}' is required",
                    'data' => null
                ], 400);
            }

            // Validate string length if applicable
            if ($rules['type'] === 'string' && isset($rules['maxLength'])) {
                if (strlen($json[$field]) > $rules['maxLength']) {
                    return $this->respond([
                        'statusCode' => 400,
                        'message' => "Field '{$field}' exceeds maximum length of {$rules['maxLength']} characters",
                        'data' => null
                    ], 400);
                }
            }

            // Validate object type
            if ($rules['type'] === 'object' && !is_array($json[$field])) {
                return $this->respond([
                    'statusCode' => 400,
                    'message' => "Field '{$field}' must be an object",
                    'data' => null
                ], 400);
            }
        }

        // Validate required data fields
        $requiredDataFields = [
            'referenceId' => ['type' => 'string', 'maxLength' => 50],
            'type' => ['type' => 'string'],
            'account' => ['type' => 'string'],
            'amount' => ['type' => 'decimal'],
            'remarks' => ['type' => 'string', 'maxLength' => 64],
            'senderInfo' => ['type' => 'object']
        ];

        foreach ($requiredDataFields as $field => $rules) {
            if (!isset($json['data'][$field]) || (is_string($json['data'][$field]) && trim($json['data'][$field]) === '')) {
                return $this->respond([
                    'statusCode' => 400,
                    'message' => "'{$field}' is required",
                    'data' => null
                ], 400);
            }

            // Validate string length
            if ($rules['type'] === 'string' && isset($rules['maxLength'])) {
                if (strlen($json['data'][$field]) > $rules['maxLength']) {
                    return $this->respond([
                        'statusCode' => 400,
                        'message' => "'{$field}' exceeds maximum length of {$rules['maxLength']} characters",
                        'data' => null
                    ], 400);
                }
            }

            // Validate decimal format for amount
            if ($rules['type'] === 'decimal' && $field === 'amount') {
                if (!is_numeric($json['data'][$field]) || floatval($json['data'][$field]) <= 0) {
                    return $this->respond([
                        'statusCode' => 400,
                        'message' => "amount must be a valid positive decimal number",
                        'data' => null
                    ], 400);
                }
            }

            // Validate type values
            if ($field === 'type') {
                $validTypes = ['PRINCIPAL', 'INCENTIVE'];
                if (!in_array(strtoupper($json['data'][$field]), $validTypes)) {
                    return $this->respond([
                        'statusCode' => 400,
                        'message' => "type must be either PRINCIPAL or INCENTIVE",
                        'data' => null
                    ], 400);
                }
            }

            // Validate referenceId format (alphanumeric, no special characters)
            if ($field === 'referenceId') {
                if (!preg_match('/^[a-zA-Z0-9]+$/', $json['data'][$field])) {
                    return $this->respond([
                        'statusCode' => 400,
                        'message' => "referenceId must be alphanumeric with no special characters",
                        'data' => null
                    ], 400);
                }
            }
        }

        // Validate senderInfo required fields
        $requiredSenderFields = [
            'senderFirstName' => ['type' => 'string', 'required' => true],
            'senderLastName' => ['type' => 'string', 'required' => true],
            'senderCountryCode' => ['type' => 'string', 'required' => true],
            'senderEmail' => ['type' => 'string', 'required' => true],
            'senderMobile' => ['type' => 'string', 'required' => true],
            'senderCurrencyCode' => ['type' => 'string', 'required' => true],
            'senderAddress' => ['type' => 'string', 'required' => true],
            'senderAccountOrCard' => ['type' => 'string', 'required' => true],
            'senderDOB' => ['type' => 'DateTime', 'required' => false],
            'senderBirthCountry' => ['type' => 'string', 'required' => true],
            'senderIDType' => ['type' => 'int', 'required' => false],
            'senderIDNumber' => ['type' => 'string', 'required' => true],
            'senderAmount' => ['type' => 'decimal', 'required' => false]
        ];

        foreach ($requiredSenderFields as $field => $rules) {
            // Check if field is required
            if ($rules['required']) {
                if (!isset($json['data']['senderInfo'][$field]) || trim($json['data']['senderInfo'][$field]) === '') {
                    return $this->respond([
                        'statusCode' => 400,
                        'message' => "'{$field}' is required",
                        'data' => null
                    ], 400);
                }
            }

            // Validate data type if field is provided
            if (isset($json['data']['senderInfo'][$field]) && !empty($json['data']['senderInfo'][$field])) {
                switch ($rules['type']) {
                    case 'string':
                        if (!is_string($json['data']['senderInfo'][$field])) {
                            return $this->respond([
                                'statusCode' => 400,
                                'message' => "'{$field}' must be a string",
                                'data' => null
                            ], 400);
                        }
                        break;

                    case 'int':
                        if (!is_numeric($json['data']['senderInfo'][$field]) || 
                            !ctype_digit(strval($json['data']['senderInfo'][$field]))) {
                            return $this->respond([
                                'statusCode' => 400,
                                'message' => "'{$field}' must be an integer",
                                'data' => null
                            ], 400);
                        }
                        break;

                    case 'decimal':
                        if (!is_numeric($json['data']['senderInfo'][$field])) {
                            return $this->respond([
                                'statusCode' => 400,
                                'message' => "'{$field}' must be a valid decimal number",
                                'data' => null
                            ], 400);
                        }
                        break;

                    case 'DateTime':
                        // Validate DateTime format
                        // Supports: ISO 8601 with milliseconds (2026-02-22T09:43:08.190Z)
                        // and other common formats
                        $dateFormats = [
                            'Y-m-d\TH:i:s.v\Z',  // ISO 8601 with milliseconds: 2026-02-22T09:43:08.190Z
                            'Y-m-d\TH:i:s\Z',    // ISO 8601: 2026-02-22T09:43:08Z
                            'Y-m-d H:i:s',       // MySQL datetime: 2026-02-22 09:43:08
                            'Y-m-d\TH:i:s',      // ISO 8601 without Z: 2026-02-22T09:43:08
                            'Y-m-d',             // Date only: 2026-02-22
                            'd/m/Y',             // UK format: 22/02/2026
                            'd-m-Y'              // Dash format: 22-02-2026
                        ];
                        $isValidDate = false;
                        
                        // Try to parse with each format
                        foreach ($dateFormats as $format) {
                            $date = \DateTime::createFromFormat($format, $json['data']['senderInfo'][$field]);
                            if ($date !== false) {
                                $isValidDate = true;
                                break;
                            }
                        }
                        
                        // If standard formats fail, try strtotime as fallback
                        if (!$isValidDate) {
                            $timestamp = strtotime($json['data']['senderInfo'][$field]);
                            if ($timestamp !== false) {
                                $isValidDate = true;
                            }
                        }
                        
                        if (!$isValidDate) {
                            return $this->respond([
                                'statusCode' => 400,
                                'message' => " '{$field}' must be a valid DateTime format",
                                'data' => null
                            ], 400);
                        }
                        break;
                }
            }
        }

        // Validate email format (additional validation for senderEmail)
        if (isset($json['data']['senderInfo']['senderEmail']) && 
            !filter_var($json['data']['senderInfo']['senderEmail'], FILTER_VALIDATE_EMAIL)) {
            return $this->respond([
                'statusCode' => 400,
                'message' => "'senderEmail' must be a valid email address",
                'data' => null
            ], 400);
        }
        
        // Validate wallet
        $wallet = $this->walletModel->validateWallet($json['data']['account']);
        
        if (!$wallet) {
            return $this->respond([
                'statusCode' => 100,
                'message' => 'FAILED - Invalid wallet/account number',
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
            ], 100);
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
                ], 100);
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
                'statusCode' => 400,
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
     * Request body (optional): {"accountNo": "880171064443"}
     * If accountNo not provided, uses wallet_number from validateUser
     */
    public function balanceEnquiry()
    {
        try {
            $json = $this->request->getJSON(true);
            
            // Get wallet_number from token (set by validateUser endpoint)
            $tokenWalletNumber = $this->request->walletNumber ?? null;
            
            // Check if accountNo is provided in request body
            $walletNumber = null;
            if (!empty($json['accountNo'])) {
                // If accountNo is provided, it must match the validated wallet number
                if (!$tokenWalletNumber) {
                    return $this->respond([
                        'statusCode' => 400,
                        'message' => 'Please call ValidateUser endpoint first',
                        'data' => null
                    ], 400);
                }
                
                // Verify that accountNo matches the validated wallet number
                if ($json['accountNo'] !== $tokenWalletNumber) {
                    return $this->respond([
                        'statusCode' => 400,
                        'message' => 'Account number does not match validated wallet',
                        'data' => null
                    ], 400);
                }
                
                $walletNumber = $json['accountNo'];
            } else {
                // Use wallet from token if no accountNo provided
                if (!$tokenWalletNumber) {
                    return $this->respond([
                        'statusCode' => 400,
                        'message' => 'Please provide accountNo in request body or call ValidateUser endpoint first',
                        'data' => null
                    ], 400);
                }
                $walletNumber = $tokenWalletNumber;
            }

            // Get wallet details using validateWallet
            $wallet = $this->walletModel->validateWallet($walletNumber);
            
            if (!$wallet) {
                return $this->respond([
                    'statusCode' => 400,
                    'message' => 'Account not found!',
                    'data' => null
                ], 400);
            }

            // Check if wallet is active
            if ($wallet['status'] !== 'Active') {
                return $this->respond([
                    'statusCode' => 400,
                    'message' => 'Account is not active!',
                    'data' => null
                ], 400);
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
                'statusCode' => 400,
                'message' => 'Something went wrong!',
                'data' => null
            ], 400);
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
                'statusCode' => 400,
                'message' => 'Field accountNo is required',
                'data' => null
            ], 400);
        }

        if (empty($json['fromDate'])) {
            return $this->respond([
                'statusCode' => 400,
                'message' => 'Field fromDate is required',
                'data' => null
            ], 400);
        }

        if (empty($json['toDate'])) {
            return $this->respond([
                'statusCode' => 400,
                'message' => 'Field toDate is required',
                'data' => null
            ], 400);
        }

        // Get wallet_number from token (set by validateUser endpoint)
        $tokenWalletNumber = $this->request->walletNumber ?? null;
        
        // Validate that accountNo matches the validated wallet number
        if (!$tokenWalletNumber) {
            return $this->respond([
                'statusCode' => 400,
                'message' => 'Please call ValidateUser endpoint first',
                'data' => null
            ], 400);
        }
        
        // Verify that accountNo matches the validated wallet number
        if ($json['accountNo'] !== $tokenWalletNumber) {
            return $this->respond([
                'statusCode' => 400,
                'message' => 'Account number does not match validated wallet',
                'data' => null
            ], 400);
        }

        $accountNo = $json['accountNo'];
        $fromDate = $json['fromDate'];
        $toDate = $json['toDate'];

        // Validate date format (dd/MM/yyyy)
        if (!$this->validateDateFormat($fromDate) || !$this->validateDateFormat($toDate)) {
            return $this->respond([
                'statusCode' => 400,
                'message' => 'Invalid date format. Please use dd/MM/yyyy',
                'data' => null
            ], 400);
        }

        try {
            // Convert dates from dd/MM/yyyy to yyyy-MM-dd for database query
            $fromDateDb = \DateTime::createFromFormat('d/m/Y', $fromDate);
            $toDateDb = \DateTime::createFromFormat('d/m/Y', $toDate);

            if (!$fromDateDb || !$toDateDb) {
                return $this->respond([
                    'statusCode' => 400,
                    'message' => 'Invalid date values',
                    'data' => null
                ], 400);
            }

            // Check if fromDate is after toDate
            if ($fromDateDb > $toDateDb) {
                return $this->respond([
                    'statusCode' => 400,
                    'message' => 'fromDate cannot be after toDate',
                    'data' => null
                ], 400);
            }

            // Get wallet details
            $wallet = $this->walletModel->validateWallet($accountNo);
            
            if (!$wallet) {
                return $this->respond([
                    'statusCode' => 400,
                    'message' => 'Account not found!',
                    'data' => null
                ], 400);
            }

            if ($wallet['status'] !== 'Active') {
                return $this->respond([
                    'statusCode' => 400,
                    'message' => 'Account is not active!',
                    'data' => null
                ], 400);
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
                'statusCode' => 400,
                'message' => 'Something went wrong!',
                'data' => null
            ], 400);
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
