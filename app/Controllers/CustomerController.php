<?php
namespace App\Controllers;

use App\Models\CustomerModel;
use CodeIgniter\Controller;
use App\Controllers\BaseController;

class CustomerController extends BaseController
{
    protected $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
  $this->initializePrivileges(); // This calls method from BaseController
        helper(['form', 'url']);
        session();
    }

 public function index()
{
    $session = session();
    $userId = $session->get('user_id');
    $userType = $session->get('user_type');

    $customerModel = new \App\Models\CustomerModel();
    $usersModel = new \App\Models\UsersModel();

    // Get filters from request
    $startDate  = $this->request->getGet('startDate');
    $endDate    = $this->request->getGet('endDate');
    $txtsearch  = $this->request->getGet('txtsearch');
    $merchantId = $this->request->getGet('merchant_id');
    $amount     = $this->request->getGet('amount');

    // Select customer with joined user info
    $customerModel->select('customer.*, users.name as merchant_name, users.user_type as merchant_type');
    $customerModel->join('users', 'users.user_id = customer.created_by', 'left');

    // Restrict merchants to their own customers
    if ($userType !== 'superadmin') {
        $customerModel->where('customer.created_by', $userId);
    }

    // Filter by merchant if selected (only visible to superadmin)
    if (!empty($merchantId)) {
        $customerModel->where('customer.created_by', $merchantId);
    }

    // Filter by amount if entered
    if (!empty($amount)) {
        $customerModel->where('customer.paid_amount >=', floatval($amount));
    }

    // Filter by Start Date
    if (!empty($startDate)) {
        $customerModel->where('DATE(customer.created_at) >=', $startDate);
    }

    // Filter by End Date
    if (!empty($endDate)) {
        $customerModel->where('DATE(customer.created_at) <=', $endDate);
    }

    // Filter by search text (name, phone, email, emirates_id)
    if (!empty($txtsearch)) {
        $customerModel->groupStart()
            ->like('customer.name', $txtsearch)
            ->orLike('customer.phone', $txtsearch)
            ->orLike('customer.email', $txtsearch)
            ->orLike('customer.emirates_id', $txtsearch)
            ->groupEnd();
    }

    $results = $customerModel->orderBy('customer.created_at', 'DESC')->findAll();

    // Load all merchants for dropdown if superadmin
    $merchants = ($userType === 'superadmin') ? $usersModel->where('user_type', 'merchant')->findAll() : [];

    return view('admin/customers/index', [
        'results'     => $results,
        'pagetitle'   => 'Customers',
        'startDate'   => $startDate,
        'endDate'     => $endDate,
        'txtsearch'   => $txtsearch,
        'merchantId'  => $merchantId,
        'amount'      => $amount,
        'merchants'   => $merchants,
    ]);
}




   

    public function save()
    {
        $request = service('request');
        $id = $request->getPost('id'); // ID from form (for update)
        $email = $request->getPost('email');
        $existing = $this->customerModel
            ->where('email', $email)
            ->where('id !=', $id) // Exclude current record when updating
            ->first();

        if ($existing) {
            session()->setFlashdata('error', 'Email already exists for another customer.');
            return redirect()->to(site_url('customers'));
        }

        $data = [
            'name' => $request->getPost('name'),
            'phone' => $request->getPost('phone'),
            'email' => $email,
            'emirates_id' => $request->getPost('emirates_id'),
            'address' => $request->getPost('address'),
            'status' => 'ACTIVE',
            'created_by' => session()->get('user_id'),
        ];

       if ($id) {
            $this->customerModel->update($id, $data);
            $msg = 'Customer updated successfully.';
        } else {
            $this->customerModel->insert($data);
            $msg = 'Customer added successfully.';
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $msg
        ]);
    }

   public function add_amountold()
    {
        $request = service('request');
        $session = session();
        $userId = $session->get('user_id'); // Get logged-in user ID

        $paymentId = $request->getPost('id');
        $amount    = $request->getPost('paid_amount');
        $mode      = $request->getPost('mode_of_payment');

        if (!$paymentId) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Payment ID is missing. Update cannot proceed.'
            ]);
        }
        // Get customer email to fetch user ID from users table
        $customer = $this->customerModel->find($paymentId);

        if (!$customer) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Customer not found.'
            ]);
        }

        $data = [
            'paid_amount'      => $amount,
            'mode_of_payment'  => $mode,
            'paid_status'      => $amount > 0 ? 'PAID' : 'UNPAID',
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        $this->customerModel = new \App\Models\customerModel();
        $updated = $this->customerModel->update($paymentId, $data);
         // Insert wallet entry
        $walletModel = new \App\Models\WalletModel();
        // print_r($paymentId);die();
        $walletModel->insert([
            'user_id'         => $userId,
            'customer_id'     => $customer['id'],
            'amount'          => $amount,
            'transaction_type'=> 'credit',
            'wallet_by'       => $session->get('user_type'),
            'created_at'      => date('Y-m-d H:i:s'),
        ]);
        if ($updated) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Customer payment updated successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Update failed. Please try again.'
            ]);
        }
    }
    
    public function add_amount()
    {
        $request = service('request');
        $session = session();
        $userId = $session->get('user_id'); // logged in user
        $userType = $session->get('user_type');

        $paymentId = $request->getPost('id');
        $amount    = floatval($request->getPost('paid_amount'));
        $mode      = $request->getPost('mode_of_payment');

        if (!$paymentId || $amount <= 0) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Invalid payment details.'
            ]);
        }

        $customer = $this->customerModel->find($paymentId);
    
        $totalamount = $amount + floatval($customer['paid_amount']);
        if (!$customer) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Customer not found.'
            ]);
        }

        // 1. Update customer payment record
        $data = [
            'paid_amount'      => $totalamount,
            'mode_of_payment'  => $mode,
            'paid_status'      => 'PAID',
            'updated_at'       => date('Y-m-d H:i:s'),
        ];
        $this->customerModel->update($paymentId, $data);

        // 2. Insert wallet transaction record
        $walletModel = new \App\Models\WalletModel();
        $walletModel->insert([
            'user_id'         => $userId,
            'customer_id'     => $customer['id'],
            'amount'          => $amount,
            'transaction_type'=> 'credit',
            'wallet_by'       => $userType,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);

        // 3. Update `wallet` in `users` table if customer exists there
        $usersModel = new \App\Models\UsersModel();
        $userRecord = $usersModel->where('email', $customer['email'])->first(); // match by email or other field

        if ($userRecord) {
            $newBalance = floatval($userRecord['wallet']) + $amount;
            $usersModel->update($userRecord['user_id'], ['wallet' => $newBalance]);
        }

        // 3. Credit amount to merchant's wallet (created_by user)
        $usersModel = new \App\Models\UsersModel();
        $merchantId = $customer['created_by'];
        $merchant = $usersModel->find($merchantId);

        if ($merchant) {
            $newWalletAmount = floatval($merchant['wallet']) + $amount;
            $usersModel->update($merchantId, ['wallet' => $newWalletAmount]);

            // 4. Add wallet transaction for merchant
            $walletModel->insert([
                'user_id'          => $merchantId,
                'customer_id'      => $customer['id'],
                'amount'           => $amount,
                'transaction_type' => 'credit',
                'wallet_by'        => 'merchant_credit',
                'created_at'       => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Customer payment updated successfully.'
        ]);
    }



    public function payment_history($customerId)
{
    $walletModel = new \App\Models\WalletModel();
    $db = \Config\Database::connect();

    $builder = $db->table('wallet');
    $builder->select('wallet.*, customer.name, customer.phone');
    $builder->join('customer', 'customer.id = wallet.customer_id', 'left');
    $builder->where('wallet.customer_id', $customerId);
    $builder->orderBy('wallet.created_at', 'DESC');

    $data['transactions'] = $builder->get()->getResultArray();
    $data['pagetitle'] = 'Customer Payment History';
    $data['customer'] = $this->customerModel->find($customerId);

    return view('admin/customers/payment_history', $data);
}

    

    public function delete()
    {
        $id = $this->request->getPost('id');
        $this->customerModel->delete($id);
        return redirect()->to(site_url('customers'));
    }

    public function show($id)
    {
        $data['pagetitle'] = 'Preview Customer';
        $data['customer'] = $this->customerModel->find($id);
        return view('admin/customers/preview', $data);
    }
}
