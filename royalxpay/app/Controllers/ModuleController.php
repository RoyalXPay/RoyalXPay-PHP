<?php

namespace App\Controllers;

use App\Models\ModuleModel;
use App\Libraries\Pagination;
use App\Models\SubmoduleModel;
use App\Controllers\BaseController;

class ModuleController extends BaseController
{
    public function index()
    {
        set_title('Module Details List | ' . SITE_NAME);

        $data = [
            'action' => "modules",
            'pageTitle' => "Module Details List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        $moduleModel = new ModuleModel();
        $customPagination = new Pagination();

        // Search Fields
        $searchFields = ['txtsearch'];
        foreach ($searchFields as $field) {
            $value = $this->request->getGet($field);
            if ($value) {
                $data[$field] = $value;
                $data['searchArray'][$field] = $value;
            }
        }

        // Pagination Logic
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 50;
        $totalRecord = $moduleModel->getModules($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetching the Results
        $data['results'] = $moduleModel->getModules($data['searchArray'], $startLimit, $Limit);

        return view('settings/modules/index', $data);
    }

    public function edit($id)
    {
        $moduleModel = new ModuleModel();
        $module = $moduleModel->find($id);
        if ($module) {
            return $this->response->setJSON($module);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Module details not found']);
        }
    }

    public function save()
    {
        $validation = \Config\Services::validation();

        if (!$this->validate([
            'module_name' => 'required',
            'module_description' => 'required',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $moduleModel = new ModuleModel();
        $id = $this->request->getPost('module_id');

        $data = [
            'module_name' => trim($this->request->getPost('module_name')),
            'module_description' => trim($this->request->getPost('module_description')),
        ];

        if ($id) {
            if ($moduleModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Module details updated successfully',
                ]);
            }
        } else {
            if ($moduleModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Module details added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save module details. Please try again.',
        ]);
    }

    public function delete($id)
    {
        if (is_numeric($id) && !empty($id)) {
            try {
                $moduleModel = new ModuleModel();
                $module = $moduleModel->find($id);

                if (!$module) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Module details not found.',
                    ]);
                }

                $moduleModel->where('module_id', $id)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Module details deleted successfully.',
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

    public function getSubmodules()
    {
        $moduleId = $this->request->getGet('module_id');

        if (!$moduleId) {
            return $this->response->setJSON([]);
        }

        $submoduleModel = new SubmoduleModel();
        $submodules = $submoduleModel->whereIn('module_id', $moduleId)->findAll();

        return $this->response->setJSON($submodules);
    }
}
