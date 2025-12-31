<?php

namespace App\Controllers;

use App\Models\GlassTypesModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;

class GlassTypesController extends BaseController
{
    public function index()
    {
        set_title('Glass Types List | ' . SITE_NAME);

        $data = [
            'action' => "glass-types",
            'pageTitle' => "Glass Types List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        $glassTypesModel = new GlassTypesModel();
        $customPagination = new Pagination();

        // Get filter values from the GET request
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
        $totalRecord = $glassTypesModel->getGlassTypes($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetch the actual glass types
        $data['results'] = $glassTypesModel->getGlassTypes($data['searchArray'], $startLimit, $Limit);

        return view('admin/glass/index', $data);
    }

    public function edit($id)
    {
        $glassTypesModel = new GlassTypesModel();
        $glassType = $glassTypesModel->find($id);
        if ($glassType) {
            return $this->response->setJSON($glassType);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Glass type not found']);
        }
    }

    public function save()
    {
        $validation = \Config\Services::validation();

        // Validate input
        if (!$this->validate([
            'name' => 'required',
            // 'description' => 'required',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $glassTypesModel = new GlassTypesModel();
        // Get glass type ID if it's an update
        $id = $this->request->getPost('glass_id');

        // Get the data and trim spaces from name and description
        $data = [
            'name' => trim($this->request->getPost('name')),
            // 'description' => trim($this->request->getPost('description')),
        ];

        if ($id) {
            // Update existing glass type
            $data['glass_id'] = $id;
            if ($glassTypesModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Glass type updated successfully',
                ]);
            }
        } else {
            // Create new glass type
            if ($glassTypesModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Glass type added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save glass type. Please try again.',
        ]);
    }

    public function delete($id)
    {
        // Validate that $id is numeric and not empty
        if (is_numeric($id) && !empty($id)) {
            try {
                // Check if the record exists
                $glassTypesModel = new GlassTypesModel();
                $glassType = $glassTypesModel->find($id);

                // If no record is found, return an error response
                if (!$glassType) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Glass type not found.',
                    ]);
                }

                // Proceed with deletion
                $glassTypesModel->where('glass_id', $id)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Glass type deleted successfully.',
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
