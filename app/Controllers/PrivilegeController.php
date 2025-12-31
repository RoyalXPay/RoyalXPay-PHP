<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PrivilegeModel;
use App\Models\ModuleModel;
use App\Models\SubmoduleModel;
use App\Models\SubSubmoduleModel;
use App\Models\UsersModel;

class PrivilegeController extends BaseController
{
    public function index()
    {
        $privilegeModel    = new PrivilegeModel();
        $userModel         = new UsersModel();
        $moduleModel       = new ModuleModel();
        $submoduleModel    = new SubmoduleModel();
        $subSubmoduleModel = new SubSubmoduleModel();

        return view('admin/privileges/index', [
            'merchants'      => $userModel->where('user_type', 'merchant')->findAll(),
            'privileges'     => $privilegeModel->getAllPrivileges(),
            'modules'        => $moduleModel->findAll(),
            'submodules'     => $submoduleModel->findAll(),
            'sub_submodules' => $subSubmoduleModel->findAll()
        ]);
    }

    public function create()
    {
        $moduleModel       = new ModuleModel();
        $submoduleModel    = new SubmoduleModel();
        $subSubmoduleModel = new SubSubmoduleModel();
        $userModel         = new UsersModel();

        return view('admin/privileges/create', [
            'merchants'      => $userModel->where('user_type', 'merchant')->findAll(),
            'modules'        => $moduleModel->findAll(),
            'submodules'     => $submoduleModel->findAll(),
            'sub_submodules' => $subSubmoduleModel->findAll()
        ]);
    }

public function store()
{
    $data = $this->request->getPost();
    $privilegeModel     = new PrivilegeModel();
    $moduleModel        = new ModuleModel();
    $submoduleModel     = new SubmoduleModel();
    $subSubmoduleModel  = new SubSubmoduleModel();

    $moduleIds       = $data['module_id'] ?? [];
    $submoduleIds    = $data['submodule_id'] ?? [];
    $subSubmoduleIds = $data['sub_submodule_id'] ?? [];

    foreach ($moduleIds as $m) {
        // ✅ Get all submodules of this module
        $allSubmodules = $submoduleModel->where('module_id', $m)->findAll();
        $allSubmoduleIds = array_column($allSubmodules, 'id');

        // filter selected submodules only belonging to this module
        $selectedSubmodules = array_filter($submoduleIds, fn($sid) => in_array($sid, $allSubmoduleIds));

        // if none selected, auto select all
        if (empty($selectedSubmodules)) {
            $selectedSubmodules = $allSubmoduleIds ?: [null];
        }

        foreach ($selectedSubmodules as $s) {
            // ✅ Get all sub-submodules for this submodule
            $allSubSubs = $s ? $subSubmoduleModel->where('submodule_id', $s)->findAll() : [];
            $allSubSubIds = array_column($allSubSubs, 'id');

            // filter selected sub-submodules of this submodule
            $selectedSubSubs = array_filter($subSubmoduleIds, fn($ssid) => in_array($ssid, $allSubSubIds));

            // if none selected, auto select all
            if (empty($selectedSubSubs)) {
                $selectedSubSubs = $allSubSubIds ?: [null];
            }

            foreach ($selectedSubSubs as $ss) {
                $saveData = [
                    'merchant_id'      => $data['merchant_id'],
                    'module_id'        => $m,
                    'submodule_id'     => $s,
                    'sub_submodule_id' => $ss,
                    'can_add'          => isset($data['can_add']) ? 1 : 0,
                    'can_edit'         => isset($data['can_edit']) ? 1 : 0,
                    'can_delete'       => isset($data['can_delete']) ? 1 : 0,
                    // ✅ default view = 1 if nothing selected
                    'can_view'         => isset($data['can_add']) || isset($data['can_edit']) || isset($data['can_delete']) || isset($data['can_download']) || isset($data['can_view']) ? (isset($data['can_view']) ? 1 : 0) : 1,
                    'can_download'     => isset($data['can_download']) ? 1 : 0,
                    'updated_at'       => date('Y-m-d H:i:s'),
                ];

                // check if exists
                $existing = $privilegeModel
                    ->where('merchant_id', $data['merchant_id'])
                    ->where('module_id', $m)
                    ->where('submodule_id', $s)
                    ->where('sub_submodule_id', $ss)
                    ->first();

                if ($existing) {
                    $privilegeModel->update($existing['id'], $saveData);
                } else {
                    $saveData['created_at'] = date('Y-m-d H:i:s');
                    $privilegeModel->insert($saveData);
                }
            }
        }
    }

    return redirect()->to('admin/privileges')->with('success', 'Privileges saved successfully.');
}

public function merchantPrivileges()
{
    $merchantId = $this->request->getGet('merchant_id');

    $data['merchants'] = $this->merchantModel->findAll();
    $data['selectedMerchant'] = null;
    $data['privileges'] = [];

    if ($merchantId) {
        $data['selectedMerchant'] = $this->merchantModel->find($merchantId);

        $data['privileges'] = $this->db->table('privileges p')
            ->select('p.*, m.name as merchant_name, mo.name as module_name, sm.name as submodule_name, ssm.name as sub_submodule_name')
            ->join('users m', 'm.user_id = p.merchant_id')
            ->join('modules mo', 'mo.id = p.module_id')
            ->join('submodules sm', 'sm.id = p.submodule_id', 'left')
            ->join('subsubmodules ssm', 'ssm.id = p.sub_submodule_id', 'left')
            ->where('p.merchant_id', $merchantId)
            ->get()->getResultArray();
    }

    return view('admin/privileges/merchant_privileges', $data);
}

public function view()
{
    $UsersModel = new \App\Models\UsersModel();
    $data['merchants'] = $UsersModel->findAll();
    
    return view('admin/privileges/view', $data);
}



public function viewMerchantPrivileges()
{
    $userModel     = new \App\Models\UsersModel();
    $moduleModel   = new \App\Models\ModuleModel();
    $submoduleModel = new \App\Models\SubmoduleModel();
    $subSubmoduleModel = new \App\Models\SubSubmoduleModel();
    $privilegeModel = new \App\Models\PrivilegeModel();

    $merchantId = $this->request->getGet('merchant_id');

    $data['merchants'] = $userModel->where('user_type', 'merchant')->findAll();
    $data['selectedMerchant'] = null;
    $data['privileges'] = [];
    $data['modules'] = $moduleModel->findAll();
    $data['submodules'] = $submoduleModel->findAll();
    $data['subsubmodules'] = $subSubmoduleModel->findAll();

    if ($merchantId) {
        $data['selectedMerchant'] = $userModel->find($merchantId);

        $data['privileges'] = $privilegeModel->select('privileges.*, modules.name as module_name, submodules.name as submodule_name, subsubmodules.name as sub_submodule_name')
            ->join('modules', 'modules.id = privileges.module_id', 'left')
            ->join('submodules', 'submodules.id = privileges.submodule_id', 'left')
            ->join('subsubmodules', 'subsubmodules.id = privileges.sub_submodule_id', 'left')
            ->where('privileges.merchant_id', $merchantId)
            ->findAll();
    }

    return view('admin/privileges/merchant_privileges', $data);
}

public function updateMerchantPrivileges()
{
    $data = $this->request->getPost();
    $privilegeModel = new \App\Models\PrivilegeModel();

    foreach ($data['privilege_id'] as $id => $vals) {
        $saveData = [
            'can_add' => isset($vals['can_add']) ? 1 : 0,
            'can_edit' => isset($vals['can_edit']) ? 1 : 0,
            'can_delete' => isset($vals['can_delete']) ? 1 : 0,
            'can_view' => isset($vals['can_view']) ? 1 : 0,
            'can_download' => isset($vals['can_download']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $privilegeModel->update($id, $saveData);
    }

    return redirect()->back()->with('success', 'Privileges updated successfully.');
}



}
