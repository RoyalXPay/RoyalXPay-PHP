<?php

namespace App\Controllers;

use App\Models\ProductTypeModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;

class ProductTypeController extends BaseController
{
    public function index()
    {
        set_title('Product Type List | ' . SITE_NAME);

        $data = [
            'action' => "product-types",
            'pageTitle' => "Product Type List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        $productTypeModel = new ProductTypeModel();
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
        $totalRecord = $productTypeModel->getProductTypes($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetch the actual product types
        $data['results'] = $productTypeModel->getProductTypes($data['searchArray'], $startLimit, $Limit);

        return view('admin/basics/products/index', $data);
    }

    public function edit($id)
    {
        $productTypeModel = new ProductTypeModel();
        $productType = $productTypeModel->find($id);
        if ($productType) {
            return $this->response->setJSON($productType);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Product Type not found']);
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

        $productTypeModel = new ProductTypeModel();
        // Get product type ID if it's an update
        $id = $this->request->getPost('id');

        // Get the data and trim spaces from name and description
        $data = [
            'name' => trim($this->request->getPost('name')),
            // 'description' => trim($this->request->getPost('description')),
        ];

        if ($id) {
            // Update existing product type
            $data['id'] = $id;
            if ($productTypeModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Product Type updated successfully',
                ]);
            }
        } else {
            // Create new product type
            if ($productTypeModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Product Type added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save product type. Please try again.',
        ]);
    }

    public function delete($id)
    {
        // Validate that $id is numeric and not empty
        if (is_numeric($id) && !empty($id)) {
            try {
                // Check if the record exists
                $productTypeModel = new ProductTypeModel();
                $productType = $productTypeModel->find($id);

                // If no record is found, return an error response
                if (!$productType) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Product Type not found.',
                    ]);
                }

                // Proceed with deletion
                $productTypeModel->where('id', $id)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Product Type deleted successfully.',
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
