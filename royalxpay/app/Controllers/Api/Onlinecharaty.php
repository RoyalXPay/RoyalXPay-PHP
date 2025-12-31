<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\Api\TransactionModel;
use Mpdf\Mpdf;
class Onlinecharaty extends BaseController
{
    use ResponseTrait;
    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }
   
    private $username = 'DBAPSP';
    private $password = 'y08yhFn1LC';
    private $tokenUrl = 'https://qty.mbme.org:8080/v2/mbme/oauth/token';
    private $paymentUrl = 'https://qty.mbme.org:8080/v2/api/payment';

    public function index()
    {
        return view('admin/Api/onlinecharaty/index', [
            'pagetitle' => 'Online charaty Payment'
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
        $html = view('admin/Api/onlinecharaty/receipt_pdf', [
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

        $input = $this->request->getJSON(true);
        $token = $input['token'] ?? '';
        $charityType = $input['reqField1'] ?? ''; // ✅ Type of charity (Zakat, Sadaqa, etc.)
        $productDetail = $input['reqField2'] ?? ''; // ✅ Product details (ZakatFund, Waqf, etc.)
        $amountRaw = $input['paidAmount'] ?? '0';
        $amountFloat = floatval(preg_replace('/[^0-9.]/', '', $amountRaw));
        $amount = intval($amountFloat); // MBME requires integer
        $transactionId = $input['transactionId'] ?? '';
        $customerMobile = $input['customer_mobile'] ?? '';
        $userId = $this->session->get('user_id');

        if (!$token || !$charityType || !$productDetail || !$amount || !$transactionId) {
            return $this->failValidationErrors('Missing required fields.');
        }

        $usersModel = new \App\Models\UsersModel();
        $walletModel = new \App\Models\WalletModel();

        // Check user wallet
        $user = $usersModel->find($userId);
        if (!$user) {
            return $this->respond(['error' => 'User not found.'], 404);
        }

        if ($user['wallet'] < $amount) {
            return $this->respond(['error' => 'Insufficient wallet balance.'], 400);
        }

        $client = \Config\Services::curlrequest();

        try {
            // ✅ MBME Payment API payload
            $paymentData = [
                'transactionId'    => $transactionId,
                'merchantId'       => '3684',
                'merchantLocation' => 'DBAPSP',
                'method'           => 'pay',
                'serviceId'        => '999',
                'lang'             => 'en',
                'paymentMode'      => 'Cash',
                'paidAmount'       => $amount,
                'reqField1'        => $charityType,    // ✅ Type of charity
                'reqField2'        => $productDetail,  // ✅ Product details
                "cardNumber"       => "",
                "authNumber"       => ""
            ];

            $paymentResponse = $client->post($this->paymentUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json'
                ],
                'body' => json_encode($paymentData),
                'http_errors' => false
            ]);

            $result = json_decode($paymentResponse->getBody(), true);
            file_put_contents(WRITEPATH . 'logs/onlinecharaty_payment.log', json_encode($result, JSON_PRETTY_PRINT));

            // Save transaction
            $model = new TransactionModel();
            $model->insert([
                'transaction_id'  => $transactionId,
                'account_number'  => $charityType,
                'amount'          => $amount,
                'customer_name'   => $userId,
                'customer_mobile' => $customerMobile,
                'status'          => $result['status'] ?? 'FAILED',
                'Payment_type'    => 'Onlinecharaty',
                'response'        => json_encode($result)
            ]);

            // Deduct wallet
            $remainingWallet = $user['wallet'] - $amount;
            $usersModel->update($userId, ['wallet' => $remainingWallet]);

            $walletModel->insert([
                'user_id'          => $userId,
                'amount'           => $amount,
                'transaction_type' => 'debit',
                'Payment_type'     => 'Onlinecharaty',
                'created_at'       => date('Y-m-d H:i:s')
            ]);

            $this->session->set('wallet', $remainingWallet);

            return $this->respond($result);

        } catch (\Exception $e) {
            return $this->respond([
                'error'     => 'Payment failed: ' . $e->getMessage(),
                'response'  => method_exists($e, 'getResponse') ? $e->getResponse()->getBody() : 'No response body'
            ], 500);
        }
    }

    public function makePaymentFromPostman()
    {
        $input = $this->request->getJSON(true);
        $account = $input['account_number'] ?? '';
        $amountRaw = $input['amount'] ?? '';
        $charityType = $input['reqField1'] ?? ''; // ✅ Type of charity (Zakat, Sadaqa, etc.)
        $productDetail = $input['reqField2'] ?? '';
        $customerMobile = $input['customer_mobile'] ?? '';
         $userId = $input['merchant_id'] ?? '';
         $name = $userId ?? '';
      

         $usersModel = new \App\Models\UsersModel();
        $walletModel = new \App\Models\WalletModel(); 

          // Step 0: Check user wallet balance
        $user = $usersModel->find($userId);


        $amount = floatval(preg_replace('/[^0-9.]/', '', $amountRaw));

        if ( !$amount) {
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

           
            // Now perform payment
            $paymentData = [
                'transactionId'    => $transactionId,
                'merchantId'       => '3684',
                'merchantLocation' => 'DBAPSP',
                'method'           => 'pay',
                'serviceId'        => '999',
                'lang'             => 'en',
                'paymentMode'      => 'Cash',
                'paidAmount'       => $amount,
                'reqField1'        => $charityType,    // ✅ Type of charity
                'reqField2'        => $productDetail,  // ✅ Product details
                "cardNumber"       => "",
                "authNumber"       => ""
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
                'Payment_type'   => 'Onlinecharaty',
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
            'Payment_type'   => 'Onlinecharaty',
            'created_at'       => date('Y-m-d H:i:s')
        ]);


            return $this->respond($result);

        } catch (\Exception $e) {
            return $this->failServerError('Payment failed: ' . $e->getMessage());
        }
    }


     public function fetchBill()
    {
        $input = $this->request->getJSON(true);
        $token = $input['token'] ?? '';
        $account = $input['account_number'] ?? '';
        $transactionId = $input['transaction_id'] ?? '';

        if (!$token || !$account || !$transactionId) {
            return $this->failValidationErrors('Missing required fields.');
        }

        $client = \Config\Services::curlrequest();
        
        $balanceData = [
            'transactionId'    => $transactionId,
            'merchantId'       => '3684',
            'merchantLocation' => 'DBAPSP',
            'serviceId'        => '1', // ✅ Corrected
            'method'           => 'balance',
            'lang'             => 'en',
            'reqField1'        => $account // ✅ Mobile number only, no reqField2

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

   public function reports()
{
    $this->session = \Config\Services::session();
    $userId = $this->session->get('user_id');
    $model = new \App\Models\Api\TransactionModel();

    $builder = $model->where('customer_name', $userId)
                     ->where('Payment_type', 'Onlinecharaty');

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

    return view('admin/Api/onlinecharaty/report', [
        'transactions' => $transactions,
        'pagetitle' => 'Online charaty Payment Reports'
    ]);
}


}
