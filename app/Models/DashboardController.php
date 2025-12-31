<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\OrderModel;
use App\Models\InvoiceModel;
use App\Models\CompanyModel;
use App\Models\ProductModel;
use App\Models\ContractModel;
use App\Models\PurchaseModel;
use App\Models\FeedbackModel;
use App\Models\QuotationModel;
use App\Models\ProductMasterModel;
use App\Models\CustomerModel;
use App\Models\PrivilegesModel;
use App\Controllers\BaseController;


class DashboardController extends BaseController
{
    /**
     * Reusable API call function
     */
    private function callApi($url, $method = 'POST', $data = [])
{
    $client = \Config\Services::curlrequest();

    try {
        $response = $client->request($method, $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'body' => json_encode($data),
            'http_errors' => false,
            'timeout' => 30,
            'connect_timeout' => 15,
            'verify' => false // TEMP: disable SSL verification to test
        ]);

        return json_decode($response->getBody(), true);
    } catch (\Exception $e) {
        log_message('error', 'API call to ' . $url . ' failed: ' . $e->getMessage());
        return [
            'error' => true,
            'message' => $e->getMessage(),
            'url' => $url,
            'data' => $data
        ];
    }
}
      

    public function index()
    {
        set_title('Dashboard | ' . SITE_NAME);

        $session = session();
        $user_id = $session->get('user_id');
        $user_type = $session->get('user_type');
        $usersModel = new UsersModel();
        $user = $usersModel->where('user_id', $user_id)->first();

        $name = $user['name'];
        $_SESSION['user_id'] = $user['user_id'];
        $data['user_type'] = $user_type;
        $data['name'] = $name;

        // Load models
        $orderModel = new OrderModel();
        $productMasterModel = new ProductMasterModel();
        $companyModel = new CompanyModel();
        $invoiceModel = new InvoiceModel();
        $feedbackModel = new FeedbackModel();
        $purchaseModel = new PurchaseModel();
        $productModel = new ProductModel();
        $contractModel = new ContractModel();
        $quotationModel = new QuotationModel();
        $customerModel = new CustomerModel();
       $privilegesModel = new PrivilegesModel();
        // Counts
        $orderCount = $orderModel->countAll();
        $productCount = $productMasterModel->countAll();
        $companyCount = $companyModel->countAll();
        $invoiceCount = $invoiceModel->countAll();
        $feedbackCount = $feedbackModel->countAll();
        $purchaseCount = $purchaseModel->countAll();
        $rentProductCount = $usersModel->where('user_type', 'Merchant')->countAllResults();
        $saleProductCount = $productModel->where('product_type', 'sale')->countAllResults();
        $rentContractCount = $contractModel->where('contract_type', 'rent')->countAllResults();
        $saleContractCount = $contractModel->where('contract_type', 'sale')->countAllResults();
        $rentQuotationCount = $quotationModel->where('quotation_type', 'rent')->countAllResults();
        $saleQuotationCount = $quotationModel->where('quotation_type', 'sale')->countAllResults();

        // Merchant logic


$privileges = $privilegesModel->where('merchant_id', $user_id)->findAll();

        $moduleAccess = [];
        foreach ($privileges as $p) {
            if ($p['can_add'] || $p['can_edit'] || $p['can_preview'] || $p['can_delete']) {
                $moduleAccess[] = strtolower($p['module_name']); // only if at least one permission granted
            }
        }
print_r($moduleAccess);die();
session()->set('module_access', $moduleAccess);
        if ($user_type === 'superadmin') {
    // Prepare request payload
    $walletRequest = [
        "secureSign" => "bypass",
        "uid"        => '342', // or use your fixed UID "299"
        "timestamp"  => date("Y-m-d H:i:s")
    ];

    // Call MBME Wallet API
    $walletResponse = $this->callApi(
        "https://mobportal.mbme.org:11005/common/v1/wallet",
        "POST",
        $walletRequest
    );
// print_r($walletResponse);die();
    // print_r( $walletResponse);die();
   if (!isset($walletResponse['error']) && isset($walletResponse['data']['walletAmount'])) {
    $mbmeBalance = (float) $walletResponse['data']['walletAmount'];

    // Update in users table
    $usersModel->update($user_id, ['mbme_wallet' => $mbmeBalance]);

    // Assign for view
    $data['mbme_balance'] = $mbmeBalance;
} else {
    // fallback if API fails
    $data['mbme_balance'] = (float) $user['mbme_wallet'];
}
}

        $walletModel = new \App\Models\WalletModel();
        $walletAmount = $walletModel
            ->selectSum('amount')
            ->where('user_id', $user_id)
            ->get()
            ->getRow()
            ->amount ?? 0;

        $today = date('Y-m-d');

        if ($user_type === 'superadmin' || $user_type === 'admin') {
            $customerCount = $customerModel->countAllResults();
            $totalMerchantUsers = $customerCount;
            $todayMerchantUsers = $customerModel
                ->where('DATE(created_at)', $today)
                ->countAllResults();
        } else {
            $customerCount = $customerModel->where('created_by', $user_id)->countAllResults();
            $totalMerchantUsers = $customerCount;
            $todayMerchantUsers = $customerModel
                ->where('created_by', $user_id)
                ->where('DATE(created_at)', $today)
                ->countAllResults();
        }

        $todayEarning = 0;
        $totalEarning = 0;

        // API Calls
        $redirectUrl = '#';
        $createUserData = [
    "email"    => $user['email'],
    "username" => explode('@', $user['email'])[0], // take part before @ as username
    "password" => $user['password'],
    "role"     => "typing_center"
];

        $createUserResponse = $this->callApi('https://airemainderbyskiphi.skiphi.com/user/create-user/', 'POST', $createUserData);

        if (isset($createUserResponse['id']) || isset($createUserResponse['error'])) {

            $loginData = [
                "username" => $user['email'],
                "password" => $user['password']
            ];
            $loginResponse = $this->callApi('https://airemainderbyskiphi.skiphi.com/user/login/', 'POST', $loginData);

       
           
            if (isset($loginResponse['access'], $loginResponse['refresh'], $loginResponse['userType'])) {
                $redirectUrl = "https://main.d2v3qt7xx431yq.amplifyapp.com/dashboard"
                             . "?refresh=" . urlencode($loginResponse['refresh'])
                             . "&access=" . urlencode($loginResponse['access'])
                             . "&userType=" . urlencode($loginResponse['userType']);

                                 $session->set('aiRedirectUrl', $redirectUrl);

            }

             // ✅ Store in session for global access
        }

        // print_r($redirectUrl);die();
        // Pass to view
        $data = [
            'customerCount' => $customerCount,
            'orderCount' => $orderCount,
            'productCount' => $productCount,
            'companyCount' => $companyCount,
            'aiRedirectUrl'=> $redirectUrl,
            'invoiceCount' => $invoiceCount,
            'feedbackCount' => $feedbackCount,
            'purchaseCount' => $purchaseCount,
            'rentProductCount' => $rentProductCount,
            'saleProductCount' => $saleProductCount,
            'rentContractCount' => $rentContractCount,
            'saleContractCount' => $saleContractCount,
            'rentQuotationCount' => $rentQuotationCount,
            'saleQuotationCount' => $saleQuotationCount,
            'walletAmount' => $walletAmount,
            'totalMerchantUsers' => $totalMerchantUsers,
            'todayMerchantUsers' => $todayMerchantUsers,
            'todayEarning' => $todayEarning,
            'totalEarning' => $totalEarning,
        ];

        return view('admin/dashboard', $data);
    }
}
