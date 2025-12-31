<?php 
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UsersModel;

class Auth extends BaseController
{
    public function login()
    {
        $username = $this->request->getJSON(true)['email'] ?? '';
        $password = $this->request->getJSON(true)['password'] ?? '';

        if (empty($username) || empty($password)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Email and password are required.',
            ]);
        }

        $usersModel = new UsersModel();
        $user = $usersModel->where('email', $username)->first();

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid email or password.',
            ]);
        }

        if (!password_verify($password, $user['password'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid email or password.',
            ]);
        }

        if ($user['status'] !== 'active') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Your account is inactive.',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Login successful.',
            'data' => [
                'name' => $user['name'],
                'user_type' => $user['user_type'], // 'admin' or 'user'
                'id' => $user['user_id'] ?? null,

                'email' => $user['email'] ?? null,
                'phone' => $user['phone'] ?? null,
                'status' => $user['status'] ?? null,
                'wallet' => $user['wallet'] ?? null,
            ],
        ]);
    }

    public function dashboardData()
    {
        $input = $this->request->getJSON(true);
        $userId = $input['user_id'] ?? null;

        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User ID is required.',
            ]);
        }

        $usersModel = new \App\Models\UsersModel();
        $user = $usersModel->where('user_id', $userId)->first();

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not found.',
            ]);
        }

        // Get Wallet Balance (optional: sum from wallet table if needed)
        $walletAmount = $user['wallet'] ?? 0;

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Dashboard data fetched successfully.',
            'data' => [
                'merchant_id'    => $user['user_id'],
                'merchant_name'  => $user['name'],
                'mobile_number'  => $user['phone'],
                'email'          => $user['email'],
                'wallet_balance' => $walletAmount,
            ],
        ]);
    }

    public function walletReport()
    {
        $input = $this->request->getJSON(true);
        $merchantId = $input['merchant_id'] ?? null;

        if (!$merchantId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Merchant ID is required.',
            ]);
        }

        $walletModel = new \App\Models\WalletModel();
        $userModel = new \App\Models\UsersModel();

        $merchant = $userModel->find($merchantId);
        if (!$merchant) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Merchant not found.',
            ]);
        }

        // ✅ Check if the user is a merchant
        if ($merchant['user_type'] !== 'Merchant') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Access denied. User is not a merchant.',
            ]);
        }

        // Get wallet transactions for this merchant
        $transactions = $walletModel
            ->select('wallet.amount, wallet.transaction_type, wallet.created_at,
                    c.name AS customer_name, c.phone AS customer_phone')
            ->join('users c', 'c.user_id = wallet.customer_id', 'left')
            ->where('wallet.user_id', $merchantId)
            ->orderBy('wallet.created_at', 'DESC')
            ->findAll();

        $data = [];

        foreach ($transactions as $row) {
            $data[] = [
                'customer_name' => $row['customer_name'] ?? 'N/A',
                'mobile_no'     => $row['customer_phone'] ?? 'N/A',
                'amount'        => (float) $row['amount'],
                'type'          => ucfirst($row['transaction_type']),
                'date_time'     => date('Y-m-d H:i:s', strtotime($row['created_at'])),
            ];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Wallet transactions fetched successfully.',
            'data' => $data,
        ]);
    }

    public function RechargeRequest()
    {
        $this->session = \Config\Services::session();
        $input = $this->request->getJSON(true); // Accept JSON body

        $merchantId = $this->session->get('user_id'); // or pass via token/auth
        if (!$merchantId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $merchant = $this->usersModel->find($merchantId);
        if (!$merchant) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Merchant not found.']);
        }

        $superadmin = $this->usersModel->where('user_type', 'superadmin')->first();
        if (!$superadmin) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Superadmin not found.']);
        }
        $superadminId = $superadmin['user_id'];

        // Get POST Data
        $amount          = floatval($input['amount'] ?? 0);
        $transactionDate = $input['transaction_date'] ?? '';
        $mode            = $input['payment_mode'] ?? '';
        $utr_no          = $input['utr_no'] ?? null;
        $cheque_no       = $input['cheque_no'] ?? null;
        $beneficiary     = $input['beneficiary'] ?? null;
        $account_no      = $input['account_no'] ?? null;
        $bank_name       = $input['bank_name'] ?? null;
        $branch          = $input['branch_address'] ?? null;

        if (!$amount || !$transactionDate || !$mode) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Required fields missing.']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Add to WALLET table (DEBIT - Merchant, CREDIT - Superadmin)
        $this->walletModel->insert([
            'user_id'          => $merchantId,
            'amount'           => $amount,
            'transaction_type' => 'Pending',
            'wallet_by'        => 'Pending',
            'customer_id'      => $superadminId,
            'created_at'       => $transactionDate . ' ' . date('H:i:s'),
        ]);

        $this->walletModel->insert([
            'user_id'          => $superadminId,
            'amount'           => $amount,
            'transaction_type' => 'credit',
            'wallet_by'        => 'Merchant Recharge',
            'customer_id'      => $merchantId,
            'created_at'       => $transactionDate . ' ' . date('H:i:s'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Transaction failed.']);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Recharge request submitted successfully. It will be credited within 24 hours.'
        ]);
    }





}





?>