<?php

namespace App\Controllers;

use App\Models\ModuleModel;
use App\Libraries\Pagination;
use App\Models\SubmoduleModel;
use App\Controllers\BaseController;

class SubmoduleController extends BaseController
{
    public function index()
    {
        set_title('Submodule Details List | ' . SITE_NAME);

        $data = [
            'action' => "submodules",
            'pageTitle' => "Submodule Details List",
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

        // Search Fields
        $searchFields = ['txtsearch', 'module_id'];
        foreach ($searchFields as $field) {
            $value = $this->request->getGet($field);
            if ($value) {
                $data[$field] = $value;
                $data['searchArray'][$field] = $value;
            }
        }

        // Pagination Logic
        $data['modules'] = $moduleModel->findAll();
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 50;
        $totalRecord = $submoduleModel->getSubmodulesDetails($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetching the Results
        $data['results'] = $submoduleModel->getSubmodulesDetails($data['searchArray'], $startLimit, $Limit);

        return view('settings/submodules/index', $data);
    }

    public function edit($id)
    {
        $submoduleModel = new SubmoduleModel();
        $submodule = $submoduleModel->find($id);
        if ($submodule) {
            return $this->response->setJSON($submodule);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Submodule details not found']);
        }
    }

    public function save()
    {
        $validation = \Config\Services::validation();

        if (!$this->validate([
            'submodule_name' => 'required',
            'submodule_description' => 'required',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $submoduleModel = new SubmoduleModel();
        $id = $this->request->getPost('submodule_id');

        $data = [
            'submodule_name' => trim($this->request->getPost('submodule_name')),
            'submodule_description' => trim($this->request->getPost('submodule_description')),
            'module_id' => trim($this->request->getPost('module_id')),
        ];

        if ($id) {
            if ($submoduleModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Submodule details updated successfully',
                ]);
            }
        } else {
            if ($submoduleModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Submodule details added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save submodule details. Please try again.',
        ]);
    }

    public function delete($id)
    {
        if (is_numeric($id) && !empty($id)) {
            try {
                $submoduleModel = new SubmoduleModel();
                $submodule = $submoduleModel->find($id);

                if (!$submodule) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Submodule details not found.',
                    ]);
                }

                $submoduleModel->where('submodule_id', $id)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Submodule details deleted successfully.',
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
