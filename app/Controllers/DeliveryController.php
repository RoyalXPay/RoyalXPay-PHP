<?php

namespace App\Controllers;

use App\Models\DeliveryTimeModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;

class DeliveryController extends BaseController
{
    public function index()
    {
        set_title('Delivery Time List | ' . SITE_NAME);

        $data = [
            'action' => "delivery-times",
            'pageTitle' => "Delivery Time List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'startDate' => '',
            'endDate' => '',
            'searchArray' => []
        ];

        $deliveryTimeModel = new DeliveryTimeModel();
        $customPagination = new Pagination();

        // Get filter values from the GET request
        $searchFields = ['txtsearch', 'startDate', 'endDate'];
        foreach ($searchFields as $field) {
            $value = $this->request->getGet($field);
            if ($value) {
                $data[$field] = $value;
                $data['searchArray'][$field] = $value;
            }
        }

        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 50;
        $totalRecord = $deliveryTimeModel->getDeliveryTimes($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetch the actual delivery times
        $data['results'] = $deliveryTimeModel->getDeliveryTimes($data['searchArray'], $startLimit, $Limit);

        return view('admin/delivery/index', $data);
    }

    public function edit($id)
    {
        $deliveryTimeModel = new DeliveryTimeModel();
        $deliveryTime = $deliveryTimeModel->find($id);
        if ($deliveryTime) {
            return $this->response->setJSON($deliveryTime);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Delivery Time not found']);
        }
    }

    public function save()
    {
        $validation = \Config\Services::validation();

        // Validate input
        if (!$this->validate([
            'title' => 'required',
            'description' => 'required',
            // 'product_type' => 'required|in_list[rent,sale]',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $deliveryTimeModel = new DeliveryTimeModel();
        // Get delivery time ID if it's an update
        $id = $this->request->getPost('id');

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            // 'product_type' => $this->request->getPost('product_type'),
        ];

        if ($id) {
            // Update existing delivery time
            $data['id'] = $id;
            if ($deliveryTimeModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Delivery Time updated successfully',
                ]);
            }
        } else {
            // Create new delivery time
            if ($deliveryTimeModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Delivery Time added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save delivery time. Please try again.',
        ]);
    }

    public function delete($id)
    {
        // Validate that $id is numeric and not empty
        if (is_numeric($id) && !empty($id)) {
            try {
                // Check if the record exists
                $deliveryTimeModel = new DeliveryTimeModel();
                $deliveryTime = $deliveryTimeModel->find($id);

                // If no record is found, return an error response
                if (!$deliveryTime) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Delivery Time not found.',
                    ]);
                }

                // Proceed with deletion
                $deliveryTimeModel->where('id', $id)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Delivery Time deleted successfully.',
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
