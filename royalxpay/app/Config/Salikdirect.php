<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\Api\TransactionModel;
use Dompdf\Dompdf;
class Salikdirect extends BaseController
{
    use ResponseTrait;

   
    private $username = 'DBAPSP';
    private $password = 'y08yhFn1LC';
    private $tokenUrl = 'https://qty.mbme.org:8080/v2/mbme/oauth/token';
    private $paymentUrl = 'https://qty.mbme.org:8080/v2/api/payment';

    public function index()
    {
        return view('admin/Api/salikdirect/index', [
            'pagetitle' => 'Salik Direct Payment'
        ]);
    }

      public function getToken()
    {
        $client = \Config\Services::curlrequest();
        $postData = http_build_query([
            'grant_type' => 'password',
            'username'   => $this->username,
            'password'   => $this->password
        ]);

        try {
            $response = $client->post($this->tokenUrl, [
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'body'    => $postData
            ]);

            $result = json_decode($response->getBody(), true);
            $token = $result['access_token'] ?? $result['accessToken'] ?? null;

            return $token
                ? $this->respond(['token' => $token])
                : $this->fail('Token not received', 401);

        } catch (\Exception $e) {
            return $this->failServerError('Token generation failed: ' . $e->getMessage());
        }
    }

public function receipt($id)
{
    $transactionModel = new \App\Models\Api\TransactionModel();
    $transaction = $transactionModel->find($id);

    if (!$transaction) {
        return redirect()->back()->with('error', 'Transaction not found.');
    }

    // Load PDF view with transaction details
    $html = view('admin/Api/salikdirect/receipt_pdf', ['txn' => $transaction]);

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Output as PDF
    $dompdf->stream('Salikdirect_Receipt_' . $transaction['transaction_id'] . '.pdf', ['Attachment' => true]);
}
   
  public function makePayment()
{
    $this->session = \Config\Services::session();
    $input = $this->request->getJSON(true);

    $token = $input['token'] ?? '';
    $account = trim($input['account_number'] ?? '');
    $pin = trim($input['account_pin'] ?? '');
    $amountRaw = $input['paidAmount'] ?? '0';
    $amount = floatval(preg_replace('/[^0-9.]/', '', $amountRaw));
    $providerTransactionId = $input['provider_transaction_id'] ?? '';
    $transactionId = $input['transaction_id'] ?? '';
    $customerName = $input['customer_name'] ?? '';
    $customerMobile = $input['customer_mobile'] ?? '';
    $userId = $this->session->get('user_id');

        $name = $userId ?? '';
    // 🔐 Input Validation
    if (strlen($account) < 4 || strlen($account) > 10) {
        return $this->respond(['status' => 'error', 'message' => 'Account number must be between 4 and 10 characters long.']);
    }

    if (strlen($pin) < 4 || strlen($pin) > 10) {
        return $this->respond(['status' => 'error', 'message' => 'Account PIN must be between 4 and 10 characters long.']);
    }

     $usersModel = new \App\Models\UsersModel();
        $walletModel = new \App\Models\WalletModel(); 

          // Step 0: Check user wallet balance
        $user = $usersModel->find($userId);
        if (!$user) {
            return $this->respond(['error' => 'User not found.'], 404);
        }

    try {
        $client = \Config\Services::curlrequest();

        $paymentData = [
            'transactionId' => $transactionId,
            'merchantId' => '3684', // your actual merchant ID
            'merchantLocation' => 'DXB',
            'serviceId' => '21',
            'method' => 'pay',
            'paidAmount' => (string) number_format($amount, 2, '.', ''),           
            'reqField1' => $account,
            'reqField2' => $pin,
            'reqField3' => $providerTransactionId,
            'customerName' => $customerName,
            'customerMobile' => $customerMobile,
        ];

        // 📦 Log request for debugging
        log_message('debug', 'SALIK Payment Request: ' . json_encode($paymentData));

        $paymentResponse = $client->post('https://qty.mbme.org:8080/v2/api/payment', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json'
            ],
            'json' => $paymentData,
        ]);

        $responseBody = $paymentResponse->getBody();

        // 🧾 Log response
        log_message('debug', 'SALIK Payment Raw Response: ' . $responseBody);

        $responseData = json_decode($responseBody, true);

         $model = new TransactionModel();
            $model->insert([
                'transaction_id' => $transactionId,
                'account_number' => $account,
                'amount'         => $amount,
                'customer_name'  => $name,
                'customer_mobile' => $customerMobile,
                'status'         => $responseData['status'] ?? 'FAILED',
                'Payment_type'   => 'Salikdirect',
                'response'       => json_encode($responseData)
            ]);

