<?php

namespace App\Controllers;

use App\Models\TouchTypesModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;

class TouchTypesController extends BaseController
{
    public function index()
    {
        set_title('Touch Types List | ' . SITE_NAME);

        $data = [
            'action' => "touch-types",
            'pageTitle' => "Touch Types List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        $touchTypesModel = new TouchTypesModel();
        $customPagination = new Pagination();

        $searchFields = ['txtsearch'];
        foreach ($searchFields as $field) {
            $value = $this->request->getGet($field);
            if ($value) {
                $data[$field] = $value;
                $data['searchArray'][$field] = $value;
            }
        }

        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 50;
        $totalRecord = $touchTypesModel->getTouchTypes($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $touchTypesModel->getTouchTypes($data['searchArray'], $startLimit, $Limit);

        return view('admin/touch/index', $data);
    }

    public function edit($id)
    {
        $touchTypesModel = new TouchTypesModel();
        $touchType = $touchTypesModel->find($id);
        if ($touchType) {
            return $this->response->setJSON($touchType);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Touch Type not found']);
        }
    }

    public function save()
    {
        $validation = \Config\Services::validation();

        if (!$this->validate([
            'name' => 'required',
            // 'description' => 'required',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $touchTypesModel = new TouchTypesModel();
        $id = $this->request->getPost('touch_id');

        $data = [
            'name' => trim($this->request->getPost('name')),
            // 'description' => trim($this->request->getPost('description')),
        ];

        if ($id) {
            $data['touch_id'] = $id;
            if ($touchTypesModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Touch Type updated successfully',
                ]);
            }
        } else {
            if ($touchTypesModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Touch Type added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save Touch Type. Please try again.',
        ]);
    }

    public function delete($id)
    {
        if (is_numeric($id) && !empty($id)) {
            try {
                $touchTypesModel = new TouchTypesModel();
                $touchType = $touchTypesModel->find($id);

                if (!$touchType) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Touch Type not found.',
                    ]);
                }

                $touchTypesModel->where('touch_id', $id)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Touch Type deleted successfully.',
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
