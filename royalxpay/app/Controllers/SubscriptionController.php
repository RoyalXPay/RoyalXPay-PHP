<?php

namespace App\Controllers;

use App\Libraries\Pagination;
use App\Models\SubscriptionPackageModel;
use App\Controllers\BaseController;

class SubscriptionController extends BaseController
{
    public function index()
    {
        set_title('Subscription Package List | ' . SITE_NAME);

        // Initialize data array
        $data = [
            'action'       => "subscription_package",
            'pageTitle'    => "Subscription Package List",
            'results'      => [],
            'pagination'   => '',
            'startLimit'   => 0,
            'reverse'      => 0,
            'txtsearch'    => '',
            'status'        => '',
            'searchArray'  => []
        ];

        // Load the SubscriptionPackage model
        $subscriptionPackageModel = new SubscriptionPackageModel();
        $customPagination = new Pagination();

        // Handle search filters
        $searchFields = ['txtsearch', 'status'];
        foreach ($searchFields as $field) {
            $value = $this->request->getGet($field);
            if ($value) {
                $data[$field] = $value;
                $data['searchArray'][$field] = $value;
            }
        }

        $data['durations'] = get_subscription_durations();
        // Pagination handling
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 10;
        $totalRecord = $subscriptionPackageModel->getSubscriptionPackages($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetch the records for the current page
        $data['results'] = $subscriptionPackageModel->getSubscriptionPackages($data['searchArray'], $startLimit, $Limit);

        return view('admin/subscription/index', $data);
    }

    public function edit($id)
    {
        $subscriptionPackageModel = new SubscriptionPackageModel();
        $subscriptionPackage = $subscriptionPackageModel->find($id);

        if ($subscriptionPackage) {
            return $this->response->setJSON($subscriptionPackage);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Subscription Package not found'
            ]);
        }
    }

    public function save()
    {
        // Validation setup
        $validation = \Config\Services::validation();

        // Define validation rules for Subscription Package
        if (!$this->validate([
            'title'       => 'required|string|max_length[255]',
            'price'       => 'required|decimal',
            'duration'    => 'required|integer',
            'status'      => 'required|in_list[active,inactive]',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        // Prepare the data to be inserted/updated
        $data = [
            'title'      => trim($this->request->getPost('title')),
            'description' => trim($this->request->getPost('description')),
            'price'      => trim($this->request->getPost('price')),
            'duration'   => trim($this->request->getPost('duration')),
            'status'     => trim($this->request->getPost('status')),
        ];

        $uploadPath = ROOTPATH . 'public/uploads/packages';

        // Create uploads directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $id = $this->request->getPost('id');
        $subscriptionPackageModel = new SubscriptionPackageModel();

        $imageFile = $this->request->getFile('image');

        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            // If there's an existing image and we're editing, delete the old one
            if ($id) {
                $existingPackage = $subscriptionPackageModel->find($id);
                if ($existingPackage && isset($existingPackage['image'])) {
                    $previousImage = $existingPackage['image'];
                    // Delete the old image file
                    if ($previousImage && file_exists($uploadPath . '/' . $previousImage)) {
                        unlink($uploadPath . '/' . $previousImage);
                    }
                }
            }

            // Handle image upload
            $imageName = $imageFile->getRandomName();
            if ($imageFile->move($uploadPath, $imageName)) {
                $data['image'] = $imageName;
            }
        }

        if ($id) {
            $data['id'] = $id;
            if ($subscriptionPackageModel->save($data)) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Subscription Package updated successfully',
                ]);
            }
        } else {
            // If no ID, create a new subscription package
            if ($subscriptionPackageModel->save($data)) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Subscription Package added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Failed to save Subscription Package. Please try again.',
        ]);
    }

    public function delete($id)
    {
        if (is_numeric($id) && !empty($id)) {
            try {
                $subscriptionPackageModel = new SubscriptionPackageModel();
                $subscriptionPackage = $subscriptionPackageModel->find($id);

                if (!$subscriptionPackage) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Subscription Package not found.',
                    ]);
                }

                // Define the upload directory path
                $uploadPath = ROOTPATH . 'public/uploads/packages';

                // Remove the image from the file system if it exists
                if (!empty($subscriptionPackage['image'])) {
                    $imagePath = $uploadPath . '/' . $subscriptionPackage['image'];

                    // Delete the image file if it exists
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }

                // Delete the subscription package entry from the database
                $subscriptionPackageModel->where('id', $id)->delete();

                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Subscription Package deleted successfully.',
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'An unexpected error occurred. Please try again.',
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Invalid ID. Please try again.',
            ]);
        }
    }
}
