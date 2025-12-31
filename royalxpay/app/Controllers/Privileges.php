<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use App\Models\PrivilegeModel;

class PrivilegeController extends BaseController
{
    
    protected $session;
    protected $usersModel;
    protected $privilegeModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
                $privilegeModel = new PrivilegeModel();

        $this->session = \Config\Services::session();
    }
    public function index()
    {

        // Fetch merchants
        $merchants = $this->usersModel->where('user_type', 'Merchant')->findAll();
        $selectedMerchant = $this->request->getGet('merchant_id');

        $privileges = [];
        if ($selectedMerchant) {
            $privRecords = $privilegeModel
                ->where('merchant_id', $selectedMerchant)
                ->findAll();
            foreach ($privRecords as $p) {
                $key = $p['module'].'|'.$p['submodule'].'|'.$p['subsubmodule'];
                $privileges[$key] = $p;
            }
        }

        $moduleTree = [
            "Recharge & Bill Payment" => [
                "Utility Payment" => ["AADC", "Nol Topup"],
                "Topup" => ["Wallet Recharge"]
            ],
            "EMS" => [
                "Employee Tracker" => ["Location", "Attendance"]
            ],
            "Customer Management" => [
                "Customers" => ["Add", "Edit"]
            ]
        ];

        // Pass variables to the view
        return view('admin/privileges/index', [
            'merchants' => $merchants,
            'selectedMerchant' => $selectedMerchant,
            'privileges' => $privileges,
            'moduleTree' => $moduleTree
        ]);
    }

    public function save()
    {
        $privilegeModel = new PrivilegeModel();

        $merchantId = $this->request->getPost('merchant_id');
        $modules    = $this->request->getPost('modules') ?? [];
        $submodules = $this->request->getPost('submodules') ?? [];
        $subsubs    = $this->request->getPost('subsubmodules') ?? [];
        $privileges = $this->request->getPost('privileges') ?? [];

        // delete old privileges
        $privilegeModel->where('merchant_id', $merchantId)->delete();

        foreach ($modules as $i => $module) {
            $priv = $privileges[$i] ?? [];
            $privilegeModel->insert([
                'merchant_id'  => $merchantId,
                'module'       => $module,
                'submodule'    => $submodules[$i] ?? null,
                'subsubmodule' => $subsubs[$i] ?? null,
                'can_add'      => !empty($priv['add']) ? 1 : 0,
                'can_edit'     => !empty($priv['edit']) ? 1 : 0,
                'can_preview'  => !empty($priv['preview']) ? 1 : 0,
                'can_delete'   => !empty($priv['delete']) ? 1 : 0,
                'can_download' => !empty($priv['download']) ? 1 : 0,
            ]);
        }

        return redirect()->to('admin/privilege?merchant_id=' . $merchantId)
                         ->with('success', 'Privileges updated successfully.');
    }
}
