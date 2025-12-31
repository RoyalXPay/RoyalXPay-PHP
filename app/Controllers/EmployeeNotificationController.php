<?php

namespace App\Controllers;


use App\Libraries\Template;
use App\Models\EmployeeModel;
use App\Libraries\Paginationnew;
use App\Models\EmployeeSalaryModel;
use App\Controllers\BaseController;

class EmployeeNotificationController extends BaseController
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

        $data['employees'] = $this->employeeModel->findAll();

        $template->render('admintemplate', 'contents', 'admin/salary/index', $data);
    }
}
