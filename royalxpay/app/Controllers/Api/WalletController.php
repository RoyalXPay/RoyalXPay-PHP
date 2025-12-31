<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\WalletModel;
use App\Controllers\BaseController;

class WalletController extends BaseController
{
    protected $session;
    protected $usersModel;

    public function __construct()
    {
        $this->session = session();
        $this->usersModel = new UsersModel();
        $this->walletModel = new WalletModel();
    }

    public function index()
{
    $session = session();
    $userType = $session->get('user_type');
    $userId = $session->get('user_id');

    $usersModel = $this->usersModel;

    // Filters
    $startDate  = $this->request->getGet('startDate');
    $endDate    = $this->request->getGet('endDate');
    $txtsearch  = $this->request->getGet('txtsearch');
    $merchantId = $this->request->getGet('merchant_id');
    $amount     = $this->request->getGet('amount');

    $usersModel->select('*');
    $usersModel->whereIn('user_type', ['user', 'merchant', 'Merchant']);

    // Superadmin can filter by merchant
    if ($userType !== 'superadmin') {
        $usersModel->where('user_id', $userId);
    } elseif (!empty($merchantId)) {
        $usersModel->where('user_id', $merchantId);
    }

    // Filter by wallet amount
    if (!empty($amount)) {
        $usersModel->where('wallet >=', floatval($amount));
    }

    // Filter by created_at
    if (!empty($startDate)) {
        $usersModel->where('DATE(created_at) >=', $startDate);
    }

    if (!empty($endDate)) {
        $usersModel->where('DATE(created_at) <=', $endDate);
    }

    // Filter by text
    if (!empty($txtsearch)) {
        $usersModel->groupStart()
            ->like('name', $txtsearch)
            ->orLike('phone', $txtsearch)
            ->orLike('email', $txtsearch)
            ->orLike('emirates_id', $txtsearch)
            ->groupEnd();
    }

    $users = $usersModel->orderBy('created_at', 'DESC')->findAll();

    // Fetch merchant list for dropdown if superadmin
    $merchants = ($userType === 'superadmin') ?
        $this->usersModel->where('user_type', 'merchant')->findAll() : [];

    return view('admin/wallets/index', [
        'pagetitle'  => 'Manage Wallet',
        'users'      => $users,
        'startDate'  => $startDate,
        'endDate'    => $endDate,
        'txtsearch'  => $txtsearch,
        'merchantId' => $merchantId,
        'amount'     => $amount,
        'merchants'  => $merchants,
    ]);
}


   public function create()
{
    $data['pagetitle'] = 'Add Wallet';
    return view('admin/wallets/create', $data);
}

public function store_old()
{
    $userId = $this->request->getPost('user_id');
    $walletAmount = $this->request->getPost('wallet');

    $user = $this->usersModel->find($userId);
    if (!$user) {
        session()->setFlashdata('error', 'User not found.');
        return redirect()->back();
    }

    $newWallet = $user['wallet'] + floatval($walletAmount);

    $this->usersModel->update($userId, ['wallet' => $newWallet]);

    session()->setFlashdata('success', 'Wallet amount added successfully.');
    return redirect()->to('manage-wallet');
}


public function storelatest()
{
    $session = session();
    $loggedInUserId = $session->get('user_id'); // superadmin or whoever is logged in
    $merchantId = $this->request->getPost('user_id');
    $amount = floatval($this->request->getPost('wallet'));

    // Load models
    $userModel = $this->usersModel;
    $walletModel = $this->walletModel;

    // Fetch both users
    $adminUser = $userModel->select('user_id, wallet')->find($loggedInUserId);
    $merchantUser = $userModel->select('user_id, wallet')->find($merchantId);

    if (!$adminUser || !$merchantUser) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'User not found']);
    }

    if ($adminUser['wallet'] < $amount) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Insufficient balance']);
    }

    // Begin DB transaction
    $db = \Config\Database::connect();
    $db->transStart();

    // 1. Deduct from logged-in user
    $userModel->update($loggedInUserId, ['wallet' => $adminUser['wallet'] - $amount]);

    // 2. Credit to merchant
    $userModel->update($merchantId, ['wallet' => $merchantUser['wallet'] + $amount]);

    // 3. Insert into wallet table - DEBIT (admin)
    $walletModel->insert([
        'user_id' => $loggedInUserId,
        'amount' => $amount,
        'transaction_type' => 'debit',
        'customer_id' => $merchantId,
        'wallet_by' => 'superadmin',
        'created_at' => date('Y-m-d H:i:s')
    ]);

    // 4. Insert into wallet table - CREDIT (merchant)
    $walletModel->insert([
        'user_id' => $merchantId,
        'amount' => $amount,
        'transaction_type' => 'credit',
        'customer_id' => $loggedInUserId,
        'wallet_by' => 'superadmin',
        'created_at' => date('Y-m-d H:i:s')
    ]);

    // Finish transaction
    $db->transComplete();

    if ($db->transStatus() === false) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Transaction failed']);
    }

    return $this->response->setJSON(['status' => 'success', 'message' => 'Wallet transferred successfully']);
}

