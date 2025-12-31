<?php

namespace App\Controllers;

use App\Models\CompanyModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;

class CompanyController extends BaseController
{
    public function index()
    {
        set_title('Company List | ' . SITE_NAME);

        $data = [
            'action' => "companies",
            'pageTitle' => "Company List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'searchArray' => []
        ];

        $companyModel = new CompanyModel();
        $customPagination = new Pagination();

        $searchFields = $this->request->getGet();
        foreach ($searchFields as $field => $searchValue) {
            if ($field) {
                $data['searchArray'][$field] = trim($searchValue);
            }
        }

        $Limit = 50;
        $page = (int) $this->request->getGet('page') ?: 1;
        $totalRecord = $companyModel->getCompanyDetails($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['startLimit'] = $startLimit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $companyModel->getCompanyDetails($data['searchArray'], $startLimit, $Limit);

        return view('company/index', $data);
    }

    public function edit($id)
    {
        $companyModel = new CompanyModel();
        $company = $companyModel->find($id);
        if ($company) {
            return $this->response->setJSON($company);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Company not found']);
        }
    }

    public function save()
    {
        $validation = \Config\Services::validation();

        // Validate input
        if (!$this->validate([
            'company_name' => 'required',
            'mobile_number' => 'required|regex_match[/^[0-9]{8,15}$/]',
            'alt_mobile_number' => 'permit_empty|regex_match[/^[0-9]{8,15}$/]',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $companyModel = new CompanyModel();
        // Get company ID if it's an update
        $companyId = $this->request->getPost('company_id');
        $status = $this->request->getPost('status');

        $data = [
            'company_name' => $this->request->getPost('company_name'),
            'mobile_number' => $this->request->getPost('mobile_number'),
            'alt_mobile_number' => $this->request->getPost('alt_mobile_number'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address'),
            'status' => empty($status) ? 'active' : $status,
        ];

        if ($companyId) {
            // Update existing company
            $data['company_id'] = $companyId;
            if ($companyModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Company updated successfully',
                ]);
            }
        } else {
            // Create new company
            if ($companyModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Company added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save company. Please try again.',
        ]);
    }

    public function showDetails($companyId)
    {
        $data = array();
        set_title('Company Details | ' . SITE_NAME);

        $data['pageTitle'] = "Company Details";
        $companyModel = new CompanyModel();
        $data['record'] = $companyModel
            ->where('company_id', $companyId)
            ->first();

        return view('company/preview', $data);
    }

    public function delete()
    {
        $companyId = $this->request->getPost('companyId');

        if (is_numeric($companyId) && !empty($companyId)) {
            try {
                $companyModel = new CompanyModel();
                $companyModel->where('company_id', $companyId)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Company deleted successfully.',
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $e->getMessage(),
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
