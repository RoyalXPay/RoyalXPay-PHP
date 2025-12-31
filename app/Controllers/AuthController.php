<?php

namespace App\Controllers;

use DateTime;
use DateTimeZone;
use RuntimeException;
use App\Models\UsersModel;
use App\Libraries\Template;
use App\Libraries\EmailService;
use App\Controllers\BaseController;

class AuthController extends BaseController
{
    protected $session;
    protected $usersModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->session = \Config\Services::session();
    }
public function index()
{
    $isLoggedIn = $this->session->get('logged_in');

    if ($isLoggedIn) {
        return redirect()->to(site_url('admin/dashboard'));
    }

    $data = [
        'first_login_done' => 0,  // default
        'last_email' => ''
    ];

    $lastEmail = $this->session->get('last_login_email'); 
    if ($lastEmail) {
        $userRecord = $this->usersModel->where("email", $lastEmail)->first();
        if ($userRecord) {
            $data['first_login_done'] = $userRecord['first_login_done']; 
            $data['last_email'] = $lastEmail;
        }
    }

    return view('admin/login', $data);
}



     public function verifyLoginold()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Check if username and password are provided
        if (!empty($username) ) {
            // Step 1: Fetch user record from the database based on the email/username
            $userRecord = $this->usersModel->where("email", $username)->first();

            if (!$userRecord) {
                $this->session->setFlashdata('error', 'User not found');
                return redirect()->to(site_url('login'));
            }

              // First time login -> password required
            if ($userRecord['first_login_done'] == 0) {
                if (empty($password)) {
                    $this->session->setFlashdata('error', 'Password is required for first login.');
                    return redirect()->to(site_url('login'));
                }

                if (!password_verify($password, $userRecord["password"])) {
                    $this->session->setFlashdata('error', 'Invalid Password');
                    return redirect()->to(site_url('login'));
                }

                // Mark first login as done
                $this->usersModel->update($userRecord['user_id'], ['first_login_done' => 1]);

                // Step 2: Validate the password
            if (!password_verify($password, $userRecord["password"])) {
                $this->session->setFlashdata('error', 'Invalid Password');
                return redirect()->to(site_url('login'));
            }
            }

            

            // Step 3: Check if the account is active
            if (strtolower($userRecord["status"]) !== "active") {
                $this->session->setFlashdata('error', 'Your account is currently inactive.');
                return redirect()->to(site_url('login'));
            }

       $this->session->set('last_login_email', $username);
            // Step 4: Generate OTP for validation
            $otp = rand(100000, 999999);

            date_default_timezone_set('Asia/Kolkata');
            $otpExpiry = date('Y-m-d H:i:s', time() + 300);

            // Step 5: Save OTP in DB for this user
            $this->usersModel->update($userRecord['user_id'], [
                'otp'             => $otp,
                'otp_expire_time' => $otpExpiry
            ]);

            // Step 6: Send OTP Email using EmailService
            try {
                $emailService = new EmailService();
                $subject = "Your Login OTP Code";
                $message = "Dear User,<br><br>Your OTP code is: <b>{$otp}</b>. 
                            It will expire in 5 minutes.<br><br>Regards,<br>Royal XPay";
                          
                $emailService->sendEmail($username, $subject, $message);
                file_put_contents(
                        WRITEPATH . 'logs/email-log-' . date('Y-m-d') . '.log',
                        date('Y-m-d H:i:s') . " - SUCCESS sending email to: $username\n",
                        FILE_APPEND
                    );
                // Step 7: Redirect to OTP verification page
                return redirect()->to(site_url('verify-otp?email=' . urlencode($username)));
            } catch (\Exception $e) {
                 file_put_contents(
                        WRITEPATH . 'logs/email-log-' . date('Y-m-d') . '.log',
                        date('Y-m-d H:i:s') . " - ERROR sending email to $username: " . $e->getMessage() . "\n",
                        FILE_APPEND
                    );
                // If email sending fails, destroy the session and show error
                session()->destroy();
                $this->session->setFlashdata('error', 'Failed to send OTP email: ' . $e->getMessage());
                return redirect()->to(site_url('login'));
            }
        } else {
            
            $this->session->setFlashdata('error', 'Username and Password are required.');
            return redirect()->to(site_url('login'));
        }
    }

