<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\ModuleModel;
use App\Models\SubmoduleModel;
use App\Models\PermissionModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;

class PermissionController extends BaseController
{
    public function index()
    {
        set_title('Permission Details List | ' . SITE_NAME);

        $data = [
            'action' => "permissions",
            'pageTitle' => "Permission Details List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        $customPagination = new Pagination();
        $moduleModel = new ModuleModel();
        $submoduleModel = new SubmoduleModel();
        $roleModel = new RoleModel();
        $permissionModel = new PermissionModel();

        // Search Fields
        $searchFields = ['txtsearch', 'role_id', 'module_id', 'submodule_id', 'permission_type'];
        foreach ($searchFields as $field) {
            $value = $this->request->getGet($field);
            if ($value) {
                $data[$field] = $value;
                $data['searchArray'][$field] = $value;
            }
        }

        // Pagination Logic
        $data['roles'] = $roleModel->findAll();
        $data['modules'] = $moduleModel->findAll();
        $data['submodules'] = $submoduleModel->findAll();

        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 50;
        $totalRecord = $permissionModel->getPermissionsDetails($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetching the Results
        $data['results'] = $permissionModel->getPermissionsDetails($data['searchArray'], $startLimit, $Limit);

        return view('settings/permissions/index', $data);
    }

    public function edit($id)
    {
        $permissionModel = new PermissionModel();
        $permission = $permissionModel->find($id);
        if ($permission) {
            return $this->response->setJSON($permission);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permission details not found']);
        }
    }

    public function save()
    {
        $validation = \Config\Services::validation();

        if (!$this->validate([
            'role_id' => 'required',
            'module_id' => 'required',
            'submodule_id' => 'required',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $permissionModel = new PermissionModel();
        $id = $this->request->getPost('id');
  $roleModel       = new RoleModel();
         // Get role details to fetch permission type
        $roleId = $this->request->getPost('role_id');
        $role   = $roleModel->find($roleId);
      

        if (!$role) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Invalid role selected',
            ]);
        }

      $data = [
    'role_id'        => $this->request->getPost('role_id'),
    'module_id'      => implode(',', (array) $this->request->getPost('module_id')),
    'submodule_id'   => implode(',', (array) $this->request->getPost('submodule_id')),
    'permission_type'=> $role['role_permission'] ,
];


        if ($id) {
            if ($permissionModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Permission details updated successfully',
                ]);
            }
        } else {
            if ($permissionModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Permission details added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save permission details. Please try again.',
        ]);
    }

    public function delete($id)
    {
        if (is_numeric($id) && !empty($id)) {
            try {
                $permissionModel = new PermissionModel();
                $permission = $permissionModel->find($id);

                if (!$permission) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Permission details not found.',
                    ]);
                }

                $permissionModel->where('id', $id)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Permission details deleted successfully.',
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'An unexpected error occurred. Please try again.',
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
