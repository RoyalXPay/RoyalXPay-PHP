<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\Api\TransactionModel;
use Mpdf\Mpdf;
class Salikdirect extends BaseController
{
    use ResponseTrait;

   
    private $username = 'DBAPSP';
    private $password = 'y08yhFn1LC';
    private $tokenUrl = 'https://qty.mbme.org:8080/v2/mbme/oauth/token';
     private $paymentUrl = 'https://mobportal.mbme.org:11005/mpay/v1/pay';
private $enquiryUrl = 'https://mobportal.mbme.org:11005/mpay/v1/enquiry';
private $hasrequest = 'https://mobportal.mbme.org:11005/mpay/hashRequest';

    public function index()
    {
        return view('admin/Api/salikdirect/index', [
            'pagetitle' => 'Salikdirect Payment'
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
    $userModel = new \App\Models\UsersModel();
    $customerModel = new \App\Models\CustomerModel();

    $transaction = $transactionModel->find($id);
    // Fetch merchant based on customer_name (merchant user_id)
    $merchant = null;
    if (!empty($transaction['customer_name'])) {
        $merchant = $userModel->where('user_id', $transaction['customer_name'])->first();
    }

    // Fetch customer based on created_for (customer_id)
    $customer = null;
    if (!empty($transaction['created_for'])) {
        $customer = $customerModel->where('id', $transaction['created_for'])->first();
    }

    // Fetch customer based on name and mobile (since customer_id doesn't exist in transactions)
    $customer = $customerModel
        ->where('id', $transaction['created_for'])
        ->first();

    try {
        $html = view('admin/Api/salikdirect/receipt_pdf', [
            'txn' => $transaction,
            'merchant' => $merchant,
            'customer' => $customer
        ]);

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="receipt.pdf"')
            ->setBody($mpdf->Output('', 'S'));

    } catch (\Throwable $e) {
        return $this->response->setStatusCode(500)->setBody('PDF generation failed: ' . $e->getMessage());
    }
}

   
public function makePayment()
{
    $this->session = \Config\Services::session();
    $client        = \Config\Services::curlrequest();

    $input = $this->request->getJSON(true);

    $accountNumber     = $input['account_number'] ?? null;
    $accountPin        = $input['account_pin'] ?? null;
    $amount            = $input['amount_without_commission'] ?? null;
    $oidFromEnquiry    = $input['oid'] ?? null;
    $providerTxnId     = $input['provider_transaction_id'] ?? null;
    $customerMobile    = $input['customer_mobile'] ?? null;

     $userId = $this->session->get('user_id');

    // ✅ Load user model
    $userModel = new \App\Models\UsersModel();
    $user      = $userModel->find($userId);

    if (empty($user) || empty($user['merchant_uid']) || empty($user['merchant_token'])) {
        return $this->respond([
            'status'  => false,
            'message' => 'Please register in merchant account.'
        ], 400);
    }

    // ✅ Assign merchant credentials
    $merchant_uid   = $user['merchant_uid'];
    $merchant_token = $user['merchant_token'];
    $name   = $userId ?? '';

    // Validations
    if (!$accountNumber || !$accountPin || !$amount || !$oidFromEnquiry || !$providerTxnId) {
        return $this->respond(['status' => false, 'message' => 'Missing required parameters'], 400);
    }

    // ✅ Salik: Amount must be multiple of 50, 50–1000
    if ($amount < 50 || $amount > 1000 || $amount % 50 != 0) {
        return $this->respond(['status' => false, 'message' => 'Amount must be between 50–1000 AED in multiples of 50'], 400);
    }

    
    // Flat card fee (if applicable)
    $cardFee                = 55000;
    $totalAmountWithCardFee = $amount + $cardFee;

    // 🔑 Create Hash Request
    $hashData = [
        "rawData" => [
            "oid"           => $oidFromEnquiry,
            "uid"           => "344",
            "billerId"      => "13",
            "method"        => "pay",
            "paymentMethod" => "CARD",
            "cardNumber"    => "1111",
            "authNumber"    => "1111",
            "paidAmount"    => "$amount",
            "invoiceAmount" => "$totalAmountWithCardFee",
            "reqField1"     => $accountNumber,
            "reqField2"     => $accountPin,
            "reqField3"     => $providerTxnId,
            "timestamp"     => ""
        ],
        "key"       => "5596e86a42755cd1ef08b603318d5bbe77362625ce41a3a9063e4baff669cbd3",
        "algorithm" => "md5"
    ];

    $hashRes = $client->post($this->hasrequest, [
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => json_encode($hashData),
        'http_errors' => false,
        'verify' => false
    ]);
    $hashResult = json_decode($hashRes->getBody(), true);

    $secureSign = $hashResult['secure_sign'] ?? null;
    $timestamp  = $hashResult['timestamp'] ?? null;

    if (!$secureSign || !$timestamp) {
        return $this->respond(['status' => false, 'message' => 'Failed to generate hash'], 400);
    }

    // 🔑 Final Payment
    $payData = [
        "secureSign"    => $secureSign,
        "oid"           => $oidFromEnquiry,
        "uid"           => "344",
        "billerId"      => "13",
        "timestamp"     => $timestamp,
        "paymentMethod" => "CARD",
        "cardNumber"    => "1111",
        "authNumber"    => "1111",
        "method"        => "pay",
        "paidAmount"    => "$amount",
        "invoiceAmount" => "$totalAmountWithCardFee",
        "reqField1"     => $accountNumber,
        "reqField2"     => $accountPin,
        "reqField3"     => $providerTxnId
    ];

    $paymentRes = $client->post('https://mobportal.mbme.org:11005/mpay/v1/pay', [
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => json_encode($payData),
        'http_errors' => false,
        'verify' => false
    ]);
    $paymentResult = json_decode($paymentRes->getBody(), true);

    // ✅ Check success
    $respCode      = $paymentResult['respCode'] ?? null;
    $innerStatus   = ($paymentResult['status'] == 1);
    $transactionId = $paymentResult['data']['transactionId'] ?? null;

    $isSuccess = ($respCode == 0 && $innerStatus);
    $status    = $isSuccess ? 'SUCCESS' : 'FAILED';

    // ✅ Save Transaction
    $model = new \App\Models\TransactionModel();
    $model->insert([
        'transaction_id' => $transactionId,
        'account_number' => $accountNumber,
        'amount'         => $amount,
        'customer_name'  => $userId,
        'customer_mobile'=> $customerMobile,
        'status'         => $status,
        'Payment_type'   => 'Salikdirect',
         'OID'            =>$oidFromEnquiry,
        'response'       => json_encode($paymentResult)
    ]);

    return $this->respond([
        'status'  => $isSuccess,
        'message' => $isSuccess ? 'Payment processed successfully' : 'Payment failed',
        'data'    => $paymentResult
    ]);
}




public function fetchBill()
{
                $this->session = \Config\Services::session();

    $input = $this->request->getJSON(true);
    $accountId = $input['account_number'] ?? '';
    $accountPin = $input['account_pin'] ?? '';

    if (!$accountId || !$accountPin) {
        return $this->failValidationErrors('Account number and PIN are required.');
    }

    $client = \Config\Services::curlrequest();

      $userId = $this->session->get('user_id');

    // ✅ Load user model
    $userModel = new \App\Models\UsersModel();
    $user      = $userModel->find($userId);

    if (empty($user) || empty($user['merchant_uid']) || empty($user['merchant_token'])) {
        return $this->respond([
            'status'  => false,
            'message' => 'Please register in merchant account.'
        ], 400);
    }

    // ✅ Assign merchant credentials
    $merchant_uid   = $user['merchant_uid'];
    $merchant_token = $user['merchant_token'];

    try {
        // 🔑 Create Hash Request
        $hashData = [
            "rawData" => [
                "oid"       => "", // auto generated by hash service
                "uid"       => "$merchant_uid",
                "billerId"  => "13",
                "method"    => "balance",
                "reqField1" => $accountId,
                "reqField2" => $accountPin,
                "timestamp" => ""
            ],
            "key"       => "$merchant_token",
            "algorithm" => "md5"
        ];

        $hashRes = $client->post($this->hasrequest, [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode($hashData),
            'http_errors' => false,
            'verify' => false
        ]);
        $hashResult = json_decode($hashRes->getBody(), true);

        if (empty($hashResult['oid'])) {
            return $this->respond(['error' => 'Failed to get oid from hash request'], 400);
        }

        $oid        = $hashResult['oid'];
        $secureSign = $hashResult['secure_sign'];
        $timestamp  = $hashResult['timestamp'] ?? time();

        // 🔑 Balance enquiry
        $balanceData = [
            "secureSign" => $secureSign,
            "oid"        => $oid,
            "uid"        => "$merchant_uid",
            "billerId"   => "13",
            "method"     => "balance",
            "reqField1"  => $accountId,
            "reqField2"  => $accountPin,
            "timestamp"  => $timestamp
        ];

        $balanceRes = $client->post('https://mobportal.mbme.org:11005/mpay/v1/enquiry', [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode($balanceData),
            'http_errors' => false,
            'verify' => false
        ]);

        $balanceResult = json_decode($balanceRes->getBody(), true);

        return $this->respond([
            'status'       => true,
            'message'      => 'Balance fetched',
            'data'         => $balanceResult['data'] ?? [],
            'oid'          => $oid,
            'secure_sign'  => $secureSign,
            'timestamp'    => $timestamp
        ]);

    } catch (\Exception $e) {
        return $this->failServerError('Error fetching bill: ' . $e->getMessage());
    }
}


     public function fetchBillFromPostman()
    {
        $input = $this->request->getJSON(true);
        $account = $input['account_number'] ?? '';

        if (!$account) {
            return $this->failValidationErrors('Account number is required.');
        }

        // Generate transaction ID
        $transactionId = date('ymdHis') . substr(microtime(true) * 100, -2) . rand(0, 9) . '3684';

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
                'merchantLocation' => 'DBAPSP',
                'serviceId'        => '125',
                'method'           => 'balance',
                'lang'             => 'en',
                'reqField1'        => $account,
                'reqField2'        => 'AccountID'
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
        $account = $input['account_number'] ?? '';
        $amountRaw = $input['amount'] ?? '';
        $customerMobile = $input['customer_mobile'] ?? '';
         $userId = $input['merchant_id'] ?? '';
         $name = $userId ?? '';
      

         $usersModel = new \App\Models\UsersModel();
        $walletModel = new \App\Models\WalletModel(); 

          // Step 0: Check user wallet balance
        $user = $usersModel->find($userId);


        $amount = floatval(preg_replace('/[^0-9.]/', '', $amountRaw));

        if (!$account || !$amount) {
            return $this->failValidationErrors('Account number and amount are required.');
        }

        $transactionId = date('ymdHis') . substr(microtime(true) * 100, -2) . rand(0, 9) . '3684';

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
                    'merchantLocation' => 'DBAPSP',
                    'serviceId'        => '125',
                    'method'           => 'balance',
                    'lang'             => 'en',
                    'reqField1'        => $account,
                    'reqField2'        => 'AccountID'
                ]),
                'http_errors' => false
            ]);
            $balance = json_decode($balanceRes->getBody(), true);

            if (($balance['responseCode'] ?? '') !== '000') {
                return $this->respond(['error' => 'Balance fetch failed', 'details' => $balance], 400);
            }

            // Now perform payment
            $paymentData = [
                'transactionId'    => $transactionId,
                'merchantId'       => '3684',
                'merchantLocation' => 'DBAPSP',
                'method'           => 'pay',
                'serviceId'        => '125',
                'lang'             => 'en',
                'paymentMode'      => 'Cash',
                'paidAmount'       => "$amount",
                'reqField1'        => $account
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
                'customer_name'  => $userId,
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
            'pagetitle' => 'Salik direct Payment Reports'
        ]);
    }


}
