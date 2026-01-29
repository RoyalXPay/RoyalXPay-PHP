<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UsersModel;
use App\Models\AccessTokenModel;
use App\Models\RemittanceWalletModel;
use App\Models\RemittanceTransactionModel;
use Exception;

class RemittanceController extends ResourceController
{
    protected $format = 'json';
    protected $userModel;
    protected $tokenModel;
    protected $walletModel;
    protected $transactionModel;

    public function __construct()
    {
        $this->userModel = new UsersModel();
        $this->tokenModel = new AccessTokenModel();
        $this->walletModel = new RemittanceWalletModel();
        $this->transactionModel = new RemittanceTransactionModel();
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
    
    public function pushRequest()
    {
        $json = $this->request->getJSON(true);

        $requiredFields = ['orgId', 'processId', 'billerCode', 'channel', 'data'];
        foreach ($requiredFields as $field) {
            if (empty($json[$field])) {
                return $this->respond([
                    'statusCode' => 400,
                    'message' => "Field '{$field}' is required",
                    'data' => null
                ], 400);
            }
        }

        $dataFields = ['referenceId', 'type', 'account', 'amount', 'remarks'];
        foreach ($dataFields as $field) {
            if (!isset($json['data'][$field])) {
                return $this->respond([
                    'statusCode' => 400,
                    'message' => "Data field '{$field}' is required",
                    'data' => null
                ], 400);
            }
        }
        
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
            
            $message = sprintf(
                "An amount of Tk. %.2f Transferred to %s. Your current balance is Tk. %.2f. Fees: Received TK. %.2f. Paid TK. %.2f. TxID: %s",
                $amount,
                $json['data']['account'],
                $newBalance,
                $amount,
                $amount,
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
}
