<?php

namespace App\Controllers;

use App\Models\ColorsModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;

class ColorsController extends BaseController
{
    public function index()
    {
        set_title('Colors List | ' . SITE_NAME);

        $data = [
            'action' => "colors",
            'pageTitle' => "Colors List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        $colorsModel = new ColorsModel();
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
        $totalRecord = $colorsModel->getColors($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $colorsModel->getColors($data['searchArray'], $startLimit, $Limit);

        return view('admin/colors/index', $data);
    }

    public function edit($id)
    {
        $colorsModel = new ColorsModel();
        $color = $colorsModel->find($id);
        if ($color) {
            return $this->response->setJSON($color);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Color not found']);
        }
    }

    public function save()
    {
        $validation = \Config\Services::validation();

        if (!$this->validate([
            'name' => 'required',
            // 'description' => 'required',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $colorsModel = new ColorsModel();
        $id = $this->request->getPost('color_id');

        $data = [
            'name' => trim($this->request->getPost('name')),
            // 'description' => trim($this->request->getPost('description')),
        ];

        if ($id) {
            $data['color_id'] = $id;
            if ($colorsModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Color updated successfully',
                ]);
            }
        } else {
            if ($colorsModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Color added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save Color. Please try again.',
        ]);
    }

    public function delete($id)
    {
        if (is_numeric($id) && !empty($id)) {
            try {
                $colorsModel = new ColorsModel();
                $color = $colorsModel->find($id);

                if (!$color) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Color not found.',
                    ]);
                }

                $colorsModel->where('color_id', $id)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Color deleted successfully.',
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
