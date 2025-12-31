<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Controllers\BaseController;

class AuthenticationController extends BaseController
{
    protected $session;
    protected $usersModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->session = \Config\Services::session();
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
                    return $this->response->setJSON([
                        'status' => 'success'
                    ]);
                }
            } elseif ($result === 'inactive') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Your account is currently inactive.',
                ]);
            } elseif ($result === 'user_not_found') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not found. Please register.',
                    'user_not_found' => true,
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid Email / Password',
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Username and Password are required.',
            ]);
        }
    }

    /**
     * Handle new user registration.
     */
    public function register()
    {
        $postData = $this->request->getPost();

        // Check if user already exists by email or phone
        $existingUser = $this->usersModel
            ->where('email', $postData['email'])
            ->orWhere('phone', $postData['phone'])
            ->first();

        if ($existingUser) {
            return $this->response->setJSON([
                'status'       => 'exists',
                'message'      => 'User already exists. Please login.',
                'suggest_email' => $postData['email']
            ]);
        }

        $rules = [
            'name'            => 'required',
            'email'           => 'required|valid_email|is_unique[users.email]',
            'phone'           => 'required|numeric|min_length[10]',
            'password'        => 'required|min_length[6]',
            'repeat_password' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $userData = [
            'name'     => $postData['name'],
            'email'    => $postData['email'],
            'phone'    => $postData['phone'],
            'password' => password_hash($postData['password'], PASSWORD_DEFAULT),
            'status'    => "active",
        ];

        try {
            $this->usersModel->insert($userData);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Registration successful. Please login.',
                'suggest_email' => $postData['email']
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Failed to register user.',
                'error'   => $e->getMessage()
            ]);
        }
    }

    /**
     * Show user profile.
     */
    public function viewProfile()
    {
        set_title('Profile | ' . SITE_NAME);

        $userId = $this->session->get('user_id');
        $data['profiledata'] = $this->usersModel->where("user_id", $userId)->first();

        return view('admin/profile', $data);
    }

    /**
     * Update user profile.
     */
    public function updateProfile()
    {
        $userId = $this->session->get('user_id');

        $updateData = [
            'first_name' => $this->request->getPost('first_name')
        ];

        $this->usersModel->update($userId, $updateData);

        $this->session->setFlashdata('success', 'Profile updated successfully.');
        return redirect()->to(site_url('admin/profile'));
    }

    /**
     * Show the update password form.
     */
    public function changePasswordForm()
    {
        return view('admin/update_password');
    }

    /**
     * Save the updated password.
     */
    public function updatePassword()
    {
        $userId = $this->session->get('user_id');
        $newPassword = $this->request->getPost('password');

        $this->usersModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);

        $this->session->setFlashdata('success', 'Password updated successfully.');
        return redirect()->to(site_url('admin/update-password'));
    }

    /**
     * Handle user logout.
     */
    public function logout()
    {
        $sessionKeys = ['user_id', 'first_name', 'last_name', 'email', 'phone', 'user_type', 'status', 'logged_in'];

        foreach ($sessionKeys as $key) {
            $this->session->remove($key);
        }

        $this->session->destroy();

        return redirect()->to(site_url('/'));
    }
}