public function store()
{
    $session = session();
    $loggedInUserId = $session->get('user_id');
    $merchantId = $this->request->getPost('user_id');
    $amount = floatval($this->request->getPost('wallet'));

    $userModel = $this->usersModel;
    $walletModel = $this->walletModel;

    $adminUser = $userModel->select('user_id, wallet')->find($loggedInUserId);
    $merchantUser = $userModel->select('user_id, wallet')->find($merchantId);

    if (!$adminUser || !$merchantUser) {
        session()->setFlashdata('swal_error', 'User not found');
        return redirect()->back();
    }

    if ($adminUser['wallet'] < $amount) {
        session()->setFlashdata('swal_error', 'Insufficient balance');
        return redirect()->back();
    }

    $db = \Config\Database::connect();
    $db->transStart();

    $userModel->update($loggedInUserId, ['wallet' => $adminUser['wallet'] - $amount]);
    $userModel->update($merchantId, ['wallet' => $merchantUser['wallet'] + $amount]);

    $walletModel->insert([
        'user_id' => $loggedInUserId,
        'amount' => $amount,
        'transaction_type' => 'debit',
        'customer_id' => $merchantId,
        'wallet_by' => 'superadmin',
        'created_at' => date('Y-m-d H:i:s')
    ]);

    $walletModel->insert([
        'user_id' => $merchantId,
        'amount' => $amount,
        'transaction_type' => 'credit',
        'customer_id' => $loggedInUserId,
        'wallet_by' => 'superadmin',
        'created_at' => date('Y-m-d H:i:s')
    ]);

    $db->transComplete();

    if ($db->transStatus() === false) {
        session()->setFlashdata('swal_error', 'Transaction failed');
        return redirect()->back();
    }

    session()->setFlashdata('swal_success', 'Wallet transferred successfully');
    return redirect()->to('manage-wallet'); // or the route you want
}




    public function edit($id)
    {
        $data['pagetitle'] = 'Edit Wallet';
        $data['edit'] = $this->usersModel->find($id);
        return view('admin/wallets/edit', $data);
    }

    public function updateold()
    {
        $id = $this->request->getPost('user_id');
        $walletAmount = $this->request->getPost('wallet');

        $this->usersModel->update($id, ['wallet' => $walletAmount]);

        session()->setFlashdata('success', 'Wallet updated successfully.');
        return redirect()->to('manage-wallet');
    }

    public function update()
{
    $session = session();
    $superadminId = $session->get('user_id'); // Logged-in superadmin

    $merchantId = $this->request->getPost('user_id'); // Selected merchant
    $walletAmount = floatval($this->request->getPost('wallet'));

    $userModel = $this->usersModel;
    $walletModel = $this->walletModel;

    // Fetch both users
    $superadmin = $userModel->find($ssuperadminId);
    $merchant = $userModel->find($merchantId);

    if (!$superadmin || !$merchant) {
        session()->setFlashdata('swal_error', 'User not found.');
        return redirect()->back();
    }

    if ($superadmin['wallet'] < $walletAmount) {
        session()->setFlashdata('swal_error', 'Insufficient wallet balance.');
        return redirect()->back();
    }

    // Start DB transaction
    $db = \Config\Database::connect();
    $db->transStart();

    // 1. Deduct from superadmin
    $userModel->update($superadminId, [
        'wallet' => $superadmin['wallet'] - $walletAmount
    ]);

    // 2. Add to merchant
    $userModel->update($merchantId, [
        'wallet' => $merchant['wallet'] + $walletAmount
    ]);

    // 3. Insert DEBIT entry for superadmin
    $walletModel->insert([
        'user_id'          => $superadminId,
        'amount'           => $walletAmount,
        'transaction_type' => 'debit',
        'wallet_by'        => 'superadmin',
        'customer_id'      => $merchantId,
        'created_at'       => date('Y-m-d H:i:s')
    ]);

    // 4. Insert CREDIT entry for merchant
    $walletModel->insert([
        'user_id'          => $merchantId,
        'amount'           => $walletAmount,
        'transaction_type' => 'credit',
        'wallet_by'        => 'superadmin',
        'customer_id'      => $superadminId,
        'created_at'       => date('Y-m-d H:i:s')
    ]);

    // Commit transaction
    $db->transComplete();

    if ($db->transStatus() === false) {
        session()->setFlashdata('swal_error', 'Transaction failed.');
        return redirect()->back();
    }

    session()->setFlashdata('swal_success', 'Wallet transferred successfully.');
    return redirect()->to('manage-wallet');
}


    // SHOW RECHARGE WALLET FORM
