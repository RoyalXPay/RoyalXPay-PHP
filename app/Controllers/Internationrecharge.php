<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\Api\TransactionModel;
use Dompdf\Dompdf;
class Internationrecharge extends BaseController
{
    use ResponseTrait;

   
    private $username = 'DBAPSP';
    private $password = 'y08yhFn1LC';
    private $tokenUrl = 'https://qty.mbme.org:8080/v2/mbme/oauth/token';
    private $paymentUrl = 'https://qty.mbme.org:8080/v2/api/payment';

    public function index()
    {
        return view('admin/Api/internationrecharge/index', [
            'pagetitle' => 'Internation Recharge Payment'
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
    $html = view('admin/Api/internationrecharge/receipt_pdf', ['txn' => $transaction]);

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Output as PDF
    $dompdf->stream('Internationrecharge_Receipt_' . $transaction['transaction_id'] . '.pdf', ['Attachment' => true]);
}
   
    public function makePayment()
    {
         
        $this->session = \Config\Services::session();

        $input = $this->request->getJSON(true);
        $token = $input['token'] ?? '';
        $account = $input['account_number'] ?? '';
        $amountRaw = $input['paidAmount'] ?? '0';
        $amount = floatval(preg_replace('/[^0-9.]/', '', $amountRaw));
        // $amount = $input['amount'] ?? '';
        $userId = $this->session->get('user_id');

        $name = $userId ?? '';
        $customerMobile = $input['customer_mobile'] ?? '';
        $transactionId = $input['transaction_id'] ?? '';

        if (!$token || !$account || !$amount || !$name || !$transactionId) {
            return $this->failValidationErrors('Missing required fields.');
        }

         $usersModel = new \App\Models\UsersModel();
        $walletModel = new \App\Models\WalletModel(); 

          // Step 0: Check user wallet balance
        $user = $usersModel->find($userId);
        if (!$user) {
            return $this->respond(['error' => 'User not found.'], 404);
        }

        if ($user['wallet'] < $amount) {
            return $this->respond(['error' => 'Insufficient wallet balance.'], 400);
        }

        $client = \Config\Services::curlrequest();

        
        try {
           

            // STEP 2: Call payment API
            $paymentData = [
                'transactionId'    => $transactionId,
                'merchantId'       => '3684',
                'merchantLocation' => 'DBAPSP',
                'method'           => 'pay',
                'serviceId'        => '112',
                'lang'             => 'en',
                'paymentMode'      => 'Cash',
                'paidAmount'       => $amount,
                'reqField1'        => $account
                'reqField2'        => $account
                'reqField3'        => $account
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

            $model = new TransactionModel();
            $model->insert([
                'transaction_id' => $transactionId,
                'account_number' => $account,
                'amount'         => $amount,
                'customer_name'  => $name,
                'customer_mobile' => $customerMobile,
                'status'         => $result['status'] ?? 'FAILED',
                'Payment_type'   => 'Internationrecharge',
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
            'Payment_type'   => 'Internationrecharge',
            'created_at'       => date('Y-m-d H:i:s')
        ]);

        // Update wallet session value
        $this->session->set('wallet', $remainingWallet);

        return $this->respond($result);

        } catch (\Exception $e) {
            return $this->respond([
                'error' => 'Payment failed: ' . $e->getMessage(),
                'response' => method_exists($e, 'getResponse') ? $e->getResponse()->getBody() : 'No response body'
            ], 500);
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
            'merchantLocation' => 'Your Location',
            'serviceId'        => '112',
            'method'           => 'accountLookUp',
            'lang'             => 'en',
            'reqField1'        => $account,
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
                     ->where('Payment_type', 'Internationrecharge');
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

    return view('admin/Api/internationrecharge/report', [
        'transactions' => $transactions,
        'pagetitle' => 'Internation Recharge Payment Reports'
    ]);
}


}
