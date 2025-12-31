<?php

namespace App\Controllers;

use App\Models\SalaryModel;
use App\Models\CompanyModel;
use App\Libraries\Pagination;
use App\Models\EmployeeModel;
use App\Controllers\BaseController;
use App\Models\ReportingManagerModel;

class SalaryController extends BaseController
{
    protected $session;
    protected $salaryModel;
    protected $companyModel;
    protected $employeeModel;
    protected $reportingManagerModel;

    public function __construct()
    {
        $this->session = session();
        $this->salaryModel = new SalaryModel();
        $this->companyModel = new CompanyModel();
        $this->employeeModel = new EmployeeModel();
        $this->reportingManagerModel = new ReportingManagerModel();
          $this->initializePrivileges();
    }

    public function index()
    {
        set_title('Employee Salaries | ' . SITE_NAME);
       $companyId  = session('user_id');
        $userType   = session('user_type');

        $data = [
            'action' => "salary",
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
        $totalRecord = $this->salaryModel->getSalariesWithEmployees($data['searchArray'], 0, 0, true);
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $this->salaryModel->getSalariesWithEmployees($data['searchArray'], $startLimit, $Limit);
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

        return view('admin/salary/index', $data);
    }

    public function create()
{
    set_title('Add Salary | ' . SITE_NAME);
    $data['pagetitle'] = "Add Employee Salary";

    $userType = $this->session->get('user_type');
    $loggedUserId = $this->session->get('user_id');

    // 🔥 ONLY this line changed
    $data['employees'] = ($userType === 'superadmin')
        ? $this->employeeModel->findAll()
        : $this->employeeModel->where('user_id', $loggedUserId)->findAll();

    return view('admin/salary/create', $data);
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
        return redirect()->to(site_url('salary'));
    }

  public function edit($salaryId)
{
    set_title('Edit Salary | ' . SITE_NAME);
    $data['pagetitle'] = "Edit Employee Salary";

    $salary = $this->salaryModel->find($salaryId);
    if (!$salary) {
        $this->session->setFlashdata('errmessage', 'Salary record not found');
        return redirect()->to(site_url('salary'));
    }

    $data['salary'] = $salary;

    // 🔥 Role-based employee listing
    $userType = $this->session->get('user_type');
    $loggedUserId = $this->session->get('user_id');

    $data['employees'] = ($userType === 'superadmin')
        ? $this->employeeModel->findAll()
        : $this->employeeModel->where('user_id', $loggedUserId)->findAll();

    return view('admin/salary/create', $data);
}


    public function delete()
    {

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
        $data['pagetitle'] = "Salary Details";

        $salary = $this->salaryModel->find($salaryId);
        if (!$salary) {
            $this->session->setFlashdata('errmessage', 'Salary record not found');
            return redirect()->to(site_url('salary'));
        }

        $employee = $this->employeeModel->find($salary['employee_id']);
        $data['salary'] = $salary;
        $data['employee'] = $employee;
        return view('admin/salary/preview', $data);
    }
}
