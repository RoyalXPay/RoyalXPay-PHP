<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Libraries\Template;
use App\Controllers\BaseController;

class AuthController extends BaseController
{
    protected $session;
    protected $usersModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $isLoggedIn = $this->session->get('logged_in');

        if ($isLoggedIn) {
            // Redirect to login page if not logged in
            return redirect()->to(site_url('admin/dashboard'));
        }

        $data = [];
        return view('admin/login', $data);
    }

    public function verifyLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if (!empty($username) && !empty($password)) {
            $result = $this->usersModel->authenticateUser($username, $password);

            if ($result === true) {
                // Get session data
                $session = session();
                $userType = $session->get('user_type');

                // Redirect based on user type
                if ($userType === 'admin') {
                    return redirect()->to(site_url('admin/dashboard'));
                } elseif ($userType === 'user') {
                    return redirect()->to(site_url('user/dashboard'));
                }
            } elseif ($result === 'inactive') {
                $this->session->setFlashdata('error', 'Your account is currently inactive.');
            } else {
                $this->session->setFlashdata('error', 'Invalid Email / Password');
            }
        } else {
            $this->session->setFlashdata('error', 'Username and Password are required.');
        }

        return redirect()->to(site_url('login'));
    }

    

    public function profile()
    {
        set_title('Profile | ' . SITE_NAME);

        $data = [];
        $userId = $this->session->get('user_id');
        $data['profiledata'] = $this->usersModel->where("user_id", $userId)->first();

        return view('admin/profile', $data);
    }

    public function updateProfile()
    {

        $userId = $this->session->get('user_id');
        $firstName = $this->request->getPost('first_name');

        $arrSaveData = [
            'first_name' => $firstName
        ];
        $this->usersModel->update($userId, $arrSaveData);

        $this->session->setFlashdata('success', 'Profile updated successfully.');
        return redirect()->to(site_url('admin/profile'));
    }

    public function changePassword()
    {

        $data = [];
        return view('admin/change_password', $data);
    }

    public function updatePassword()
    {

        $userId = $this->session->get('user_id');
        $password = $this->request->getPost('password');

        $arrSaveData = [
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $this->usersModel->update($userId, $arrSaveData);

        $this->session->setFlashdata('success', 'Password updated successfully.');
        return redirect()->to(site_url('admin/change-password'));
    }

    public function logout()
    {
        // Define the session keys to be cleared
        $sessionKeys = ['user_id', 'first_name', 'last_name', 'email', 'phone', 'user_type', 'status', 'logged_in'];
        // Get the session service
        $session = \Config\Services::session();
        // Remove the specified session variables
        foreach ($sessionKeys as $sessionKey) {
            $session->remove($sessionKey);
        }
        // Optionally destroy the session
        $session->destroy();
        // Redirect to the login page after logout
        return redirect()->to(site_url('login'));
    }

    public function register()
    {
        return view('admin/register');
    }

   public function registerSave()
{
    if ($this->request->isAJAX()) {
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $repeatPassword = $this->request->getPost('repeat_password');

        // 1. Check if email already exists
        $existingUser = $this->usersModel
            ->where('email', $email)
            ->orWhere('username', $username)
            ->first();

        if ($existingUser) {
            return $this->response->setJSON([
                'status' => 'exists',
                'message' => 'User already exists. Please login.',
                'suggest_email' => $existingUser['email']
            ]);
        }

        // 2. Validate form fields
        $rules = [
            'username' => 'required',
            'name' => 'required',
            'email' => 'required|valid_email',
            'phone' => 'required',
            'password' => 'required|min_length[6]',
            'repeat_password' => 'required|matches[password]',
        ];

        // if (!$this->validate($rules)) {
        //     return $this->response->setJSON([
        //         'status' => 'validation_error',
        //         'message' => $this->validator->listErrors()
        //     ]);
        // }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // 3. Save new merchant
        $data = [
            'username' => $username,
            'name' => $this->request->getPost('name'),
            'email' => $email,
            'phone' => $this->request->getPost('phone'),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'gender' => $this->request->getPost('gender'),
            'user_type' => "Merchant",
            'status' => 'active',
            'wallet' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->usersModel->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Registration successful. You can now login.'
        ]);
    }

    // fallback for non-AJAX
    return redirect()->to(site_url('register'));
}


}
