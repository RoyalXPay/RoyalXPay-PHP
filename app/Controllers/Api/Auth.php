<?php 
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use App\Libraries\EmailService;
use App\Models\CustomerModel;

use App\Models\WalletModel; // add this at the top if not already


class Auth extends BaseController
{
    public function login()
    {
        $requestData = $this->request->getJSON(true);
        $email = $requestData['email'] ?? '';
        $agree = true ;

        if (empty($email)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Email is required.'
            ]);
        }

        if (!$agree) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You must agree to the Terms & Conditions and Privacy Policy.'
            ]);
        }

        $usersModel = new UsersModel();
        $user = $usersModel->where('email', $email)->first();

        if (!$user) {
            // Create new user
            $userId = $usersModel->insert([
                'email'      => $email,
                'status'     => 'active',
                'user_type'  => 'Merchant',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $user = $usersModel->find($userId);
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        $otpExpiry = date('Y-m-d H:i:s', time() + 300); // 5 minutes

        $usersModel->update($user['user_id'], [
            'otp'             => $otp,
            'otp_expire_time' => $otpExpiry
        ]);

        // Send OTP Email
        try {
            $emailService = new EmailService();
            $subject = "Your Login OTP Code";
            $logoUrl = base_url('assets/images/favicon.png'); // your logo path

            // OTP table in email
            $otpTable = '<table align="center" cellpadding="10" cellspacing="10" style="margin:20px auto;"><tr>';
            foreach (str_split($otp) as $digit) {
                $otpTable .= '<td style="border:1px solid #ddd; font-size:24px; font-weight:bold; padding:15px 20px; border-radius:6px; text-align:center;">' . $digit . '</td>';
            }
            $otpTable .= '</tr></table>';

            $message = <<<EOT
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>RoyalXPay OTP Verification</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f7; font-family:Arial, sans-serif;">
  <table align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:20px auto; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
    <tr>
      <td style="padding:15px; text-align:left;">
        <img src="$logoUrl" alt="RoyalXPay" style="height:50px; vertical-align:middle;">
        <span style="font-size:24px; font-weight:bold; color:#000; margin-left:10px; vertical-align:middle;">
          Account Verification
        </span>
      </td>
    </tr>
    <tr>
      <td style="padding:30px;">
        <p style="font-size:16px; color:#000; margin:0 0 20px;">
          Hey,<br>Welcome to RoyalXPay! We're excited to have you on board. <br>
          Your code to access your account is:
        </p>
        $otpTable
        <p style="color:#888; font-size:14px;">This verification code will expire in 5 minutes.</p>
      </td>
    </tr>
    <tr>
      <td style="padding:20px; background:#f9f9f9; font-size:13px; color:#777;">
        If you did not request this code, you can safely ignore this email.<br><br>
        Best regards,<br><strong>Team RoyalXPay</strong>
      </td>
    </tr>
  </table>
</body>
</html>
EOT;

            $emailService->sendEmail($email, $subject, $message);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "OTP has been sent to {$user['email']}.",
                'data'    => [
                    'email'      => $user['email'],
                    'user_id'    => $user['user_id'],
'company_name'    => $user['company_name'],
'logo'    => $user['logo'],
'company_website'    => $user['company_website'],
'contactus'    => $user['contactus'],
                    'otp_expiry' => $otpExpiry,
                    'otp'        => $otp // ⚠️ remove in production
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Failed to send OTP email: ' . $e->getMessage()
            ]);
        }
    }

public function verifyOtp()
{
    $email = $this->request->getJSON(true)['email'] ?? '';
    $otp   = $this->request->getJSON(true)['otp'] ?? '';

    if (empty($email) || empty($otp)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Email and OTP are required.',
        ]);
    }

    $usersModel = new UsersModel();
    $user = $usersModel->where('email', $email)->first();

    if (!$user) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'User not found.',
        ]);
    }

    if (time() > strtotime($user['otp_expire_time'])) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'OTP expired. Please login again.',
        ]);
    }

    if ($user['otp'] != $otp) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid OTP.',
        ]);
    }

    // ✅ OTP valid → clear OTP
    $usersModel->update($user['user_id'], [
        'otp' => null,
        'otp_expire_time' => null,
    ]);

    return $this->response->setJSON([
        'status' => 'success',
        'message' => "OTP verified successfully. Login granted for {$user['email']}.",
        'data' => [
            'id' => $user['user_id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'user_type' => $user['user_type'],
            'wallet' => $user['wallet'],
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
        $this->walletModel = new \App\Models\WalletModel();
        $this->usersModel = new \App\Models\UsersModel();
        $input = $this->request->getJSON(true); // Accept JSON body

        $merchantId = $input['merchant_id'] ?? null; // or pass via token/auth
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

    public function getMerchantNotifications()
    {
        $request = service('request');
        $input = $this->request->getJSON(true);
$merchantId = $input['merchant_id'] ?? null;

        if (empty($merchantId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Merchant ID is required.'
            ]);
        }

        $walletModel = new WalletModel();

        $notifications = $walletModel
            ->where('status', 'pending')
            ->where('user_id', $merchantId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'count' => count($notifications),
            'notifications' => $notifications
        ]);
    }



    
public function customerSendOtp()
{
    $email = $this->request->getJSON(true)['email'] ?? '';

    if (empty($email)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Email is required.'
        ]);
    }

    $customerModel = new CustomerModel();
    $userModel     = new UsersModel();   // Make sure UserModel exists
    $emailService  = new \App\Libraries\EmailService();

    // Generate OTP
   if ($email == 'mobiletest@gmail.com') {
    $otp = 111111; // manual OTP for testing email
} else {
    $otp = rand(100000, 999999);
}
    $otpExpiry = date('Y-m-d H:i:s', time() + 300); // 5 minutes

    // Check if customer exists
    $customer = $customerModel->where('email', $email)->first();


    if ($customer) {
        $customerModel->update($customer['id'], [
            'token' => $otp,
            'status' => 'ACTIVE',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    } else {
        $customerModel->insert([
            'email' => $email,
            'name' => 'New Customer',
            'phone' => '',
            'address' => '',
            'mode_of_payment' => 'Cash',
            'paid_amount' => 0,
            'balance_amount' => 0,
            'items' => '',
            'quantity' => 0,
            'status' => 'ACTIVE',
            'token' => $otp,
            'created_by' => 0,
            'paid_status' => 'UNPAID',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    // ---------- GET COMPANY DETAILS BY EMAIL ----------
    $company = $userModel
        ->select('company_name, logo')
        ->where('email', $email)
        ->first();

    $companyName = $company['company_name'] ?? 'Royal XPay';
    $companyLogo = !empty($company['logo']) 
        ? base_url( $company['logo']) 
        : base_url('uploads/logos/default.png');


    // Send Email
    try {
        $subject = "Your OTP for Customer Verification";
        $message = "
            <p>Dear Customer,</p>
            <p>Your OTP is: <b>{$otp}</b>.</p>
            <p>This OTP will expire in 5 minutes.</p>
            <p>Regards,<br>{$companyName}</p>
        ";

        $emailService->sendEmail($email, $subject, $message);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'OTP sent successfully to ' . $email,
            'data' => [
                'email' => $email,
                'otp_expiry' => $otpExpiry,
                'otp' => $otp,

                // 🔥 New fields
                'company_name' => $companyName,
                'company_logo' => $companyLogo
            ]
        ]);
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to send email: ' . $e->getMessage()
        ]);
    }
}

public function customerVerifyOtp()
{
    $email = $this->request->getJSON(true)['email'] ?? '';
    $otp   = $this->request->getJSON(true)['otp'] ?? '';

    if (empty($email) || empty($otp)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Email and OTP are required.'
        ]);
    }

    $customerModel = new CustomerModel();
    $customer = $customerModel->where('email', $email)->first();

    if (!$customer) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Customer not found.'
        ]);
    }

    if ($customer['token'] != $otp) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid OTP.'
        ]);
    }

    // Clear OTP after verification
    $customerModel->update($customer['id'], ['token' => null]);

    // Send confirmation email
    try {
        $emailService = new \App\Libraries\EmailService();
        $subject = "Royal XPay - Verification Successful";
        $message = "
            <p>Dear {$customer['name']},</p>
            <p>Your OTP has been verified successfully.</p>
            <p>Welcome to Royal XPay!</p>
        ";

        $emailService->sendEmail($email, $subject, $message);
    } catch (\Exception $e) {
        // ignore email send failure
    }

    return $this->response->setJSON([
        'status' => 'success',
        'message' => 'OTP verified successfully.',
        'data' => [
            'id' => $customer['id'],
            'name' => $customer['name'],
            'email' => $customer['email'],
            'phone' => $customer['phone'],
            'status' => $customer['status']
        ]
    ]);
}
}





?>