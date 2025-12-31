<?php

namespace App\Controllers;
require_once ROOTPATH . 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use App\Models\UsersModel;
use App\Models\CompanyModel;
use App\Models\EmployeeModel;
use App\Libraries\Pagination;
use App\Models\AttendanceModel;
use App\Controllers\BaseController;
use App\Models\ReportingManagerModel;

class EmployeeController extends BaseController
{
    protected $db;
    protected $session;
    protected $usersModel;
    protected $companyModel;
    protected $employeeModel;
    protected $reportingManagerModel;

    public function __construct()
    {
         $this->session = session();
        $this->db = \Config\Database::connect();
        $this->usersModel = new UsersModel();
        $this->employeeModel = new EmployeeModel();
        $this->reportingManagerModel = new ReportingManagerModel();
            $this->initializePrivileges(); // This calls method from BaseController

    }
    public function index()
    {
        set_title('Employee List | ' . SITE_NAME);

        $companyId  = session('user_id');
        $userType   = session('user_type');

        $data = [
            'action'      => "employees",
              'startLimit'   => 0,
            'reverse'      => 0,
            'pagination'   => '',
            'results'      => [],
            'searchArray'  => [],
        ];

        $customPagination = new Pagination();

        // Collect search filters
        $searchFields = $this->request->getGet();
        foreach ($searchFields as $field => $searchValue) {
            $data['searchArray'][$field] = trim($searchValue);
        }

            // If logged in as company, force company_id in search
        if ($userType === 'Merchant'  && !empty($companyId)) {
            $data['searchArray']['user_id'] = $companyId;
        }

        $selectedCompanyId = $data['searchArray']['user_id'] ?? null;
     
       
        $data['managers'] = [];
        if (!empty($selectedCompanyId)) {
            $data['managers'] = $this->reportingManagerModel->getReportingManagers([
                'companyId' => $selectedCompanyId
            ]);
        }

        if (empty($data['managers']) && $userType === 'Merchant') {
            $data['managers'] = $this->reportingManagerModel->getReportingManagers([
                'companyId' => $companyId
            ]);
        }


      
        $data['companies'] = $this->usersModel
            ->where('status', 'Active')
            ->where('user_type !=', 'superadmin')
            ->findAll();
                   $Limit = 10;
        $page = (int) $this->request->getGet('page') ?: 1;
        $totalRecord = $this->employeeModel->getEmployees($data['searchArray'], '', '', true);
   
        $startLimit = ($page - 1) * $Limit;
                $data['startLimit'] = $startLimit;

        $data['reverse'] = $totalRecord - $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);
        $data['results'] = $this->employeeModel->getEmployees($data['searchArray'], $startLimit, $Limit);
       
        return view('admin/employee/index', $data);
    }

public function update_status()
{
    $employee_id = $this->request->getPost('employee_id');
    $status = $this->request->getPost('status');

    // Use the existing model instance from the constructor
    $updated = $this->employeeModel->update($employee_id, ['status' => $status]);

    if ($updated) {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Status updated successfully.'
        ]);
    } else {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update status.'
        ]);
    }
}


    public function createold()
    {
        set_title('Add Employee | ' . SITE_NAME);

        $data['pagetitle'] = "Add Employee";
        $userType = $this->session->get('user_type');

       $companyId = $this->session->get('user_id'); // or get company ID if superadmin
            $searchArray = [];
                $searchArray['company_id'] = $companyId;

             
        $data['companies'] = $this->usersModel
            ->where('status', 'Active')
            ->where('user_type !=', 'superadmin')
            ->findAll();

            $managers = $this->employeeModel->getEmployees($searchArray, 0, 1000);
            $data['managers'] = json_decode(json_encode($managers), true);

        return view('admin/employee/create', $data);
    }