public function verifyLogin()
{
    $email = $this->request->getPost('email');
    $agree = $this->request->getPost('agree_terms');

    // 1. Validate input
    if (empty($email)) {
        $this->session->setFlashdata('error', 'Email is required.');
        return redirect()->to(site_url('login'));
    }

    if (!$agree) {
        $this->session->setFlashdata('error', 'You must agree to the “Terms & Conditions” and “Privacy Policy”.');
        return redirect()->to(site_url('login'))->withInput();
    }

    // 2. Find or create user
    $userRecord = $this->usersModel->where("email", $email)->first();
    if (!$userRecord) {
        // create new user
        $userId = $this->usersModel->insert([
            'email' => $email,
            'status' => 'active',
            'user_type' => 'Merchant',

            'created_at' => date('Y-m-d H:i:s')
        ]);
        $userRecord = $this->usersModel->find($userId);
    }

    if ($email === 'mobiletest@gmail.com') {
    $otp = 111111; // manual OTP for testing email
} else {
    $otp = rand(100000, 999999);
}

            date_default_timezone_set('Asia/Kolkata');
            $otpExpiry = date('Y-m-d H:i:s', time() + 300);

            // Step 5: Save OTP in DB for this user
            $this->usersModel->update($userRecord['user_id'], [
                'otp'             => $otp,
                'otp_expire_time' => $otpExpiry
            ]);

    // 4. Send OTP Email
    try {
        $emailService = new \App\Libraries\EmailService();
        $subject = "Your Login OTP Code";
       $logoUrl = base_url('assets/images/favicon.png'); // your logo path

       // Build OTP HTML separately
$otpTable = '<table align="center" cellpadding="10" cellspacing="10" style="margin:20px auto;"><tr>';
foreach (str_split($otp) as $digit) {
    $otpTable .= '<td style="border:1px solid #ddd; font-size:24px; font-weight:bold; padding:15px 20px; border-radius:6px; text-align:center;">' . $digit . '</td>';
}
$otpTable .= '</tr></table>';

// Now use it in the template
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

        return redirect()->to(site_url('verify-otp?email=' . urlencode($email)));
    } catch (\Exception $e) {
        $this->session->setFlashdata('error', 'Failed to send OTP email.');
        return redirect()->to(site_url('login'));
    }
}




 



    public function otpForm()
    {
        $username = $this->request->getGet('email');
        $data = ['email' => $username];

        return view('admin/verify_otp', $data);
    }

    public function resendOtp()
    {
        $email = $this->request->getGet('email');

        if (!$email) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Session expired. Please login again.'
            ]);
        }

        // Fetch user
        $userRecord = $this->usersModel->where('email', $email)->first();

        if (!$userRecord) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'User not found.'
            ]);
        }

        // Generate new OTP
        $otp = rand(100000, 999999);
        date_default_timezone_set('Asia/Kolkata');
        $otpExpiry = date('Y-m-d H:i:s', time() + 300);

        // Update DB
        $this->usersModel->update($userRecord['user_id'], [
            'otp'             => $otp,
            'otp_expire_time' => $otpExpiry
        ]);

        try {
            $emailService = new EmailService();
            $subject = "Your Login OTP Code (Resent)";
            $message = "Dear User,<br><br>Your new OTP code is: <b>{$otp}</b>. 
                It will expire in 5 minutes.<br><br>Regards,<br>Kahipay";

            $emailService->sendEmail($email, $subject, $message);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'A new OTP has been sent to your email.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Failed to resend OTP: ' . $e->getMessage()
            ]);
        }
    }

    public function verifyOtp()
    {
 set_title('verify otp');
        $session   = session();
        $username  = $this->request->getPost('otp_user');
        $inputOtp  = $this->request->getPost('otp_code');

        // Validate OTP
        if (empty($username)) {
            $this->session->setFlashdata('error', 'Session expired. Please login again.');
            return redirect()->to(site_url('login'));
        }

        // Fetch OTP & expiry from DB
        $user = $this->usersModel
            ->select('otp, otp_expire_time, user_id, name, email, phone, status, user_type')
            ->where('email', $username)
            ->first();

        if (!$user) {
            $this->session->setFlashdata('error', 'User not found. Please login again.');
            return redirect()->to(site_url('login'));
        }

        // Check expiry
        $expiry = new DateTime($user['otp_expire_time'], new DateTimeZone('Asia/Kolkata'));
        if (time() > $expiry->getTimestamp()) {
            $this->session->setFlashdata('error', 'OTP expired. Please login again.');
            return redirect()->to(site_url('login'));
        }

        // Update last login timestamp
        $currentTime = date('Y-m-d H:i:s');
        $user["last_login"] = $currentTime;

        // Validate OTP
        if ($inputOtp == $user['otp']) {
            // Step 1: Mark user as logged in by setting session data
            $sessionData = [
                'user_id'       => $user['user_id'],
                'name'          => $user['name'],
                'email'         => $user['email'],
                'phone'         => $user['phone'],
                'status'        => $user['status'],
                'user_type'     => $user['user_type'],
                'last_login'    => $user['last_login'] ?? null,
                'timezone'      => $user['timezone'] ?? 'UTC',
                'logged_in'     => true,  // User is now logged in
            ];

            // Handle company-specific data
            if (strtolower($user['user_type']) === 'merchant') {
                $usersModel = new usersModel();
                $users = $usersModel->where('user_id', $user['user_id'])->first();

                if ($users) {
                    $sessionData["user_id"] = $users['user_id'];
                    $sessionData["name"] = $users['name'] ?? null;
                }
            }
            

            // Set session data
            $session->set($sessionData);

            // Clear OTP in DB for security
            $this->usersModel->where('email', $username)->update(null, [
                'otp'        => null,
                'otp_expire_time' => null
            ]);

            $session->remove(['otp_user']);

            return redirect()->to(site_url('admin/dashboard'));
        } else {
            $this->session->setFlashdata('error', 'Invalid OTP');
            return redirect()->to(site_url('verify-otp'));
        }
    }


    

    public function profile()
    {
        set_title('Profile | ' . SITE_NAME);

        $data = [];
        $userId = $this->session->get('user_id');
        $data['profiledata'] = $this->usersModel->where("user_id", $userId)->first();

        return view('admin/profile', $data);
    }

    public function updateProfile()
    {

        $userId = $this->session->get('user_id');
        $firstName = $this->request->getPost('first_name');

        $arrSaveData = [
            'first_name' => $firstName
        ];
        $this->usersModel->update($userId, $arrSaveData);

        $this->session->setFlashdata('success', 'Profile updated successfully.');
        return redirect()->to(site_url('admin/profile'));
    }

    public function changePassword()
    {

        $data = [];
        return view('admin/change_password', $data);
    }

    public function updatePassword()
    {

        $userId = $this->session->get('user_id');
        $password = $this->request->getPost('password');

        $arrSaveData = [
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $this->usersModel->update($userId, $arrSaveData);

        $this->session->setFlashdata('success', 'Password updated successfully.');
        return redirect()->to(site_url('admin/change-password'));
    }

    public function logout()
    {
        // Define the session keys to be cleared
        $sessionKeys = ['user_id', 'first_name', 'last_name', 'email', 'phone', 'user_type', 'status', 'logged_in'];
        // Get the session service
        $session = \Config\Services::session();
        // Remove the specified session variables
        foreach ($sessionKeys as $sessionKey) {
            $session->remove($sessionKey);
        }
        // Optionally destroy the session
        // $session->destroy();
        // Redirect to the login page after logout
        return redirect()->to(site_url('login'));
    }

    public function register()
    {
        return view('admin/register');
    }

     // Token generation function
    private function getMbmeTokenDirect()
    {
        $client = \Config\Services::curlrequest();

        $postData = http_build_query([
            'grant_type' => 'password',
            'username'   => 'DBAPSP',
            'password'   => 'y08yhFn1LC'
        ]);

        try {
            $response = $client->post('https://qty.mbme.org:8080/v2/mbme/oauth/token', [
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'body'    => $postData
            ]);

            $result = json_decode($response->getBody(), true);
            return $result['access_token'] ?? $result['accessToken'] ?? null;

        } catch (\Exception $e) {
            log_message('error', 'Token error: ' . $e->getMessage());
            return null;
        }
    }

    // Merchant wallet balance fetch
   private function getMerchantBalanceFromMbme($token, $transactionId)
    {
        $client = \Config\Services::curlrequest();

        $postData = http_build_query([
            'transactionId' => $transactionId
        ]);

        try {
            $response = $client->post('https://qty.mbme.org:8080/v2/mbme/merchantBalance', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/x-www-form-urlencoded'
                ],
                'body' => $postData,
                'http_errors' => false
            ]);

            $result = json_decode($response->getBody(), true);

            // Always return the balance from MBME response
            return floatval($result['balance']);

        } catch (\Exception $e) {
            log_message('error', 'Merchant balance fetch exception: ' . $e->getMessage());

            // Still return 0 if exception occurs (as float)
            return 0.0;
        }
    }



    // Transaction ID generator (UAE time)
    private function generateTransactionId()
    {
        $now = new \DateTime('now', new \DateTimeZone('Asia/Dubai'));

        $YY = $now->format('y');
        $MM = $now->format('m');
        $DD = $now->format('d');
        $HH = $now->format('H');
        $mm = $now->format('i');
        $SS = $now->format('s');
        $MS = substr((string)microtime(true), -2);
        $randomDigit = rand(0, 9);

        return "{$YY}{$MM}{$DD}{$HH}{$mm}{$SS}{$MS}{$randomDigit}3684";
    }

   public function registerSaveold()
{
    if ($this->request->isAJAX()) {
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $repeatPassword = $this->request->getPost('repeat_password');

        // 1. Check if email already exists
        $existingUser = $this->usersModel
            ->where('email', $email)
            ->orWhere('username', $username)
            ->first();

        if ($existingUser) {
            return $this->response->setJSON([
                'status' => 'exists',
                'message' => 'User already exists. Please login.',
                'suggest_email' => $existingUser['email']
            ]);
        }

        // 2. Validate form fields
        $rules = [
            'username' => 'required',
            'name' => 'required',
            'email' => 'required|valid_email',
            'phone' => 'required',
            'password' => 'required|min_length[6]',
            'repeat_password' => 'required|matches[password]',
        ];

        // if (!$this->validate($rules)) {
        //     return $this->response->setJSON([
        //         'status' => 'validation_error',
        //         'message' => $this->validator->listErrors()
        //     ]);
        // }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // 3. Get MBME Wallet Balance
        $walletBalance = 0;
        $transactionId = $this->generateTransactionId();
        $token = $this->getMbmeTokenDirect();

        if ($token && $transactionId) {
            $walletBalance = $this->getMerchantBalanceFromMbme($token, $transactionId);
        }
        // 3. Save new merchant
        $data = [
            'username' => $username,
            'name' => $this->request->getPost('name'),
            'email' => $email,
            'phone' => $this->request->getPost('phone'),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'gender' => $this->request->getPost('gender'),
            'user_type' => "Merchant",
            'status' => 'active',
            'wallet' => $walletBalance,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->usersModel->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Registration successful. You can now login.'
        ]);
    }

    // fallback for non-AJAX
    return redirect()->to(site_url('register'));
}

