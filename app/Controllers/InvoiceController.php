<?php

namespace App\Controllers;

use App\Models\InvoiceModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;

class InvoiceController extends BaseController
{
    public function index()
    {
        set_title('Invoice List | ' . SITE_NAME);

        $data = [
            'action' => "companies",
            'pageTitle' => "Company List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'companyId' => '',
            'startDate' => '',
            'endDate' => '',
            'searchArray' => []
        ];

        $invoiceModel = new InvoiceModel();
        $customPagination = new Pagination();

        $searchFields = ['txtsearch', 'companyId', 'startDate', 'endDate'];
        foreach ($searchFields as $field) {
            $value = $this->request->getGet($field);
            if ($value) {
                $data[$field] = $value;
                $data['searchArray'][$field] = $value;
            }
        }

        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 50;
        $totalRecord = $invoiceModel->getInvoiceDetails($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $invoiceModel->getInvoiceDetails($data['searchArray'], $startLimit, $Limit);

        return view('admin/invoice/index', $data);
    }

    public function edit($id)
    {
        $invoiceModel = new InvoiceModel();
        $invoice = $invoiceModel->find($id);
        if ($invoice) {
            return $this->response->setJSON($invoice);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invoice not found']);
        }
    }

    public function save()
    {
        $validation = \Config\Services::validation();

        // Validate input
        if (!$this->validate([
            'invoice_id' => 'permit_empty|numeric',
            'company_name' => 'required',
            'product_name' => 'required',
            'amount' => 'required|numeric',
            'status' => 'required',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $invoiceModel = new InvoiceModel();
        // Get invoice ID if it's an update
        $invoiceId = $this->request->getPost('invoice_id');

        $data = [
            'company_name' => $this->request->getPost('company_name'),
            'product_name' => $this->request->getPost('product_name'),
            'amount' => $this->request->getPost('amount'),
            'status' => $this->request->getPost('status'),
        ];

        if ($invoiceId) {
            // Update existing invoice
            $data['invoice_id'] = $invoiceId;
            if ($invoiceModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Invoice updated successfully',
                ]);
            }
        } else {
            // Create new invoice
            if ($invoiceModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Invoice added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save invoice. Please try again.',
        ]);
    }

    public function showDetails($invoiceId)
    {
        $data = array();
        set_title('Invoice Details | ' . SITE_NAME);

        $data['pageTitle'] = "Invoice Details";
        $invoiceModel = new InvoiceModel();
        $data['record'] = $invoiceModel
            ->where('invoice_id', $invoiceId)
            ->first();

        return view('admin/invoice/preview', $data);
    }

    public function delete()
    {
        $invoiceId = $this->request->getPost('invoiceId');

        if (is_numeric($invoiceId) && !empty($invoiceId)) {
            try {
                $invoiceModel = new InvoiceModel();
                $invoiceModel->where('invoice_id', $invoiceId)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Invoice deleted successfully.',
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
