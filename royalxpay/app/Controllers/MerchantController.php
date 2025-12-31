<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Libraries\EmailSms;
use App\Libraries\Pagination;
use App\Models\MerchantPrivilegesModel;
use App\Controllers\BaseController;
use App\Controllers\Api\GeneralApiController;


class MerchantController extends BaseController
{
    protected $session;
    protected $usersModel;
    protected $isAdminLoggedIn;
      protected $privilegesModel;

    public function __construct()
    {
        $this->session = session();
        $this->usersModel = new UsersModel();
        $this->isAdminLoggedIn = $this->session->get('isAdminLoggedIn');
        $this->privilegesModel = new MerchantPrivilegesModel();
        $this->initializePrivileges();
    }

    public function index()
    {
        set_title('Merchant list | ' . SITE_NAME);

        $data = [
            'action' => "Merchant",
            'pageTitle' => "Merchant list",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'companyId' => '',
            'startDate' => '',
            'endDate' => '',
            'amount' => '',
            'searchArray' => []
        ];

        $customPagination = new Pagination();

        // Include new filter field: 'amount'
        $searchFields = ['txtsearch', 'companyId', 'startDate', 'endDate', 'amount'];
        foreach ($searchFields as $field) {
            $value = $this->request->getGet($field);
            if ($value !== null && $value !== '') {
                $data[$field] = $value;
                $data['searchArray'][$field] = $value;
            }
        }

        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 200;
        $totalRecord = $this->usersModel->getUsersDetails($data['searchArray'], 'Merchant', '', '', 1);
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - $startLimit;
        $data['startLimit'] = $startLimit;
        $data['pagetitle'] = 'Merchant list';
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $this->usersModel->getUsersDetails($data['searchArray'], 'Merchant', $startLimit, $Limit);

         // ✅ yahan modules, submodules, actions bhi bhej do
    $db = \Config\Database::connect();

    // ✅ Modules
    $modules = $db->table('merchant_privileges')
        ->select('DISTINCT(module) as name')
        ->get()->getResultArray();

    // ✅ Submodules
    $submodules = $db->table('merchant_privileges')
        ->select('DISTINCT(submodule) as name')
        ->where('submodule IS NOT NULL')
        ->get()->getResultArray();

    // ✅ Actions
    $actions = $db->table('merchant_privileges')
        ->select('DISTINCT(action) as name')
        ->where('action IS NOT NULL')
        ->get()->getResultArray();

    // ✅ Users list with their privileges
    $merchant_result = $db->table('users u')
    ->select('u.*, mp.module, mp.submodule, mp.action')
    ->join('merchant_privileges mp', 'u.user_id = mp.merchant_id', 'left')
    ->get()->getResult();   // not getResultArray()

   // ✅ group privileges user-wise
// ✅ group privileges user-wise
$userPrivileges = [];
foreach ($merchant_result as $row) {
    $uid = $row->user_id;

    $modules    = !empty($row->module) ? explode(',', $row->module) : [];
    $submodules = !empty($row->submodule) ? explode(',', $row->submodule) : [];
    $actions    = !empty($row->action) ? explode(',', $row->action) : [];

    // normalize submodules (replace spaces with underscore & lowercase)
    $submodules = array_map(function($s){ return strtolower(str_replace(' ','_',$s)); }, $submodules);
    $actions    = array_map('strtolower', $actions);

    $userPrivileges[$uid] = [
        'modules'    => array_unique($modules),
        'submodules' => array_unique($submodules),
        'actions'    => array_unique($actions),
    ];
}


// ✅ Duplicate remove karne ke liye
foreach ($userPrivileges as $uid => $priv) {
    $userPrivileges[$uid]['modules']    = array_unique($priv['modules']);
    $userPrivileges[$uid]['submodules'] = array_unique($priv['submodules']); // ✅ ab match karega
    $userPrivileges[$uid]['actions']    = array_unique($priv['actions']);
}


$data['merchant_result']  = $merchant_result;
$data['userPrivileges']   = $userPrivileges;

        return view('merchant/index', $data);
    }

    public function edit($id)
    {
        $customerModel = new UsersModel();
        $customer = $customerModel->find($id);
        if ($customer) {
            return $this->response->setJSON($customer);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Merchant not found']);
        }
    }

