<?php

namespace App\Controllers;

use App\Libraries\Template;
use App\Models\EmployeeModel;
use App\Libraries\Paginationnew;
use App\Models\EmployeeTaskModel;

class EmployeeTaskController extends BaseController
{
    protected $session;
    protected $employeeModel;
    protected $isAdminLoggedIn;
    protected $employeeTaskModel;

    public function __construct()
    {
        $this->session = session();
        $this->employeeModel = new EmployeeModel();
        $this->employeeTaskModel = new EmployeeTaskModel();
        $this->isAdminLoggedIn = $this->session->get('isAdminLoggedIn');
            $this->initializePrivileges(); // This calls method from BaseController

    }

    public function index()
    {
       

        set_title('Employee Tasks | ' . SITE_NAME);
        $data['pagetitle'] = "Task List";
        $data['action'] = "employee-tasks";

        $searchArray = array();
        $template = new Template();
        $paginationnew = new Paginationnew();

        // Search filters
        $txtsearch = $this->request->getGet('txtsearch');
        if ($txtsearch) {
            $searchArray['txtsearch'] = $txtsearch;
        }

        $status = $this->request->getGet('status');
        if ($status) {
            $searchArray['status'] = $status;
        }

        $priority = $this->request->getGet('priority');
        if ($priority) {
            $searchArray['priority'] = $priority;
        }

        $employee_id = $this->request->getGet('employee_id');
        if ($employee_id) {
            $searchArray['employee_id'] = $employee_id;
        }

        // Pagination setup
         $page = (int) $this->request->getGet('page') ?: 1;

        $Limit = 10;
        $totalRecord = $this->employeeTaskModel->getTasks($searchArray, '', '', 1);

        $startLimit = ($page - 1) * $Limit;
        $data['startLimit'] = $startLimit;

        $pagination = $paginationnew->getPaginate($totalRecord, $page, $Limit);
        $data['txtsearch'] = $txtsearch;
        $data['status'] = $status;
        $data['priority'] = $priority;
        $data['employee_id'] = $employee_id;
        $data['pagination'] = $pagination;
        $data["searchArray"] = $searchArray;
        $data["results"] = $this->employeeTaskModel->getTasks($searchArray, $startLimit, $Limit);
        $data['taskStatistics'] = $this->employeeTaskModel->getTaskStatistics();

        $template->render('admintemplate', 'contents', 'admin/task/index', $data);
    }

    public function create()
    {
       

        $template = new Template();
        set_title('Add Task | ' . SITE_NAME);
        $data['pagetitle'] = "Add Task";
        $data['employees'] = $this->employeeModel->findAll();

        $template->render('admintemplate', 'contents', 'admin/task/create', $data);
    }

    public function edit()
    {
       

        $template = new Template();
        set_title('Edit Task | ' . SITE_NAME);
        $data['pagetitle'] = "Update Task";

        $id = $this->request->getGet('id');
        $data['task'] = $this->employeeTaskModel->first($id);
        $data['employees'] = $this->employeeModel->findAll();

        $template->render('admintemplate', 'contents', 'admin/task/create', $data);
    }

    public function save()
    {
        $postData = $this->request->getPost();

        $data = [
            'employee_id' => $postData['employee_id'],
            'title'       => $postData['title'],
            'description' => $postData['description'],
            'priority'    => $postData['priority'],
            'due_date'    => $postData['due_date'],
            'assigned_by' => $this->session->get('isAdminLoggedIn'),
            'status'      => isset($postData['status']) ? $postData['status'] : 'pending',
        ];

        if (!empty($postData['status']) && $postData['status'] == 'completed') {
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        if (!empty($postData['task_id'])) {
            $result = $this->employeeTaskModel->update($postData['task_id'], $data);
            $message = 'Task updated successfully.';
        } else {
            $result = $this->employeeTaskModel->insert($data);
            $message = 'Task added successfully.';
        }

        if ($result) {
            $this->session->setFlashdata('message', $message);
        } else {
            $this->session->setFlashdata('errmessage', 'Something went wrong...');
        }

        return redirect()->to(site_url('employee-tasks'));
    }

    public function preview()
    {
       

        $template = new Template();
        set_title('Task Details | ' . SITE_NAME);
        $data['pagetitle'] = "Task Details";

        $id = $this->request->getGet('id');
        $data['task'] = $this->employeeTaskModel->first($id);

        $template->render('admintemplate', 'contents', 'admin/task/preview', $data);
    }

    public function updateStatus()
    {
        $task_id = $this->request->getPost('task_id');
        $status = $this->request->getPost('status');

        if ($this->employeeTaskModel->updateTaskStatus($task_id, $status)) {
            $this->session->setFlashdata('message', 'Task status updated successfully.');
        } else {
            $this->session->setFlashdata('errmessage', 'Failed to update task status.');
        }

        return redirect()->back();
    }

    public function delete()
    {
        if (!$this->isAdminLoggedIn) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ]);
        }

        $taskId = $this->request->getPost('task_id');

        if (empty($taskId) || !is_numeric($taskId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid task ID'
            ]);
        }

        $task = $this->employeeTaskModel->find($taskId);

        if (!$task) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Task not found'
            ]);
        }

        try {
            $this->employeeTaskModel->delete($taskId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Task deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete task: ' . $e->getMessage()
            ]);
        }
    }
}