              // STEP 4: Deduct wallet and update user
        $remainingWallet = $user['wallet'] - $amount;
        $usersModel->update($userId, ['wallet' => $remainingWallet]);

        // STEP 5: Log to wallet_details table
        $walletModel->insert([
            'user_id'          => $userId,
            'amount'           => $amount,
            'transaction_type' => 'debit',
            'Payment_type'   => 'Salikdirect',
            'created_at'       => date('Y-m-d H:i:s')
        ]);

        // Update wallet session value
        $this->session->set('wallet', $remainingWallet);


        if (isset($responseData['status']) && $responseData['status'] == 'SUCCESS') {
            return $this->respond([
                'status' => 'success',
                'message' => 'Payment successful.',
                'data' => $responseData
            ]);
        } else {
            return $this->respond([
                'status' => 'error',
                'message' => $responseData['message'] ?? 'Payment failed.',
                'response' => $responseData
            ]);
        }

    }  catch (\Exception $e) {
    if (method_exists($e, 'getResponse')) {
        $errorResponse = (string) $e->getResponse()->getBody();
        log_message('error', 'SALIK Payment Error Response Body: ' . $errorResponse);
    }

    return $this->respond([
        'status' => 'error',
        'message' => 'Transaction failed. Please try again.',
        'error' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => $e->getFile()
    ], 500);
}
}



    public function fetchBill()
    {
        $input = $this->request->getJSON(true);
        $token = $input['token'] ?? '';
        $account = $input['account_number'] ?? '';
         $accountPin    = $input['account_pin'] ?? ''; // ✅ add this
        $transactionId = $input['transaction_id'] ?? '';

        if (!$token || !$account || !$transactionId || !$accountPin) {
        return $this->failValidationErrors('Missing required fields.');
        }

        $client = \Config\Services::curlrequest();
        
        $balanceData = [
            'transactionId'    => $transactionId,
            'merchantId'       => '3684',
            'merchantLocation' => 'Your Location',
            'serviceId'        => '21', // ✅ Use 21 instead of 32
            'method'           => 'balance',
            'lang'             => 'en',
            'reqField1'        => trim($account),
            'reqField2'        => trim($accountPin)
        ];


        try {
            $response = $client->post($this->paymentUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json'
                ],
                'body' => json_encode($balanceData),
                'http_errors' => false
            ]);

            $result = json_decode($response->getBody(), true);
            return $this->respond($result);

        } catch (\Exception $e) {
            return $this->failServerError('Balance fetch failed: ' . $e->getMessage());
        }
    }

     public function fetchBillFromPostman()
    {
        $input = $this->request->getJSON(true);
         $account = $input['account_number'] ?? '';
         $accountPin    = $input['account_pin'] ?? ''; // ✅ add this

        if (!$account) {
            return $this->failValidationErrors('Account number is required.');
        }

        // Generate transaction ID
        $transactionId = date('ymdHis') . rand(1000, 9999) . '3684';

        // Generate token
        $client = \Config\Services::curlrequest();
        $postData = http_build_query([
            'grant_type' => 'password',
            'username'   => $this->username,
            'password'   => $this->password
        ]);

        try {
            $tokenRes = $client->post($this->tokenUrl, [
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'body'    => $postData
            ]);
            $tokenData = json_decode($tokenRes->getBody(), true);
            $token = $tokenData['access_token'] ?? $tokenData['accessToken'] ?? null;

            if (!$token) {
                return $this->fail('Token generation failed.');
            }

            // Fetch bill
            $balanceData = [
                'transactionId'    => $transactionId,
                'merchantId'       => '3684',
                'merchantLocation' => 'Your Location',
                'serviceId'        => '21', // ✅ Use 21 instead of 32
                'method'           => 'balance',
                'lang'             => 'en',
                'reqField1'        => trim($account),
                'reqField2'        => trim($accountPin)
            ];

            $response = $client->post($this->paymentUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json'
                ],
                'body' => json_encode($balanceData),
                'http_errors' => false
            ]);

            $result = json_decode($response->getBody(), true);
            return $this->respond($result);

        } catch (\Exception $e) {
            return $this->failServerError('Failed: ' . $e->getMessage());
        }
    }

    public function makePaymentFromPostman()
    {
        $input = $this->request->getJSON(true);
         $account = trim($input['account_number'] ?? '');
         $pin = trim($input['account_pin'] ?? '');
        $amountRaw = $input['amount'] ?? '';
        $customerMobile = $input['customer_mobile'] ?? '';
         $userId = $input['merchant_id'] ?? '';
         $name = $userId ?? '';
         $customerName = $userId ?? '';

         $usersModel = new \App\Models\UsersModel();
        $walletModel = new \App\Models\WalletModel(); 

          // Step 0: Check user wallet balance
        $user = $usersModel->find($userId);


        $amount = floatval(preg_replace('/[^0-9.]/', '', $amountRaw));

        if (!$account || !$amount) {
            return $this->failValidationErrors('Account number and amount are required.');
        }

        $transactionId = date('ymdHis') . rand(1000, 9999) . '3684';

        // Generate token
        $client = \Config\Services::curlrequest();
        $postData = http_build_query([
            'grant_type' => 'password',
            'username'   => $this->username,
            'password'   => $this->password
        ]);

        try {
            $tokenRes = $client->post($this->tokenUrl, [
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'body'    => $postData
            ]);
            $tokenData = json_decode($tokenRes->getBody(), true);
            $token = $tokenData['access_token'] ?? $tokenData['accessToken'] ?? null;

            if (!$token) {
                return $this->fail('Token generation failed.');
            }

            // Fetch balance first (recommended before payment)
            $balanceRes = $client->post($this->paymentUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json'
                ],
                'body' => json_encode([
                    'transactionId'    => $transactionId,
                    'merchantId'       => '3684',
                  
                    'merchantLocation' => 'Your Location',
                    'serviceId'        => '21', // ✅ Use 21 instead of 32
                    'method'           => 'balance',
                    'lang'             => 'en',
                    'reqField1'        => trim($account),
                    'reqField2'        => trim($pin)
                ]),
                'http_errors' => false
            ]);
            $balance = json_decode($balanceRes->getBody(), true);
            
            $providerTransactionId = $balance['providerTransactionId'] ?? $balance['provider_transaction_id'] ?? '';

            if (($balance['responseCode'] ?? '') !== '000') {
                return $this->respond(['error' => 'Balance fetch failed', 'details' => $balance], 400);
            }

            // Now perform payment
            $paymentData = [
                'transactionId'    => $transactionId,
                'merchantId'       => '3684',
                'merchantLocation' => 'DBAPSP',
                'method'           => 'pay',
                'serviceId'        => '21',
                'lang'             => 'en',
                'paymentMode'      => 'Cash',
                'paidAmount' => (string) number_format($amount, 2, '.', ''),           
                'reqField1'        => $account,
                 'reqField2' => $pin,
                'reqField3' => $providerTransactionId,
                'customerName' => $customerName,
                'customerMobile' => $customerMobile
            ];

            $payRes = $client->post($this->paymentUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json'
                ],
                'body' => json_encode($paymentData),
                'http_errors' => false
            ]);

            $result = json_decode($payRes->getBody(), true);

            $model = new TransactionModel();
            $model->insert([
                'transaction_id' => $transactionId,
                'account_number' => $account,
                'amount'         => $amount,
                'customer_name'  => $name,
                'customer_mobile' => $customerMobile,
                'status'         => $result['status'] ?? 'FAILED',
                'Payment_type'   => 'Salikdirect',
                'response'       => json_encode($result)
            ]);

             // STEP 4: Deduct wallet and update user
        $remainingWallet = $user['wallet'] - $amount;
        $usersModel->update($userId, ['wallet' => $remainingWallet]);

        // STEP 5: Log to wallet_details table
        $walletModel->insert([
            'user_id'          => $userId,
            'amount'           => $amount,
            'transaction_type' => 'debit',
            'Payment_type'   => 'Salikdirect',
            'created_at'       => date('Y-m-d H:i:s')
        ]);


            return $this->respond($result);

        } catch (\Exception $e) {
            return $this->failServerError('Payment failed: ' . $e->getMessage());
        }
    }

   public function reports()
{
    $this->session = \Config\Services::session();
    $userId = $this->session->get('user_id');
    $model = new \App\Models\Api\TransactionModel();

    $builder = $model->where('customer_name', $userId)
                     ->where('Payment_type', 'Salikdirect');

    // Filtering logic
    $startDate = $this->request->getGet('start_date');
    $endDate   = $this->request->getGet('end_date');
    $search    = $this->request->getGet('search');

    if ($startDate) {
        $builder->where('DATE(created_at) >=', $startDate);
    }
    if ($endDate) {
        $builder->where('DATE(created_at) <=', $endDate);
    }
    if ($search) {
        $builder->groupStart()
            ->like('account_number', $search)
            ->orLike('transaction_id', $search)
            ->groupEnd();
    }

    $transactions = $builder->orderBy('created_at', 'DESC')->findAll();

    return view('admin/Api/salikdirect/report', [
        'transactions' => $transactions,
        'pagetitle' => 'Salik Direct Payment Reports'
    ]);
}


}
