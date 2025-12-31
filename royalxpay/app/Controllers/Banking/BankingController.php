<?php

namespace App\Controllers\Banking;



use App\Models\EmployeeModel;

use App\Models\EmployeeSalaryModel;
use App\Controllers\BaseController;

class BankingController extends BaseController
{
      protected $session;
    protected $employeeModel;
    protected $isAdminLoggedIn;

    public function __construct()
    {
        $this->session = session();
        $this->salaryModel = new EmployeeSalaryModel();
        $this->isAdminLoggedIn = $this->session->get('isAdminLoggedIn');
    }

    public function index()
    {
        return view('admin/banking/transfer_to_bank');
    }

    public function transferToBank()
    {
        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');
        $txtsearch = $this->request->getGet('txtsearch');

        $transactionModel = new \App\Models\BankingModel();

        $filters = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'txtsearch' => $txtsearch
        ];
         $isLoggedIn = $this->session->get('logged_in');

        $data = [
            'transactions' => $transactionModel->getFilteredTransactions($filters,$isLoggedIn),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'txtsearch' => $txtsearch,
            'pager' => $transactionModel->pager
        ];

        return view('admin/banking/transfer_history', $data);
    }


    public function transferToMobile()
    {
        return view('admin/banking/transfer_to_mobile');
    }

    public function transactionHistory()
    {
         $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');
        $txtsearch = $this->request->getGet('txtsearch');

        $transactionModel = new \App\Models\BankingModel();

        $filters = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'txtsearch' => $txtsearch
        ];
         $isLoggedIn = $this->session->get('logged_in');

        $data = [
            'transactions' => $transactionModel->getFilteredTransactions($filters,$isLoggedIn),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'txtsearch' => $txtsearch,
            'pager' => $transactionModel->pager
        ];

        return view('admin/banking/transaction_history', $data);
    }
}
