<?php

namespace App\Controllers;

use App\Models\UsersModel;   // ✅ FIXED
use App\Models\WalletModel;  // ✅ Already correct
use App\Controllers\BaseController;

class MerchantWalletController extends BaseController
{
    protected $session;
    protected $usersModel;

    public function __construct()
    {
        $this->session = session();
        $this->usersModel = new UsersModel(); // ✅ FIXED (capital "U")
    }

   public function history($userId)
    {
        $walletModel = new WalletModel();
        $userModel = new UsersModel();
        $session = session();

        $user = $userModel->find($userId);
        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to('manage-wallet');
        }

        // Filters from GET
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $search = $this->request->getGet('search');
        $type = $this->request->getGet('type');
        $amount = $this->request->getGet('amount');

        // $builder = $walletModel->select('wallet.*, u.name, u.phone')
        // ->join('users u', 'u.user_id = wallet.user_id', 'left')
        // ->where('wallet.user_id', $userId);

        $builder = $walletModel
        ->select('wallet.*, 
                u.name AS user_name, u.phone AS user_phone, 
                c.name AS customer_name, c.phone AS customer_phone')
        ->join('users u', 'u.user_id = wallet.user_id', 'left')
        ->join('users c', 'c.user_id = wallet.customer_id', 'left')
        ->where('wallet.user_id', $userId);


        if ($startDate) {
            $builder->where('DATE(wallet.created_at) >=', $startDate);
        }

        if ($endDate) {
            $builder->where('DATE(wallet.created_at) <=', $endDate);
        }

        if ($search) {
            $builder->groupStart()
                ->like('u.name', $search)
                ->orLike('u.phone', $search)
                ->groupEnd();
        }

        if ($type) {
            $builder->where('wallet.transaction_type', $type);
        }

        if ($amount) {
            $builder->where('wallet.amount', $amount);
        }

        $transactions = $builder->orderBy('wallet.created_at', 'DESC')->findAll();

        return view('admin/merchant_wallets/index', [
            'transactions' => $transactions,
            'user' => $user,
            'pagetitle' => 'Wallet Transaction History',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'search' => $search,
            'type' => $type,
            'amount' => $amount,
        ]);
    }

}
