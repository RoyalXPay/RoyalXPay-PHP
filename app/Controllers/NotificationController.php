<?php

namespace App\Controllers;

use App\Libraries\Pagination;
use App\Libraries\Notifications;
use App\Models\NotificationModel;
use App\Controllers\BaseController;

class NotificationController extends BaseController
{

    public function index()
    {
        set_title('Notifications | ' . SITE_NAME);

        $data = [
            'action' => "notifications",
            'pageTitle' => "Notifications",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        // Initialize models and pagination
        $notificationModel = new NotificationModel();
        $customPagination = new Pagination();

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
        $Limit = 10;
        $totalRecord = $notificationModel->getNotifications($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetch notifications for the current page with search filter
        $data['results'] = $notificationModel->getNotifications($data['searchArray'], $startLimit, $Limit);

        return view('admin/notifications/index', $data);
    }

    public function save()
    {
        $validation = \Config\Services::validation();

        // Validation for notifications
        if (!$this->validate([
            'title'       => 'required|string|max_length[255]',
            'description' => 'required|string',
            'attachment'  => 'permit_empty|is_image[attachment]',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        // Assuming the NotificationModel is used for notifications
        $notificationModel = new NotificationModel();
        $id = $this->request->getPost('id');

        // Prepare data from request
        $data = [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
        ];

        // Handle attachment upload (if exists)
        $uploadPath = ROOTPATH . 'public/uploads/notifications';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Handle file upload for attachment (image or other files)
        $attachmentFile = $this->request->getFile('attachment');
        if ($attachmentFile && $attachmentFile->isValid() && !$attachmentFile->hasMoved()) {
            // If an ID exists, check for previous attachment and delete if necessary
            if ($id) {
                $existingNotification = $notificationModel->find($id);
                if ($existingNotification && isset($existingNotification['attachment'])) {
                    $previousFile = $existingNotification['attachment'];
                    if ($previousFile && file_exists($uploadPath . '/' . $previousFile)) {
                        unlink($uploadPath . '/' . $previousFile);
                    }
                }
            }

            // Generate random name and move file
            $attachmentFileName = $attachmentFile->getRandomName();
            if ($attachmentFile->move($uploadPath, $attachmentFileName)) {
                $data['attachment'] = $attachmentFileName;
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Failed to upload attachment.',
                ]);
            }
        }

        // Check if we are updating or creating a new notification
        if ($id) {
            $data['id'] = $id;
            if ($notificationModel->save($data)) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Notification updated successfully.',
                ]);
            }
        } else {
            if ($notificationModel->save($data)) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Notification added successfully.',
                ]);
            }
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Failed to save notification. Please try again.',
        ]);
    }

    public function edit($id)
    {
        $notificationModel = new NotificationModel();
        $notification = $notificationModel->find($id);

        if ($notification) {
            return $this->response->setJSON($notification);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Notification not found']);
        }
    }

    public function delete($id)
    {
        if (is_numeric($id) && !empty($id)) {
            try {
                $notificationModel = new NotificationModel();
                $notification = $notificationModel->find($id);

                // Check if notification exists
                if (!$notification) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Notification not found.',
                    ]);
                }

                $notificationModel->delete($id);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Notification deleted successfully.',
                ]);
            } catch (\Exception $e) {
                // Catch any exception and return an error
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'An unexpected error occurred. Please try again.',
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid notification ID.',
            ]);
        }
    }

    public function showDetails($id)
    {
        $data = array();
        $data['pageTitle'] = "Notification Details";
        $notificationModel = new NotificationModel();

        $data['record'] = $notificationModel->find($id);

        return view('admin/notifications/preview', $data);
    }

    public function sendNotification()
    {
        $android_token = 'dHf2pzmlQT-L0J8eR-lOv4:APA91bHoBIk_QeEXxRJvb61o-vmr6OvofOU_oiOb_5JtrpKfJy4_a6gA53LCk_o2pbq0NlQGFlnwuG4_LxYVgZQqH0yHPD2E3iX2eokPLqVGwJETsJjcD5N8E2rALcWcnUo0_0EAXHrt';
        $push = new Notifications();

        $result = $push->sendAndroidNotification($android_token, 'Test Notification', "Test Notification");

        if ($result === 200) {
            echo "Notification sent successfully";
        } else {
            echo "Failed to send notification. Response: " . $result . "\n";
        }
    }
}
