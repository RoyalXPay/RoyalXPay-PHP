<?php

namespace App\Controllers;

use App\Models\TermsAndConditionsModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;

class TermsAndConditionsController extends BaseController
{
    public function index()
    {
        set_title('Terms & Conditions List | ' . SITE_NAME);

        $data = [
            'action' => "terms-and-conditions",
            'pageTitle' => "Terms & Conditions List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        $termsModel = new TermsAndConditionsModel();
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
        $totalRecord = $termsModel->getTermsAndConditions($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetch the actual terms and conditions
        $data['results'] = $termsModel->getTermsAndConditions($data['searchArray'], $startLimit, $Limit);

        return view('admin/terms_and_conditions/index', $data);
    }

    public function edit($id)
    {
        $termsModel = new TermsAndConditionsModel();
        $terms = $termsModel->find($id);
        if ($terms) {
            return $this->response->setJSON($terms);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Terms & Conditions not found']);
        }
    }

    public function save()
    {
        $validation = \Config\Services::validation();

        // Validate input
        if (!$this->validate([
            'title' => 'required',
            'description' => 'required',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $termsModel = new TermsAndConditionsModel();
        // Get terms and conditions ID if it's an update
        $id = $this->request->getPost('id');

        // Get the data and trim spaces from title and description
        $data = [
            'title' => trim($this->request->getPost('title')),
            'description' => trim($this->request->getPost('description')),
        ];

        if ($id) {
            // Update existing terms and conditions
            $data['id'] = $id;
            if ($termsModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Terms & Conditions updated successfully',
                ]);
            }
        } else {
            // Create new terms and conditions
            if ($termsModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Terms & Conditions added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save terms & conditions. Please try again.',
        ]);
    }

    public function delete($id)
    {
        // Validate that $id is numeric and not empty
        if (is_numeric($id) && !empty($id)) {
            try {
                // Check if the record exists
                $termsModel = new TermsAndConditionsModel();
                $terms = $termsModel->find($id);

                // If no record is found, return an error response
                if (!$terms) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Terms & Conditions not found.',
                    ]);
                }

                // Proceed with deletion
                $termsModel->where('id', $id)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Terms & Conditions deleted successfully.',
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
