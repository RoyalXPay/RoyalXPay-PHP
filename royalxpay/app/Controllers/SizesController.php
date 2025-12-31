<?php

namespace App\Controllers;

use App\Models\SizesModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;

class SizesController extends BaseController
{
    public function index()
    {
        set_title('Sizes List | ' . SITE_NAME);

        $data = [
            'action' => "sizes",
            'pageTitle' => "Sizes List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        $sizesModel = new SizesModel();
        $customPagination = new Pagination();

        // Collect search criteria
        $searchFields = ['txtsearch'];
        foreach ($searchFields as $field) {
            $value = $this->request->getGet($field);
            if ($value) {
                $data[$field] = $value;
                $data['searchArray'][$field] = $value;
            }
        }

        // Pagination logic
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 50;  // You can change the limit as needed
        $totalRecord = $sizesModel->getSizes($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetching results for the current page
        $data['results'] = $sizesModel->getSizes($data['searchArray'], $startLimit, $Limit);

        return view('admin/sizes/index', $data);
    }

    public function edit($id)
    {
        $sizesModel = new SizesModel();
        $size = $sizesModel->find($id);
        if ($size) {
            return $this->response->setJSON($size);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Size not found']);
        }
    }

    public function save()
    {
        $validation = \Config\Services::validation();

        // Validation for the size fields
        if (!$this->validate([
            'size_in_inches' => 'required',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $sizesModel = new SizesModel();
        $id = $this->request->getPost('size_id');

        $data = [
            'size_in_inches' => trim($this->request->getPost('size_in_inches')),
        ];

        if ($id) {
            // If the size exists, update it
            $data['size_id'] = $id;
            if ($sizesModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Size updated successfully',
                ]);
            }
        } else {
            // If it's a new size, insert it
            if ($sizesModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Size added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save Size. Please try again.',
        ]);
    }

    public function delete($id)
    {
        if (is_numeric($id) && !empty($id)) {
            try {
                $sizesModel = new SizesModel();
                $size = $sizesModel->find($id);

                if (!$size) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Size not found.',
                    ]);
                }

                $sizesModel->where('size_id', $id)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Size deleted successfully.',
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
                'message' => 'Invalid size ID.',
            ]);
        }
    }
}