public function rechargeForm()
{
    return view('admin/merchant_wallets/recharge', [
        'pagetitle' => 'Recharge Wallet'
    ]);
}

public function saveRecharge()
{
    $session = session();
    $merchantId = $session->get('user_id');

    // Step 1: Get the logged-in merchant
    $merchant = $this->usersModel->find($merchantId);
    if (!$merchant) {
        return redirect()->back()->with('error', 'Merchant not found.');
    }

    // Step 2: Find actual superadmin from DB
    $superadmin = $this->usersModel->where('user_type', 'superadmin')->first();
    if (!$superadmin) {
        return redirect()->back()->with('error', 'Superadmin not found.');
    }
    $superadminId = $superadmin['user_id'];

    // Step 3: Get form data
    $amount           = floatval($this->request->getPost('amount'));
    $transactionDate  = $this->request->getPost('transaction_date');
    $mode             = $this->request->getPost('payment_mode');
    $utr_no           = $this->request->getPost('utr_no');
    $cheque_no        = $this->request->getPost('cheque_no');
    $beneficiary      = $this->request->getPost('beneficiary');
    $account_no       = $this->request->getPost('account_no');
    $bank_name        = $this->request->getPost('bank_name');
    $branch           = $this->request->getPost('branch_address');

    // Step 4: Basic validation
    if (!$amount || !$transactionDate || !$mode) {
        return redirect()->back()->with('error', 'Please fill all required fields.');
    }

    // Step 5: Begin DB transaction
    $db = \Config\Database::connect();
    $db->transStart();

    // Step 6: Update merchant wallet (deduct amount)
    $newMerchantBalance = floatval($merchant['wallet']) - $amount;
   
    // $this->usersModel->update($merchantId, ['wallet' => $newMerchantBalance]);

    // Step 7: Update superadmin wallet (add amount)
    $newSuperadminBalance = floatval($superadmin['wallet']) + $amount;
    // $this->usersModel->update($superadminId, ['wallet' => $newSuperadminBalance]);

    // Step 8: Insert DEBIT entry for merchant
    $this->walletModel->insert([
        'user_id'          => $merchantId,
        'amount'           => $amount,
        'transaction_type' => 'Pending',
        'wallet_by'        => 'Pending',
        'customer_id'      => $superadminId,
        'created_at'       => $transactionDate . ' ' . date('H:i:s'),
    ]);

    // Step 9: Insert CREDIT entry for superadmin
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
        return redirect()->back()->with('error', 'Transaction failed.');
    }

    return redirect()->to('manage-wallet/recharge-history')->with('success', 'Recharge request submitted successfully. It will be credited within 24 hours.!');
}




