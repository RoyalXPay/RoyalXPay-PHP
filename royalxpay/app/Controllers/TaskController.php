<?php

namespace App\Controllers;

use App\Models\TaskModel;
use App\Models\CompanyModel;
use App\Models\EmployeeModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;
use App\Models\ReportingManagerModel;

class TaskController extends BaseController
{
    protected $session;
    protected $taskModel;
    protected $companyModel;
    protected $employeeModel;
    protected $reportingManagerModel;

    public function __construct()
    {
        $this->session = session();
        $this->taskModel = new TaskModel();
        $this->companyModel = new CompanyModel();
        $this->employeeModel = new EmployeeModel();
        $this->reportingManagerModel = new ReportingManagerModel();
            $this->initializePrivileges(); // This calls method from BaseController

    }

    public function index()
    {
        set_title('Employee Tasks | ' . SITE_NAME);
        $companyId  = session('user_id');
        $userType   = session('user_type');
        $data = [
            'action' => "employee-tasks",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'searchArray' => []
        ];

        $customPagination = new Pagination();

        // Collect search criteria
        $searchFields = $this->request->getGet();
        foreach ($searchFields as $field => $searchValue) {
            $data['searchArray'][$field] = trim($searchValue);
        }

        if (strtolower($userType) === 'merchant' && !empty($companyId)) {
            $data['searchArray']['user_id'] = $companyId;
        }

        $Limit = 10;
        $page = (int) $this->request->getGet('page') ?: 1;
        $data['managers'] = $this->reportingManagerModel->getReportingManagers();
        $data['companies'] = $this->companyModel->where('status', 'Active')->findAll();
        $totalRecord = $this->taskModel->getTasks($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $this->taskModel->getTasks($data['searchArray'], $startLimit, $Limit);
        $data['taskStatistics'] = $this->taskModel->getTaskStatistics();

        return view('admin/task/index', $data);
    }

   public function create()
{
    set_title('Add Task | ' . SITE_NAME);

    $data['pagetitle'] = "Add Task";

    $userType = $this->session->get('user_type');
    $loggedUserId = $this->session->get('user_id');

    // ✔ Superadmin → All employees
    if ($userType === 'superadmin') {
        $data['employees'] = $this->employeeModel->findAll();
    } 
    // ✔ Merchant → only his employees
    else {
        $data['employees'] = $this->employeeModel
            ->where('user_id', $loggedUserId)
            ->findAll();
    }

    return view('admin/task/create', $data);
}


   public function edit()
{
    set_title('Edit Task | ' . SITE_NAME);
    $data['pagetitle'] = "Update Task";

    $id = $this->request->getGet('id');
    $data['task'] = $this->taskModel->first($id);

    $userType = $this->session->get('user_type');
    $loggedUserId = $this->session->get('user_id');

    // 🔥 ONLY THIS LINE CHANGED
    $data['employees'] = ($userType === 'superadmin')
        ? $this->employeeModel->findAll()
        : $this->employeeModel->where('user_id', $loggedUserId)->findAll();

    return view('admin/task/create', $data);
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
            'assigned_by' => $this->session->get('user_id'),
            'notes'       => $this->session->get('notes'),
            'status'      => isset($postData['status']) ? $postData['status'] : 'pending',
        ];

        if (!empty($postData['status']) && $postData['status'] == 'completed') {
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        if (!empty($postData['task_id'])) {
            $result = $this->taskModel->update($postData['task_id'], $data);
            $message = 'Task updated successfully.';
        } else {
            $result = $this->taskModel->insert($data);
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
        set_title('Task Details | ' . SITE_NAME);
        $data['pageTitle'] = "Task Details";

        $id = $this->request->getGet('id');
        $data['task'] = $this->taskModel->first($id);
        return view('admin/task/preview', $data);
    }

    public function updateStatus()
    {
        $task_id = $this->request->getPost('task_id');
        $status = $this->request->getPost('status');

        if ($this->taskModel->updateTaskStatus($task_id, $status)) {
            $this->session->setFlashdata('message', 'Task status updated successfully.');
        } else {
            $this->session->setFlashdata('errmessage', 'Failed to update task status.');
        }

        return redirect()->back();
    }

    public function delete()
    {
        $taskId = $this->request->getPost('task_id');

        if (empty($taskId) || !is_numeric($taskId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid task ID'
            ]);
        }

        $task = $this->taskModel->find($taskId);

        if (!$task) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Task not found'
            ]);
        }

        try {
            $this->taskModel->delete($taskId);

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
