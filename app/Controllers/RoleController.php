<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\UsersModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;

class RoleController extends BaseController
{
    public function index()
    {
        set_title('Role Details List | ' . SITE_NAME);

        $data = [
            'action' => "roles",
            'pageTitle' => "Role Details List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        $roleModel = new RoleModel();
        $customPagination = new Pagination();

        $searchFields = ['txtsearch'];
        foreach ($searchFields as $field) {
            $value = $this->request->getGet($field);
            if ($value) {
                $data[$field] = $value;
                $data['searchArray'][$field] = $value;
            }
        }

        
        $data['rolePermission'] = ['view', 'add', 'edit', 'delete'];
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 50;
        $totalRecord = $roleModel->getRoles($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $roleModel->getRoles($data['searchArray'], $startLimit, $Limit);

            $usersModel = new UsersModel();
    $data['merchantdetails'] = $usersModel
        ->select('user_id, name')
        ->where('user_type', 'merchant')
        ->orderBy('name', 'ASC')
        ->findAll();
        return view('settings/roles/index', $data);
    }

    public function edit($id)
    {
        $roleModel = new RoleModel();
        $role = $roleModel->find($id);
        if ($role) {
            if (!empty($role['role_permission'])) {
                $role['role_permission'] = json_decode($role['role_permission'], true);
            } else {
                $role['role_permission'] = [];
            }
            return $this->response->setJSON($role);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Role details not found']);
        }
    }

   public function save()
{
    $validation = \Config\Services::validation();

    if (!$this->validate([
        'role_name' => 'required', // contains merchant_id|merchant_name
        'description' => 'permit_empty',
        'role_permission' => 'required'
    ])) {
        return $this->response->setJSON([
            'status' => 'error',
            'errors' => $validation->getErrors(),
        ]);
    }

    $roleModel = new RoleModel();
    $id = $this->request->getPost('role_id');

    // Handle permissions
    $permissions = $this->request->getPost('role_permission');
    $permissionsJson = json_encode($permissions);

    // Handle merchants
    $merchants = $this->request->getPost('role_name'); 
    $merchantArray = [];

    if (is_array($merchants)) {
        foreach ($merchants as $merchant) {
            list($merchant_id, $merchant_name) = explode('|', $merchant);
            $merchantArray[] = [
                'merchant_id'   => $merchant_id,
                'merchant_name' => $merchant_name
            ];
        }
    } else {
        list($merchant_id, $merchant_name) = explode('|', $merchants);
        $merchantArray[] = [
            'merchant_id'   => $merchant_id,
            'merchant_name' => $merchant_name
        ];
    }

    $merchantsJson = json_encode($merchantArray);

    // 👇 If you want to store as JSON in DB
    // $data = [
    //     'role_name'       => $merchantsJson,  // full JSON array
    //     'description'     => trim($this->request->getPost('description')),
    //     'role_permission' => $permissionsJson,
    // ];

    // Or 👇 If you want to save only the first merchant (common in many systems)
   
    $data = [
        'role_name'       => $merchantArray[0]['merchant_name'], 
        'merchant_id'     => $merchantArray[0]['merchant_id'], 
        'description'     => trim($this->request->getPost('description')),
        'role_permission' => $permissionsJson,
    ];
    

    if ($id) {
        if ($roleModel->update($id, $data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Role details updated successfully',
            ]);
        }
    } else {
        if ($roleModel->save($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Role details added successfully',
            ]);
        }
    }

    return $this->response->setJSON([
        'status' => 'error',
        'message' => 'Failed to save role details. Please try again.',
    ]);
}

    public function delete($id)
    {
        if (is_numeric($id) && !empty($id)) {
            try {
                $roleModel = new RoleModel();
                $role = $roleModel->find($id);

                if (!$role) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Role details not found.',
                    ]);
                }

                $roleModel->where('role_id', $id)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Role details deleted successfully.',
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