public function registerSave()
{
    if ($this->request->isAJAX()) {
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $repeatPassword = $this->request->getPost('repeat_password');

        // 1. Check if email or username already exists
        $existingUser = $this->usersModel
            ->where('email', $email)
            ->orWhere('username', $username)
            ->first();

        if ($existingUser) {
            return $this->response->setJSON([
                'status' => 'exists',
                'message' => 'User already exists. Please login.',
                'suggest_email' => $existingUser['email']
            ]);
        }

        // 2. Validate form
        $rules = [
            'username' => 'required',
            'name' => 'required',
            'email' => 'required|valid_email',
            'phone' => 'required',
            'country' => 'required',
            'password' => 'required|min_length[6]',
            'repeat_password' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // 3. Call MBME API to register merchant
        $mbmeResponse = $this->callMbmeRegisterAPI([
            'username' => $username,
            'email' => $email,
            'mobile' => $this->request->getPost('phone'),
             'country' => $this->request->getPost('country'), // ✅ store country
            'name' => $this->request->getPost('name'),
            
            'gender' => $this->request->getPost('gender'),
        ]);
        // Optional: Log MBME response
        log_message('info', 'MBME Register Response: ' . print_r($mbmeResponse, true));

        // 4. Get MBME Wallet Balance
        $walletBalance = 0;
        // $transactionId = $this->generateTransactionId();
        // $token = $this->getMbmeTokenDirect();

        // if ($token && $transactionId) {
        //     $walletBalance = $this->getMerchantBalanceFromMbme($token, $transactionId);
        // }

        // 5. Save new merchant
        $data = [
            'username' => $username,
            'name' => $this->request->getPost('name'),
            'email' => $email,
            'phone' => $this->request->getPost('phone'),
            'gender' => $this->request->getPost('gender'),
             'country' => $this->request->getPost('country'), // ✅ store country
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'user_type' => 'Merchant',
            'status' => 'active',
            'wallet' => $walletBalance,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->usersModel->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Registration successful. You can now login.',
            'mbme_response' => $mbmeResponse // optional: show or log it
        ]);
    }

    // fallback for non-AJAX
    return redirect()->to(site_url('register'));
}
private function callMbmeRegisterAPI($params)
{
    $client = \Config\Services::curlrequest();

    $apiUrl = 'https://qty.mbme.org:8080/v2/mbme/register'; // Replace with actual MBME register API URL

    $payload = [
        'username' => $params['username'],
        'name' => $params['name'],
        'email' => $params['email'],
        'mobile' => $params['mobile'],
        'gender' => $params['gender']
        // Add other required MBME fields
    ];

    try {
        $response = $client->post($apiUrl, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->getMbmeTokenDirect() // if token is needed
            ],
            'json' => $payload,
        ]);

        return json_decode($response->getBody(), true);
    } catch (\Exception $e) {
        log_message('error', 'MBME Register API Error: ' . $e->getMessage());
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}



}