    // Public method to call from frontend or controller
    public function getMerchantWalletBalance()
    {
        $transactionId = $this->generateTransactionId();
        $token = $this->getMbmeTokenDirect();

        if (!$token || !$transactionId) {
            return $this->fail('Token generation or Transaction ID failed.', 500);
        }

        $balance = $this->getMerchantBalanceFromMbme($token, $transactionId);

        return $this->respond([
            'wallet_balance' => $balance,
            'transaction_id' => $transactionId
        ]);
    }


  public function privileges($merchant_id)
{
    $db = \Config\Database::connect();

    // Jo privileges save kiye gaye hain wo table se nikal lo
    $builder = $db->table('merchant_privileges');
    $builder->select('module_id, submodule_id, action_id');
    $builder->where('merchant_id', $merchant_id);
    $query = $builder->get()->getResultArray();

    // Module, Submodule aur Action ke naam bhi saath lana hai
    $modules = $db->table('modules')->get()->getResultArray();
    $submodules = $db->table('submodules')->get()->getResultArray();
    $actions = $db->table('actions')->get()->getResultArray();

    // Map bana lete hain
    $modulesMap = [];
    foreach ($modules as $m) {
        $modulesMap[$m['id']] = $m['name'];
    }

    $submodulesMap = [];
    foreach ($submodules as $s) {
        $submodulesMap[$s['id']] = $s['name'];
    }

    $actionsMap = [];
    foreach ($actions as $a) {
        $actionsMap[$a['id']] = $a['name'];
    }

    // Ab privileges ko readable bana dete hain
    $privileges = [];
    foreach ($query as $row) {
        $privileges[] = [
            'module'    => $modulesMap[$row['module_id']] ?? '',
            'submodule' => $submodulesMap[$row['submodule_id']] ?? '',
            'action'    => $actionsMap[$row['action_id']] ?? ''
        ];
    }

    // Ab isse view me bhej do
    return view('admin/merchant/privileges_list', [
        'merchant_id' => $merchant_id,
        'privileges'  => $privileges
    ]);
}


    // ✅ Save Privileges
 public function savePrivileges()
{
    $merchant_id = $this->request->getPost('merchant_id');
    $module      = $this->request->getPost('module');      
    $submodules  = $this->request->getPost('submodules');   // multiple submodules
    $actions     = $this->request->getPost('actions');      // multiple actions

    $db = \Config\Database::connect();
    $builder = $db->table('merchant_privileges');

    if (!empty($submodules) && !empty($actions)) {
        foreach ($submodules as $submodule) {

            // purane records delete karo same merchant+module+submodule ke liye
            $builder->where('merchant_id', $merchant_id)
                    ->where('module', $module)
                    ->where('submodule', $submodule)
                    ->delete();

            foreach ($actions as $act) {
                $data = [
                    'merchant_id' => $merchant_id,
                    'module'      => $module,
                    'submodule'   => $submodule,
                    'action'      => strtolower($act),
                    'can_add'     => ($act === 'add') ? 1 : 0,
                    'can_edit'    => ($act === 'edit') ? 1 : 0,
                    'can_delete'  => ($act === 'delete') ? 1 : 0,
                    'can_view'    => ($act === 'view') ? 1 : 0,
                    'created_at'  => date('Y-m-d H:i:s'),
                ];
                $builder->insert($data);
            }
        }
    }

    return redirect()->back()->with('success', 'Merchant previlage setting done successfully.');
}

public function fetch_privillege()
{
    $MerchantModel = new \App\Models\MerchantModel();
    $PrivilegeModel = new \App\Models\PrivilegeModel();

    // Merchant list fetch karo
    $results = $MerchantModel->findAll();

    // Har merchant ke privileges fetch karke attach kar do
    foreach ($results as &$merchant) {
        $privileges = $PrivilegeModel
            ->where('merchant_id', $merchant->user_id)
            ->findAll();

        // yahan ek array bana rahe hai jo module, submodules, actions store karega
        $merchant->privileges = [
            'module'     => $privileges[0]['module'] ?? '',
            'submodules' => !empty($privileges[0]['submodules']) ? explode(',', $privileges[0]['submodules']) : [],
            'actions'    => !empty($privileges[0]['actions']) ? explode(',', $privileges[0]['actions']) : []
        ];
    }

    return view('admin/merchant/list', [
        'results' => $results,
        'pagination' => [], // jo bhi aap already bhej rahe ho
    ]);
}




