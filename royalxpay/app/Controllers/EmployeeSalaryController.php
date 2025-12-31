<?php

namespace App\Controllers;

use App\Libraries\Template;
use App\Libraries\Paginationnew;
use App\Models\EmployeeModel;
use App\Models\EmployeeSalaryModel;

class EmployeeSalaryController extends BaseController
{
    protected $session;
    protected $salaryModel;
    protected $employeeModel;
    protected $isAdminLoggedIn;

    public function __construct()
    {
        $this->session = session();
        $this->employeeModel = new EmployeeModel();
        $this->salaryModel = new EmployeeSalaryModel();
        $this->isAdminLoggedIn = $this->session->get('isAdminLoggedIn');
            $this->initializePrivileges(); // This calls method from BaseController

    }

    public function index()
    {
        

        set_title('Employee Salaries | ' . SITE_NAME);
        $data['pagetitle'] = "Employee Salaries";
        $data['action'] = "employee-salaries";

        $searchArray = array();
        $template = new Template();
        $paginationnew = new Paginationnew();

        // Get search parameters
        $txtsearch = $this->request->getGet('txtsearch');
        $employeeId = $this->request->getGet('employee_id');
        $month = $this->request->getGet('month');
        $year = $this->request->getGet('year');

        if ($employeeId) {
            $searchArray['employee_id'] = $employeeId;
            $data['employee_id'] = $employeeId;
        }

        if ($month) {
            $searchArray['month'] = $month;
            $data['month'] = $month;
        }

        if ($year) {
            $searchArray['year'] = $year;
            $data['year'] = $year;
        }

        $page = $this->request->getGet('page');
         $page = (int) $this->request->getGet('page') ?: 1;

        $Limit = 10;
        $totalRecord = $this->salaryModel->getSalariesWithEmployees($searchArray, 0, 0, true);

        $startLimit = ($page - 1) * $Limit;
        $data['startLimit'] = $startLimit;

        $pagination = $paginationnew->getPaginate($totalRecord, $page, $Limit);
        $data['txtsearch'] = $txtsearch;
        $data['pagination'] = $pagination;
        $data["searchArray"] = $searchArray;
        $data["results"] = $this->salaryModel->getSalariesWithEmployees($searchArray, $startLimit, $Limit);

        $data["months"] = [
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
        ];
        $data['employees'] = $this->employeeModel->findAll();

        $template->render('admintemplate', 'contents', 'admin/salary/index', $data);
    }

    public function create()
    {
        

        set_title('Add Salary | ' . SITE_NAME);
        $template = new Template();
        $data['pagetitle'] = "Add Employee Salary";
        $data['employees'] = $this->employeeModel->findAll();
        $template->render('admintemplate', 'contents', 'admin/salary/create', $data);
    }

    public function store()
    {
        

        $rules = [
            'employee_id' => 'required|numeric',
            'month' => 'required|numeric|greater_than[0]|less_than_equal_to[12]',
            'year' => 'required|numeric',
            'basic_salary' => 'required|decimal',
            'allowances' => 'permit_empty|decimal',
            'deductions' => 'permit_empty|decimal',
            'net_salary' => 'required|decimal',
            'remarks' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('errmessage', implode('<br>', $this->validator->getErrors()));
            return redirect()->back()->withInput();
        }

        $postData = $this->request->getPost();

        $data = [
            'employee_id' => $postData['employee_id'],
            'month' => $postData['month'],
            'year' => $postData['year'],
            'basic_salary' => $postData['basic_salary'],
            'allowances' => $postData['allowances'] ?? 0,
            'deductions' => $postData['deductions'] ?? 0,
            'net_salary' => $postData['net_salary'],
            'remarks' => $postData['remarks'] ?? null,
        ];

        if (!empty($postData['salary_id'])) {
            $this->salaryModel->update($postData['salary_id'], $data);
            $message = 'Salary record updated successfully.';
        } else {
            $this->salaryModel->insert($data);
            $message = 'Salary record added successfully.';
        }

        $this->session->setFlashdata('message', $message);
        return redirect()->to(site_url('employee-salaries'));
    }

    public function edit($salaryId)
    {
        

        set_title('Edit Salary | ' . SITE_NAME);
        $template = new Template();
        $data['pagetitle'] = "Edit Employee Salary";

        $salary = $this->salaryModel->find($salaryId);
        if (!$salary) {
            $this->session->setFlashdata('errmessage', 'Salary record not found');
            return redirect()->to(site_url('employee-salaries'));
        }

        $data['salary'] = $salary;
        $data['employees'] = $this->employeeModel->findAll();

        $template->render('admintemplate', 'contents', 'admin/salary/create', $data);
    }

    public function delete()
    {
        if (!$this->isAdminLoggedIn) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ]);
        }

        $salaryId = $this->request->getPost('salary_id');

        if (empty($salaryId) || !is_numeric($salaryId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid salary ID'
            ]);
        }

        $salary = $this->salaryModel->find($salaryId);
        if (!$salary) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Salary record not found'
            ]);
        }

        $this->salaryModel->delete($salaryId);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Salary record deleted successfully.'
        ]);
    }

    public function view($salaryId)
    {
        

        set_title('Salary Details | ' . SITE_NAME);
        $template = new Template();
        $data['pagetitle'] = "Salary Details";

        $salary = $this->salaryModel->find($salaryId);
        if (!$salary) {
            $this->session->setFlashdata('errmessage', 'Salary record not found');
            return redirect()->to(site_url('employee-salaries'));
        }

        $employee = $this->employeeModel->find($salary['employee_id']);
        $data['salary'] = $salary;
        $data['employee'] = $employee;

        $template->render('admintemplate', 'contents', 'admin/salary/preview', $data);
    }
}
