<?php

namespace App\Controllers\Api;
use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\AadcPaymentModel;

class AadcController extends BaseController
{
    public function index()
    {
        return view('admin/Api/aadc/index', [
            'pagetitle' => 'AADC Payment'
        ]);
    }

    public function fetchWalletUsers()
    {
        $type = $this->request->getPost('wallet_type');

        $userModel = new UserModel();
        $users = $userModel->where('user_type', $type)->where('status', 'active')->findAll();

        return $this->response->setJSON(['status' => true, 'data' => $users]);
    }

    public function processPayment()
    {
        $data = $this->request->getPost();

        $txnId = date('ymdHisv') . rand(0,9) . '3684'; // Format + merchant ID

        // Call your MBME payment API here
        $apiResponse = $this->callMbmePayApi($data, $txnId);

        $model = new AadcPaymentModel();
        $model->insert([
            'user_id' => $data['user_id'],
            'wallet_type' => $data['wallet_type'],
            'amount' => $data['amount'],
            'consumer_number' => $data['consumer_number'],
            'txn_id' => $txnId,
            'status' => $apiResponse['status'],
            'response' => json_encode($apiResponse),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return view('admin/api/aadc/success', [
            'txn_id' => $txnId,
            'status' => $apiResponse['status']
        ]);
    }

    private function callMbmePayApi($data, $txnId)
    {
        // Simulate a real MBME API call
        return [
            'status' => 'Success',
            'msg' => 'Payment completed',
            'transaction_id' => $txnId,
        ];
    }

    public function report()
    {
        $model = new AadcPaymentModel();
        $payments = $model->orderBy('created_at', 'DESC')->findAll();

        return view('admin/api/aadc/report', [
            'pagetitle' => 'AADC Payment Reports',
            'results' => $payments,
        ]);
    }
}