    // Token generation function
    private function getMbmeTokenDirect()
    {
        $client = \Config\Services::curlrequest();

        $postData = http_build_query([
            'grant_type' => 'password',
            'username'   => 'shiva',
            'password'   => 'MdNpTjXu'
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

   public function save()
    {
        $validation = \Config\Services::validation();

        // Get input
        $customerId = $this->request->getPost('customer_id');
        $status = $this->request->getPost('status');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');
        $user_type = 'Merchant';

        // Common validation rules
        $rules = [
            'name' => 'required',
            'phone' => 'required|numeric|min_length[8]|max_length[15]'
        ];

        // Only validate password if it's new user or password field is filled
        if (!$customerId || !empty($password)) {
            $rules['password'] = 'required|min_length[6]';
            $rules['confirm_password'] = 'required|matches[password]';
        }

        // Validate form
            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->validator->getErrors(), // ✅ fixed
                ]);
            }

        $customerModel = new \App\Models\UsersModel();

         // Check if email is already taken (for new or updated user)
        if (!empty($email)) {
            $existingUser = $customerModel
                ->where('email', $email)
                ->where('user_id !=', $customerId ?? 0)
                ->first();

            if ($existingUser) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => ['email' => 'Email already exists, please try with another.'],
                ]);
            }
        }


        $data = [
            'username' => $this->request->getPost('name'),
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
             'address' => $this->request->getPost('address'),
             'company_name'      => $this->request->getPost('company_name'),
'company_website'      => $this->request->getPost('company_website'),
'contactus'      => $this->request->getPost('contactus'),
            'alt_mobile_number' => $this->request->getPost('alt_mobile_number'),
            'gender' => $this->request->getPost('gender'),
            'status' => !empty($status) ? $status : 'active',
            'user_type' => $user_type,
            'notes' => $this->request->getPost('notes'),
            'wallet' => 0,
            
        ];

        // Optional email
        if (!empty($email)) {
            $data['email'] = $email;
        }

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

            // Handle Logo Upload
            $logoFile = $this->request->getFile('logo');
            if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
                $validTypes = ['image/jpeg', 'image/png', 'image/svg+xml'];
                if (in_array($logoFile->getMimeType(), $validTypes)) {
                    $newName = time() . '_' . $logoFile->getRandomName();
                    $uploadPath = ROOTPATH . 'public/uploads/logos/';
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                    $logoFile->move($uploadPath, $newName);
                    $data['logo'] = 'uploads/logos/' . $newName;
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'errors' => ['logo' => 'Invalid file type. Only JPG, PNG, or SVG allowed.'],
                    ]);
                }
            }

         // === If new merchant, get wallet balance and store ===
        // if (!$customerId) {
        //     // Step 1: Generate transaction ID (PHP version)
        //     $transactionId = $this->generateTransactionId();

        //     // Step 2: Get MBME token (from getToken() logic)
        //     $token = $this->getMbmeTokenDirect(); // implement below

        //     // Step 3: Call Wallet Balance API
        //     $walletBalance = 0.00;
        //     if ($token && $transactionId) {
        //         $walletBalance = $this->getMerchantBalanceFromMbme($token, $transactionId);
        //     }

        //     $data['wallet'] = $walletBalance;
        // }

        if ($customerId) {
            $data['user_id'] = $customerId;
            if ($customerModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Merchant updated successfully',
                ]);
            }
        } else {
            if ($customerModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Merchant added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'errors' => 'Failed to save Merchant. Please try again.',
        ]);
    }

  
    
    


    public function showDetails($userId)
    {

        $data = array();
        set_title('Merchant Details | ' . SITE_NAME);

        $data['pageTitle'] = "Merchant Details";
        $data['record'] = $this->usersModel
            ->join('address', 'address.user_id = users.user_id', 'left')
            ->where('users.user_id', $userId)
            ->select('users.*,  address.city, address.state, address.postal_code, address.country, address.address_type')
            ->first();

        return view('merchant/preview', $data);
    }

    public function delete()
    {
        $customerId = $this->request->getPost('customerId');

        if (is_numeric($customerId) && !empty($customerId)) {
            try {
                $customerModel = new UsersModel();
                $customerModel->where('user_id', $customerId)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Product deleted successfully.',
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again.',
            ]);
        }
    }
}
