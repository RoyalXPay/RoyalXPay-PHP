<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\UsersModel;

class TransactionReport extends BaseController
{

 public function __construct()
    {
  $this->initializePrivileges(); // This calls method from BaseController
        helper(['form', 'url']);
        session();
    }
    public function index()
    {
        $txnModel = new TransactionModel();
        $userModel = new UsersModel();

        $filters = [
            'start_date'   => $this->request->getGet('start_date'),
            'end_date'     => $this->request->getGet('end_date'),
            'username'     => $this->request->getGet('username'),
            'mobile'       => $this->request->getGet('mobile'),
            'email'        => $this->request->getGet('email'),
            'merchant'     => $this->request->getGet('merchant'),
            'employee'     => $this->request->getGet('employee'),
            'txn_id'       => $this->request->getGet('txn_id'),
            'subscription' => $this->request->getGet('subscription'),
            'city'         => $this->request->getGet('city'),
            'location'     => $this->request->getGet('location'),
            'Payment_type'    => $this->request->getGet('Payment_type'),
        ];

        $transactions = $txnModel->getFilteredTransactions($filters);

        

        return view('admin/reports/transaction_report', [
            'transactions' => $transactions,
            'filters'      => $filters,
        ]);
    }
}
