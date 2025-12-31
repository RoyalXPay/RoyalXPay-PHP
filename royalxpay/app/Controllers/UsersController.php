<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Libraries\EmailSms;
use App\Libraries\Pagination;
use App\Controllers\BaseController;

class UsersController extends BaseController
{
    protected $session;
    protected $usersModel;
    protected $isAdminLoggedIn;

    public function __construct()
    {
        $this->session = session();
        $this->usersModel = new UsersModel();
        $this->isAdminLoggedIn = $this->session->get('isAdminLoggedIn');
    }

    public function index()
    {
        set_title('Customer list | ' . SITE_NAME);

        $data = [
            'action' => "customers",
            'pageTitle' => "Customer list",
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
        $Limit = 200;
        $totalRecord = $this->usersModel->getUsersDetails($data['searchArray'],'user', '', '', 1);
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $this->usersModel->getUsersDetails($data['searchArray'],'user', $startLimit, $Limit);

        return view('customers/index', $data);
    }

    public function edit($id)
    {
        $customerModel = new UsersModel();
        $customer = $customerModel->find($id);
        if ($customer) {
            return $this->response->setJSON($customer);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Customer not found']);
        }
    }

    public function save()
    {
        $validation = \Config\Services::validation();

        // Validate input
        if (!$this->validate([
            'name' => 'required',
            'phone' => 'required|numeric|min_length[8]|max_length[15]',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $customerModel = new UsersModel();

        // Get customer ID for update
        $customerId = $this->request->getPost('customer_id');
        $status = $this->request->getPost('status');
        $email = $this->request->getPost('email');

        // Prepare data for update
        $data = [
            'username' => $this->request->getPost('name'),
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
            'alt_mobile_number' => $this->request->getPost('alt_mobile_number'),
            'gender' => $this->request->getPost('gender'),
            'status' => !empty($status) ? $status : 'active',
            'notes' => $this->request->getPost('notes'),
        ];

        // Optionally add email if provided
        if (!empty($email)) {
            $data['email'] = $email;
        }

        if ($customerId) {
            // Update existing customer
            $data['user_id'] = $customerId;
            if ($customerModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Customer updated successfully',
                ]);
            }
        } else {
            // Create new customer
            if ($customerModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Customer added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'errors' => 'Failed to save customer. Please try again.',
        ]);
    }

    public function showDetails($userId)
    {

        $data = array();
        set_title('Customers Details | ' . SITE_NAME);

        $data['pageTitle'] = "Customers Details";
        $data['record'] = $this->usersModel
            ->join('address', 'address.user_id = users.user_id', 'left')
            ->where('users.user_id', $userId)
            ->select('users.*, address.address, address.city, address.state, address.postal_code, address.country, address.address_type')
            ->first();

        return view('customers/preview', $data);
    }

    public function delete()
    {
        $customerId = $this->request->getPost('customerId');

        if (is_numeric($customerId) && !empty($customerId)) {
            try {
                $customerModel = new UsersModel();
                $customerModel->where('user_id', $customerId)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Product deleted successfully.',
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
