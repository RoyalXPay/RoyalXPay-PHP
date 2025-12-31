<?php

namespace App\Controllers;

use App\Models\ResolutionModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;

class ResolutionController extends BaseController
{
    public function index()
    {
        set_title('Resolution List | ' . SITE_NAME);

        $data = [
            'action' => "resolutions",
            'pageTitle' => "Resolution List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        $resolutionModel = new ResolutionModel();
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
        $totalRecord = $resolutionModel->getResolutions($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $resolutionModel->getResolutions($data['searchArray'], $startLimit, $Limit);

        return view('admin/resolution/index', $data);
    }

    public function edit($id)
    {
        $resolutionModel = new ResolutionModel();
        $resolution = $resolutionModel->find($id);
        if ($resolution) {
            return $this->response->setJSON($resolution);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Resolution not found']);
        }
    }

    public function save()
    {
        $validation = \Config\Services::validation();

        if (!$this->validate([
            'resolution_type' => 'required',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $resolutionModel = new ResolutionModel();
        $id = $this->request->getPost('resolution_id');

        $data = [
            'resolution_type' => trim($this->request->getPost('resolution_type')),
        ];

        if ($id) {
            $data['resolution_id'] = $id;
            if ($resolutionModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Resolution updated successfully',
                ]);
            }
        } else {
            if ($resolutionModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Resolution added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save Resolution. Please try again.',
        ]);
    }

    public function delete($id)
    {
        if (is_numeric($id) && !empty($id)) {
            try {
                $resolutionModel = new ResolutionModel();
                $resolution = $resolutionModel->find($id);

                if (!$resolution) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Resolution not found.',
                    ]);
                }

                $resolutionModel->where('resolution_id', $id)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Resolution deleted successfully.',
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