// RECHARGE HISTORY
public function rechargeHistory()
{
    $session = session();
    $userId = $session->get('user_id');

    $wallets = $this->walletModel
        ->where('user_id', $userId)
        ->where('transaction_type', 'credit')
        ->orderBy('created_at', 'DESC')
        ->findAll();

    return view('admin/merchant_wallets/recharge_history', [
        'pagetitle' => 'Recharge History',
        'wallets' => $wallets
    ]);
}

public function fetchNotifications()
{
    $session = session();
    $loggedInUserId = $session->get('user_id');
    $userType = $session->get('user_type');

    // // Only for superadmin
    // if (strtolower($userType) !== 'superadmin') {
    //     return $this->response->setJSON(['count' => 0, 'notifications' => []]);
    // }

    $walletModel = $this->walletModel;

    // Get transactions where superadmin is user_id or customer_id AND status is pending
    $notifications = $walletModel
        ->where('status', 'pending')
        ->groupStart()
            ->where('user_id', $loggedInUserId)
            // ->orWhere('customer_id', $loggedInUserId)
        ->groupEnd()
        ->orderBy('created_at', 'DESC')
        ->findAll();

    return $this->response->setJSON([
        'count' => count($notifications),
        'notifications' => $notifications
    ]);
}
public function markNotificationDone($id)
{
    // Check if it's an AJAX request
    if ($this->request->isAJAX()) {
        $updated = $this->walletModel->update($id, ['status' => 'done']);

        if ($updated) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Update failed']);
        }
    }

    // Fallback for non-AJAX request
    $this->walletModel->update($id, ['status' => 'done']);
    return redirect()->back()->with('success', 'Notification marked as done.');
}

public function approveRecharge($id)
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
    }

    $walletEntry = $this->walletModel->find($id);

    if (!$walletEntry || $walletEntry['wallet_by'] !== 'Merchant Recharge') {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Recharge not found or invalid.']);
    }

    // Check if already approved
    $superadmin = $this->usersModel->where('user_type', 'superadmin')->first();
    if (!$superadmin) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Superadmin not found.']);
    }

    // Prevent double-credit
    $existing = $this->walletModel->where([
        'user_id' => $superadmin['user_id'],
        'customer_id' => $walletEntry['user_id'],
        'amount' => $walletEntry['amount'],
        'transaction_type' => 'credit',
        'wallet_by' => 'Merchant Recharge'
    ])->first();

    if ($existing) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Already approved.']);
    }

    // Insert credit entry to superadmin wallet
    // $this->walletModel->insert([
    //     'user_id' => $superadmin['user_id'],
    //     'customer_id' => $walletEntry['user_id'],
    //     'amount' => $walletEntry['amount'],
    //     'transaction_type' => 'credit',
    //     'wallet_by' => 'Merchant Recharge',
    //     'created_at' => date('Y-m-d H:i:s')
    // ]);
     $updated = $this->walletModel
    ->where('id', $id)
    ->where('wallet_by', 'Merchant Recharge')
    ->set([
        'wallet_by' => '',
        'status'    => 'done'
    ])
    ->update();

         if (!$updated) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update wallet entry.']);
    }

    // Update superadmin balance
    $this->usersModel->update($superadmin['user_id'], [
        'wallet' => floatval($superadmin['wallet']) + floatval($walletEntry['amount'])
    ]);

    return $this->response->setJSON(['status' => 'success']);
}




}
