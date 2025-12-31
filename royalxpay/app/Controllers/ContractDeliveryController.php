<?php

namespace App\Controllers;

use App\Libraries\Pagination;
use App\Models\ContractModel;
use App\Models\ContractDeliveryModel;
use App\Controllers\BaseController;

class ContractDeliveryController extends BaseController
{
    public function index()
    {
        set_title('Contract Deliveries | ' . SITE_NAME);

        // Check for contract_id in the GET request
        $contractId = $this->request->getGet('contract_id');
        $data = [
            'action' => "contract-delivery-history?contract_id=" . $contractId,
            'pageTitle' => "Contract Delivery List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        // Initialize models and pagination
        $customPagination = new Pagination();
        $contractDeliveryModel = new ContractDeliveryModel();

        // Collect search criteria
        $searchFields = ['txtsearch'];
        foreach ($searchFields as $field) {
            $value = $this->request->getGet($field);
            if ($value) {
                $data[$field] = $value;
                $data['searchArray'][$field] = $value;
            }
        }

        // Pagination logic
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 20;

        // Get the total record count based on search criteria (if any)
        $totalRecord = $contractDeliveryModel->getContractDeliveries($contractId, $data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetch results for the current page based on search criteria
        $data['results'] = $contractDeliveryModel->getContractDeliveries($contractId, $data['searchArray'], $startLimit, $Limit);
        
        // Return the view with the data
        return view('admin/contract/delivery/index', $data);
    }

    public function save()
    {
        // Load validation service
        $id = $this->request->getPost('delivery_id');
        $validation = \Config\Services::validation();

        // Validate the form data
        if (!$this->validate([
            'contract_id'    => 'required|numeric',
            'delivery_date'   => 'required|valid_date',
            'delivery_agent'  => 'required|string',
        ])) {
            // Return errors if validation fails
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        // Initialize the models
        $contractDeliveryModel = new ContractDeliveryModel();

        // Prepare the data for saving
        $data = [
            'contract_id'    => $this->request->getPost('contract_id'),
            'delivery_date'   => $this->request->getPost('delivery_date'),
            'delivery_agent'  => $this->request->getPost('delivery_agent'),
            'delivery_status' => $this->request->getPost('delivery_status') ?? '',
            'notes'           => $this->request->getPost('notes') ?? '',
        ];

        // File upload handling
        $uploadPath = FCPATH . 'uploads/contract/delivery/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $imageFileName = $imageFile->getRandomName();
            if ($imageFile->move($uploadPath, $imageFileName)) {
                $data['image'] = $imageFileName;
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Failed to upload image.',
                ]);
            }
        }

        // Check if we are updating or creating a new record
        if ($id) {
            // Update the delivery history
            $data['delivery_id'] = $id;
            if ($contractDeliveryModel->save($data)) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Delivery history updated successfully.',
                ]);
            }
        } else {
            // Insert a new delivery history
            if ($contractDeliveryModel->save($data)) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Delivery history added successfully.',
                ]);
            }
        }

        // If saving/updating failed, return error
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Failed to save delivery history. Please try again.',
        ]);
    }

    public function edit($id)
    {
        $contractDeliveryModel = new ContractDeliveryModel();
        $deliveryDetails = $contractDeliveryModel->find($id);

        if ($deliveryDetails) {
            return $this->response->setJSON($deliveryDetails);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Delivery not found']);
        }
    }

    public function delete($id)
    {
        // Validate that $id is numeric and not empty
        if (is_numeric($id) && !empty($id)) {
            try {
                // Initialize the models
                $contractDeliveryModel = new ContractDeliveryModel();

                // Check if the delivery history exists for the given ID
                $deliveryHistory = $contractDeliveryModel->find($id);

                // If no delivery history record is found, return an error response
                if (!$deliveryHistory) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Delivery history not found.',
                    ]);
                }

                // Proceed with deletion of the delivery history record
                $contractDeliveryModel->delete($id);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Delivery history deleted successfully.',
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
                'message' => 'Invalid ID provided.',
            ]);
        }
    }
}