public function create()
{
    set_title('Add Employee | ' . SITE_NAME);

    $data['pagetitle'] = "Add Employee";

    $userType = $this->session->get('user_type');
    $loggedUserId = $this->session->get('user_id');

    $searchArray = [];

    // SUPERADMIN → see all employees (no filter)
    if ($userType !== 'superadmin') {
        // MERCHANT → see only his employees
        $searchArray['user_id'] = $loggedUserId;
    }

    // Company dropdown: superadmin sees all companies,
    // merchant sees only self
    if ($userType === 'superadmin') {
        $data['companies'] = $this->usersModel
            ->where('status', 'Active')
            ->where('user_type !=', 'superadmin')
            ->findAll();
    } else {
        $data['companies'] = $this->usersModel
            ->where('status', 'Active')
            ->where('user_id', $loggedUserId)
            ->findAll();
    }

    // Get managers using filtered search (superadmin gets all)
    $managers = $this->employeeModel->getEmployees($searchArray, 0, 1000);
    $data['managers'] = json_decode(json_encode($managers), true);

    return view('admin/employee/create', $data);
}


    public function approve($employeeId)
    {
        $userType = session('user_type');

        // Only checkers, superadmins, or merchants can approve
        if (!in_array($userType, ['checker', 'superadmin', 'merchant'])) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $employee = $this->employeeModel->find($employeeId);

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        // Update status to Active
        $this->employeeModel->update($employeeId, [
            'status' => 'Active',
            'approved_by' => session('user_id'),
            'approved_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(site_url('employees/view/' . $employeeId))
                        ->with('success', 'Employee approved successfully.');
    }

public function edit($employeeId)
{
    set_title('Edit Employee | ' . SITE_NAME);

    $data['pagetitle'] = "Edit Employee";

    $employee = $this->employeeModel->where('employee_id', $employeeId)->first();

    if (!$employee) {
        $this->session->setFlashdata('error', 'Employee not found');
        return redirect()->to(site_url('employees'));
    }

    $reportingManager = $this->reportingManagerModel
        ->where('employee_id', $employeeId)
        ->select('manager_id')
        ->first();

    $data['reporting_manager_id'] = $reportingManager['manager_id'] ?? null;

    $userType = $this->session->get('user_type');
    $loggedUserId = $this->session->get('user_id');

    // ⛔ SECURITY CHECK —
    // Merchant must NOT edit someone else’s employee
    if ($userType !== 'superadmin' && $employee['user_id'] != $loggedUserId) {
        $this->session->setFlashdata('error', 'Unauthorized access');
        return redirect()->to(site_url('employees'));
    }

    // 🎯 Manager dropdown filtering
    $managerSearch = [];

    if ($userType !== 'superadmin') {
        // Merchant sees only his own managers
        $managerSearch['companyId'] = $loggedUserId;
    }

    // Exclude current employee
    $managerSearch['employeeId'] = $employeeId;

    // Company dropdown
    if ($userType === 'superadmin') {
        $data['companies'] = $this->usersModel
            ->where('status', 'Active')
            ->where('user_type !=', 'superadmin')
            ->findAll();
    } else {
        // Merchant → only his company
        $data['companies'] = $this->usersModel
            ->where('user_id', $loggedUserId)
            ->findAll();
    }

    $data['employee'] = $employee;

    // Managers list
    $data['managers'] = $this->employeeModel->getManagerCandidates($managerSearch);

    return view('admin/employee/create', $data);
}


    public function editold($employeeId)
    {
        set_title('Edit Employee | ' . SITE_NAME);

        $data['pagetitle'] = "Edit Employee";
        $employee = $this->employeeModel->where('employee_id', $employeeId)->first();

        if (!$employee) {
            $this->session->setFlashdata('error', 'Employee not found');
            return redirect()->to(site_url('employees'));
        }

        $reportingManager = $this->reportingManagerModel
            ->where('employee_id', $employeeId)
            ->select('manager_id')
            ->first();

        $data['reporting_manager_id'] = $reportingManager['manager_id'] ?? null;

        $userType = $this->session->get('user_type');
        $companyId = $this->session->get('user_id');

        // Include 'search' => 'all' here too
        $managerOptions = [
            'search' => 'all',
            'employeeId' => $employeeId
        ];

        if ($userType === 'merchant') {
            $data['companies'] = []; // No dropdown
            $managerOptions['companyId'] = $companyId;
        } else {
            $data['companies'] = $this->usersModel
                ->where('status', 'Active')
                ->findAll();
        }

        $data['employee'] = $employee;
        $data['managers'] = $this->reportingManagerModel->getReportingManagers($managerOptions);

        return view('admin/employee/create', $data);
    }

   public function store()
{
    
    $rules = [
        'company_id' => 'required',
        'first_name' => 'required',
        'phone' => 'required|numeric|min_length[10]|max_length[15]',
        'email' => 'required|valid_email|max_length[150]',
        'status' => 'required|in_list[Active,Inactive,Suspended]',
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
    }

    $employeeId = $this->request->getPost('employee_id');
    $companyId =  session()->get('user_id');;
    $email = $this->request->getPost('email');
    $password = $this->request->getPost('password') ?? 'default@123';
    $reportingManagerId = $this->request->getPost('manager_reporting');

    // Check for duplicate email
    $existingEmployee = $this->employeeModel
        ->where('email', $email)
        ->where('employee_id !=', $employeeId)
        ->first();

    if ($existingEmployee) {
        return redirect()->back()->withInput()->with('error', "The email address '{$email}' is already being used by another employee.");
    }

    // Prepare employee data
    $employeeData = [
        'company_id' => $companyId,
        'user_id' => $companyId,

        'first_name' => $this->request->getPost('first_name'),
        'last_name' => $this->request->getPost('last_name'),
        'email' => $email,
        'phone' => $this->request->getPost('phone'),
        'designation' => $this->request->getPost('designation'),
        'department' => $this->request->getPost('department'),
        'address' => $this->request->getPost('address'),
        'dob' => $this->request->getPost('dob'),
        'gender' => $this->request->getPost('gender'),
        'manager_reporting' => $reportingManagerId,
        'status' => $this->request->getPost('status'),
        'joining_date' => $this->request->getPost('joining_date'),
        'geo_tracking' => (int)$this->request->getPost('geo_tracking'),
        'access_role' => $this->request->getPost('access_role'),

    ];

    // Handle profile image upload
    $profileImagePath = null;
    $profileImage = $this->request->getFile('profile_image');
    if ($profileImage && $profileImage->isValid() && !$profileImage->hasMoved()) {
        $profileImagePath = $profileImage->getRandomName();
        $profileImage->move(FCPATH . 'uploads/employees', $profileImagePath);
        $employeeData['profile_image'] = $profileImagePath;

        // Delete old image if updating
        if ($employeeId) {
            $oldImage = $this->employeeModel->find($employeeId)['profile_image'] ?? null;
            if ($oldImage && file_exists(FCPATH . 'uploads/employees/' . $oldImage)) {
                unlink(FCPATH . 'uploads/employees/' . $oldImage);
            }
        }
    }

    $this->db->transStart();

    try {
       if ($employeeId) {
    // Get original data BEFORE updating
    $originalEmployee = $this->employeeModel->find($employeeId);

    // Update employee code if posted
    $postedCode = $this->request->getPost('employee_code');
    if ($postedCode) {
        $employeeData['employee_code'] = $postedCode;
    }

    // Update employee
    $this->employeeModel->update($employeeId, $employeeData);

    // Compare original vs new data
    $changedFields = [];
    foreach ($employeeData as $key => $newValue) {
        $originalValue = $originalEmployee[$key] ?? '';
        if ((string)$originalValue !== (string)$newValue) {
            $changedFields[$key] = $newValue;
        }
    }

    // Log changes if any
    if (!empty($changedFields)) {
        $historyData = [
            'employee_id' => $employeeId,
            'action' => 'update',
            'changed_by' => session()->get('user_id'),
            'change_details' => json_encode($changedFields),
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->table('employee_history')->insert($historyData);
    }

    $message = "Employee updated successfully";
}
 else {
            // ===== CREATE =====
            $company = $this->usersModel->find($companyId);         // Employee code: use posted value if provided, else generate
            $employeeCode = $this->request->getPost('employee_code');
            if (!$employeeCode) {
                $cleanedName = preg_replace('/[^a-zA-Z]/', '', $company['name']);
                $companyPrefix = strtoupper(substr($cleanedName, 0, 3));
                if (strlen($companyPrefix) < 3) $companyPrefix = str_pad($companyPrefix, 3, 'X');

                for ($i = 0; $i < 1000; $i++) {
                    $number = rand(10000, 99999);
                    $employeeCode = $companyPrefix . $number;
                    if (!$this->employeeModel->where('employee_code', $employeeCode)->first()) break;
                }
            }
            $employeeData['employee_code'] = $employeeCode;

            
            $employeeData['created_by'] = session()->get('user_id');
$employeeData['created_by_type'] = strtolower(session()->get('user_type')); // 👈 NEW LINE

            

            // 2️⃣ Create employee
          $employeeId = $this->employeeModel->insert($employeeData);

if (!$employeeId) {
    $error = $this->db->error();
    print_r($error); die();
}
              // After saving or updating employee
       // Add CREATE log
$this->db->table('employee_history')->insert([
    'employee_id' => $employeeId,
    'action' => 'create',
    'changed_by' => session()->get('user_id'),
    'change_details' => json_encode($employeeData),
    'created_at' => date('Y-m-d H:i:s')
]);

            $message = "Employee created successfully";
        }

        // ===== Reporting Manager =====
        if (empty($reportingManagerId)) {
            $this->reportingManagerModel->where('employee_id', $employeeId)->delete();
        } else {
            $existingMapping = $this->reportingManagerModel->where('employee_id', $employeeId)->first();
            if ($existingMapping) {
                $this->reportingManagerModel->update($existingMapping['id'], [
                    'manager_id' => $reportingManagerId,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $this->reportingManagerModel->save([
                    'employee_id' => $employeeId,
                    'manager_id' => $reportingManagerId,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $this->db->transComplete();

        $this->session->setFlashdata('success', $message);
        return redirect()->to("employees/view/{$employeeId}");

    } catch (\Exception $e) {
        $this->db->transRollback();
        if (!empty($profileImagePath) && file_exists(FCPATH . 'uploads/employees/' . $profileImagePath)) {
            unlink(FCPATH . 'uploads/employees/' . $profileImagePath);
        }
        return redirect()->back()->withInput()->with('error', "Failed to save employee: " . $e->getMessage());
    }
}

public function view($employeeId)
{
    set_title('Employee Details | ' . SITE_NAME);

    $data['pageTitle'] = "Employee Details";

    // Fetch current employee details
    $employee = $this->employeeModel->where('employee_id', $employeeId)->first();

    if (!$employee) {
        $this->session->setFlashdata('error', 'Employee not found');
        return redirect()->to(site_url('employees'));
    }

    // ✅ Fetch manager name using manager_reporting ID
    if (!empty($employee['manager_reporting'])) {
        $manager = $this->employeeModel
            ->select("CONCAT(first_name, ' ', last_name) as manager_name")
            ->where('employee_id', $employee['manager_reporting'])
            ->first();

        $employee['manager_name'] = $manager['manager_name'] ?? 'N/A';
    } else {
        $employee['manager_name'] = 'N/A';
    }

    $data['employee'] = $employee;

    // ✅ Fetch last 10 employees (optional filter by user_id)
    $userId = $employee['user_id'] ?? null;
    $data['lastEmployees'] = $this->employeeModel
        ->where('user_id', $userId)
        ->orderBy('created_at', 'DESC')
        ->limit(10)
        ->findAll();

    return view('admin/employee/preview', $data);
}

public function bulkUpload()
{
    set_title('Bulk Upload Employees | ' . SITE_NAME);
    return view('admin/employee/bulk_upload');
}

public function importExcel()
{
    $file = $this->request->getFile('employee_file');
    if (!$file->isValid()) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid file upload.'
        ]);
    }

    $filePath = $file->getTempName();
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

   $expectedHeaders = [
    'first name', 'last name', 'email', 'phone', 'employee code',
    'designation', 'department', 'gender', 'reporting manager',
    'date of birth', 'status', 'joining date'
];

$actualHeaders = array_map('trim', $rows[0]);
$actualHeadersLower = array_map('strtolower', $actualHeaders);

// Normalize headers by removing anything in parentheses
$normalize = fn($h) => preg_replace('/\s*\(.*?\)/', '', $h);

$expectedNormalized = array_map($normalize, $expectedHeaders);
$actualNormalized = array_map($normalize, $actualHeadersLower);

// Check only first 12 headers
if (array_slice($actualNormalized, 0, count($expectedNormalized)) !== $expectedNormalized) {
    return $this->response->setJSON([
        'status' => 'error',
        'message' => 'Incorrect format. Please download the correct template and try again.'
    ]);
}
    unset($rows[0]);

    $employeeModel = new \App\Models\EmployeeModel();
    $user_id = $this->session->get('user_id');

    $resultRows = []; // Track inserted + skipped
    $errors = [];

    foreach ($rows as $index => $row) {
        if (empty(array_filter($row))) {
            continue;
        }

        $rowNumber = $index + 2;

        if (empty($row[0]) || empty($row[2])) {
            $resultRows[] = [
                'row' => $rowNumber,
                'email' => $row[2],
                'name' => $row[0] . ' ' . $row[1],
                'status' => false,
                'message' => 'Missing required fields'
            ];
            continue;
        }

        $existing = $employeeModel->where('email', $row[2])->first();
        if ($existing) {
            $resultRows[] = [
                'row' => $rowNumber,
                'email' => $row[2],
                'name' => $row[0] . ' ' . $row[1],
                'status' => false,
                'message' => 'Email already exists'
            ];
            continue;
        }

        try {
            $data = [
                'first_name'       => $row[0],
                'last_name'        => $row[1],
                'email'            => $row[2],
                'phone'            => $row[3],
                'employee_code'    => $row[4],
                'designation'      => $row[5],
                'department'       => $row[6],
                'gender'           => $row[7],
                'reporting_manage' => $row[8],
                'dob'              => !empty($row[9]) ? date('Y-m-d', strtotime($row[9])) : null,
                'status'           => $row[10],
                'joining_date'     => !empty($row[11]) ? date('Y-m-d', strtotime($row[11])) : null,
                'user_id'          => $user_id,
                'created_at'       => date('Y-m-d H:i:s')
            ];

            $employeeModel->insert($data);

            $resultRows[] = [
                'row' => $rowNumber,
                'email' => $row[2],
                'name' => $row[0] . ' ' . $row[1],
                'status' => true,
                'message' => 'Inserted successfully'
            ];

        } catch (\Exception $e) {
            $resultRows[] = [
                'row' => $rowNumber,
                'email' => $row[2],
                'name' => $row[0] . ' ' . $row[1],
                'status' => false,
                'message' => 'Insert error'
            ];
        }
    }

    if (empty($resultRows)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No data found in file.'
        ]);
    }

    $insertedCount = count(array_filter($resultRows, fn($r) => $r['status']));

    if ($insertedCount == 0) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No valid data inserted.',
            'uploaded' => $resultRows
        ]);
    }

    return $this->response->setJSON([
        'status' => 'success',
        'message' => "$insertedCount employees uploaded successfully.",
        'uploaded' => $resultRows
    ]);
}





public function importPreview()
{
    $file = $this->request->getFile('excel_file');
    if (!$file->isValid()) {
        return redirect()->back()->with('error', 'Invalid file uploaded.');
    }

    $ext = $file->getClientExtension();
    if (!in_array($ext, ['xls', 'xlsx'])) {
        return redirect()->back()->with('error', 'Please upload only Excel files.');
    }

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
    $data = $spreadsheet->getActiveSheet()->toArray();

    // Remove header row
    unset($data[0]);

    $previewData = [];
    foreach ($data as $row) {
        if (empty(array_filter($row))) continue;

        $previewData[] = [
            'first_name' => $row[0],
            'last_name' => $row[1],
            'email' => $row[2],
            'phone' => $row[3],
            'designation' => $row[4],
            'department' => $row[5],
            'gender' => $row[6],
            'dob' => $row[7],
            'joining_date' => $row[8],
            'status' => $row[9],
            'address' => $row[10],
        ];
    }

    return view('admin/employee/bulk_preview', ['previewData' => $previewData]);
}

/**
 * Save & Submit after preview
 */
public function saveBulk()
{
    $employees = $this->request->getPost('employees');
    if (empty($employees)) {
        return redirect()->back()->with('error', 'No employees found to save.');
    }

    $companyId = session('user_id');

    $this->db->transStart();
    try {
        foreach ($employees as $emp) {
            if (empty($emp['email']) || empty($emp['first_name'])) continue;

            $company = $this->usersModel->find($companyId);
            $cleanedName = preg_replace('/[^a-zA-Z]/', '', $company['name']);
            $companyPrefix = strtoupper(substr($cleanedName, 0, 3));
            if (strlen($companyPrefix) < 3) {
                $companyPrefix = str_pad($companyPrefix, 3, 'X');
            }

            do {
                $employeeCode = $companyPrefix . rand(10000, 99999);
            } while ($this->employeeModel->where('employee_code', $employeeCode)->first());

            $employeeData = [
                'company_id' => $companyId,
                'employee_code' => $employeeCode,
                'first_name' => $emp['first_name'],
                'last_name' => $emp['last_name'],
                'email' => $emp['email'],
                'phone' => $emp['phone'],
                'designation' => $emp['designation'],
                'department' => $emp['department'],
                'gender' => $emp['gender'],
                'dob' => $emp['dob'],
                'joining_date' => $emp['joining_date'],
                'status' => $emp['status'] ?: 'Active',
                'address' => $emp['address'],
                'created_by' => $companyId,
            ];

            $this->employeeModel->insert($employeeData);
        }

        $this->db->transComplete();
        $this->session->setFlashdata('success', 'Employees imported successfully.');
        return redirect()->to('employees');
    } catch (\Exception $e) {
        $this->db->transRollback();
        return redirect()->back()->with('error', 'Error while saving: ' . $e->getMessage());
    }
}

    public function delete()
    {
        $employeeId = $this->request->getPost('employee_id');

        // Validate employee ID
        if (empty($employeeId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Employee ID is required to proceed with deletion.'
            ]);
        }

        // Fetch the employee record
        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No employee found with the provided ID.'
            ]);
        }

        // Begin database transaction
        $this->db->transStart();

        try {
            // Delete employee's profile image if it exists
            if (!empty($employee['profile_image'])) {
                $profileImagePath = ROOTPATH . 'public/uploads/employees/' . $employee['profile_image'];
                if (file_exists($profileImagePath)) {
                    unlink($profileImagePath);
                }
            }

            // Delete associated user and their profile image if exists
            if (!empty($employee['user_id'])) {
                $user = $this->usersModel->find($employee['user_id']);

                if ($user) {
                    if (!empty($user['profile_image'])) {
                        $userImagePath = ROOTPATH . 'public/uploads/users/' . $user['profile_image'];
                        if (file_exists($userImagePath)) {
                            unlink($userImagePath);
                        }
                    }

             
                    $this->usersModel->delete($user['user_id']);
                }
            }

            // Delete the employee record
                   $this->db->table('employee_history')->insert([
                    'employee_id' => $employeeId,
                    'action' => 'delete',
                    'changed_by' => session()->get('user_id'),
                    'change_details' => null,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            $this->employeeModel->delete($employeeId);

            // Delete reporting manager relationships
            $this->reportingManagerModel
                ->where('employee_id', $employeeId)
                ->orWhere('manager_id', $employeeId)
                ->delete();

            // Complete transaction
            $this->db->transComplete();

           

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Employee and related user data have been successfully deleted.'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while deleting the employee: ' . $e->getMessage()
            ]);
        }
    }

    public function lastUpdatedHistory($employeeId)
    {
        $history = $this->db->table('employee_history as h')
            ->select('h.id, h.action, h.change_details, h.created_at, u.name')
            ->join('users u', 'u.user_id = h.changed_by', 'left')
            ->where('h.employee_id', $employeeId)
            ->orderBy('h.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return $this->response->setJSON($history);
    }


    public function getAttendance()
    {
        $employeeId = $this->request->getGet('employee_id');
        $month = $this->request->getGet('month');

        if (!$employeeId || !$month) {
            return $this->response->setJSON([
                'attendance' => [],
                'summary' => [],
                'error' => 'Invalid parameters.',
            ]);
        }

        // Calculate first and last day of the selected month
        $startDate = $month . '-01';
        $endDate = date("Y-m-t", strtotime($startDate));

        $attendanceModel = new AttendanceModel();

        // Fetch attendance for that employee and month
        $attendanceRecords = $attendanceModel
            ->where('employee_id', $employeeId)
            ->where('attendance_date >=', $startDate)
            ->where('attendance_date <=', $endDate)
            ->findAll();

        $events = [];
        $summary = [
            'present' => 0,
            'absent' => 0,
            'holiday' => 0,
            'wfh' => 0,
            'sick' => 0,
            'casual' => 0,
        ];

        $colorMap = [
            'present' => '#28a745',
            'absent' => '#dc3545',
            'holiday' => '#ffc107',
            'wfh' => '#17a2b8',
            'sick' => '#6c757d',
            'casual' => '#fd7e14',
        ];

        foreach ($attendanceRecords as $record) {
            $status = strtolower($record['status']);

            $events[] = [
                'title' => ucfirst($status),
                'start' => $record['attendance_date'],
                'color' => $colorMap[$status] ?? '#000000',
            ];

            if (isset($summary[$status])) {
                $summary[$status]++;
            }
        }

        return $this->response->setJSON([
            'attendance' => $events,
            'summary' => $summary,
        ]);
    }

    public function getAttendanceCalendar()
    {
        $employeeId = $this->request->getGet('employee_id');
        $month = $this->request->getGet('month');
        $year = $this->request->getGet('year');

        $attendanceModel = new AttendanceModel();
        $monthFormatted = str_pad($month, 2, '0', STR_PAD_LEFT);
        $attendanceData = $attendanceModel->where('employee_id', $employeeId)
            ->like('attendance_date', "$year-$monthFormatted-", 'after')
            ->findAll();

        // Create a calendar view
        return view('admin/employee/attendance_calendar', [
            'attendanceData' => $attendanceData,
            'month' => $month,
            'year' => $year
        ]);
    }




}
