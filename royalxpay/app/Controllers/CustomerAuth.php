<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Controllers\BaseController;

class CustomerAuth extends BaseController
{
    protected $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel(); // ✅ Initialize model here
        helper(['form', 'url']);
        session();
    }

    public function register()
    {
        $request = service('request');
        $name = $request->getPost('name');
        $email = $request->getPost('email');
        $password = password_hash($request->getPost('password'), PASSWORD_DEFAULT);

        // Check if already registered
        if ($this->customerModel->where('email', $email)->first()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Email already exists.']);
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'status' => 'ACTIVE'
        ];

        if ($this->customerModel->insert($data)) {
            return $this->response->setJSON(['status' => true, 'message' => 'Registration successful.']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Registration failed.']);
        }
    }

    public function login()
    {
        $request = service('request');
        $email = $request->getPost('loginEmail');
        $password = $request->getPost('loginPassword');
        $user = $this->customerModel->where('email', $email)->first();

        if (!$user) {
            return $this->response->setJSON(['status' => false, 'message' => 'Email ID does not exist please register']);
        }

        if (!password_verify($password, $user['password'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Incorrect Password.']);
        }

        if ($user['status'] != 'ACTIVE') {
            return $this->response->setJSON(['status' => false, 'message' => 'Account is inactive.']);
        }

        session()->set([
            'customer_id' => $user['id'],
            'customer_name' => $user['name'],
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Login successful.']);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url());
    }
}
