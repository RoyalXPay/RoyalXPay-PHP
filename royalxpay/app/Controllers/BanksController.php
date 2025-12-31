<?php

namespace App\Controllers;

use App\Models\BanksModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;

class BanksController extends BaseController
{
    public function index()
    {
        set_title('Bank Details List | ' . SITE_NAME);

        $data = [
            'action' => "banks",
            'pageTitle' => "Bank Details List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        $banksModel = new BanksModel();
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
        $totalRecord = $banksModel->getBanks($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $banksModel->getBanks($data['searchArray'], $startLimit, $Limit);

        return view('admin/banks/index', $data);
    }

    public function edit($id)
    {
        $banksModel = new BanksModel();
        $bank = $banksModel->find($id);
        if ($bank) {
            return $this->response->setJSON($bank);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Bank details not found']);
        }
    }

    public function save()
    {
        $validation = \Config\Services::validation();

        if (!$this->validate([
            'bank_name' => 'required',
            'account_number' => 'required|numeric',
            'ifsc_code' => 'required',
            'account_holder' => 'required',
            'swift_code' => 'required'
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $banksModel = new BanksModel();
        $id = $this->request->getPost('bank_id');

        $data = [
            'bank_name' => trim($this->request->getPost('bank_name')),
            'account_number' => trim($this->request->getPost('account_number')),
            'ifsc_code' => trim($this->request->getPost('ifsc_code')),
            'branch_name' => trim($this->request->getPost('branch_name')),
            'account_holder' => trim($this->request->getPost('account_holder')),
            'swift_code' => trim($this->request->getPost('swift_code'))
        ];

        if ($id) {
            $data['bank_id'] = $id;
            if ($banksModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Bank details updated successfully',
                ]);
            }
        } else {
            if ($banksModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Bank details added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save bank details. Please try again.',
        ]);
    }

    public function delete($id)
    {
        if (is_numeric($id) && !empty($id)) {
            try {
                $banksModel = new BanksModel();
                $bank = $banksModel->find($id);

                if (!$bank) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Bank details not found.',
                    ]);
                }

                $banksModel->where('bank_id', $id)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Bank details deleted successfully.',
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
