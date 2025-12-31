<?php

namespace App\Controllers;

use Mpdf\Mpdf;
use App\Models\SalaryModel;
use App\Models\EmployeeModel;
use App\Controllers\BaseController;

class SalarySlipController extends BaseController
{
    protected $session;
    protected $employeeModel;
    protected $salaryModel;

    public function __construct()
    {
        $this->session = session();
        $this->salaryModel = new SalaryModel();
        $this->employeeModel = new EmployeeModel();
    }

    public function view($salaryId)
    {
        // Get salary data
        $salary = $this->salaryModel->find($salaryId);
        if (!$salary) {
            return redirect()->back()->with('error', 'Salary record not found');
        }

        $employee = $this->employeeModel->find($salary['employee_id']);
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found');
        }

        // Prepare data for the slip
        $data = [
            'employee_id' => $employee['employee_id'],
            'employee_name' => $employee['first_name'] . ' ' . $employee['last_name'],
            'designation' => $employee['designation'],
            'department' => $employee['department'],
            'month_year' => date('F Y', strtotime("{$salary['year']}-{$salary['month']}-01")),
            'basic_salary' => $salary['basic_salary'],
            'allowances' => $salary['allowances'],
            'deductions' => $salary['deductions'],
            'net_salary' => $salary['net_salary'],
            'company_name' => 'Royalxpay Private Limited',
            'company_logo' => FCPATH . 'assets/images/royalxpay_logo.jpg',
            'company_address' => '123 Business Street, City, Country',
            'salary_id' => $salaryId
        ];

        // Check if PDF path exists in database and file exists
        if (!empty($salary['payslip']) && file_exists(FCPATH . $salary['payslip'])) {
            $data["pdf_path"] = base_url($salary['payslip']);
            return view('admin/salary/salary_preview', $data);
        }

        // Generate PDF path (relative to FCPATH)
        $relativePath = 'uploads/salary_slips/' . date('Y/m/') .
            'salary_slip_' . $data['employee_id'] . '_' .
            date('F_Y', strtotime("{$salary['year']}-{$salary['month']}-01")) . '.pdf';

        $pdfPath = FCPATH . $relativePath;

        // Generate PDF if it doesn't exist
        if (!file_exists($pdfPath)) {
            $mpdf = $this->generatePdf($data);

            // Ensure directory exists
            $dir = dirname($pdfPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            // Save the PDF
            $mpdf->Output($pdfPath, 'F');
        }

        // Update database with PDF path
        $this->salaryModel->update($salaryId, ['payslip' => $relativePath]);

        $data["pdf_path"] = base_url($relativePath);
        return view('admin/salary/salary_preview', $data);
    }

    public function download($salaryId)
    {
        $salary = $this->salaryModel->find($salaryId);
        if (!$salary) {
            return redirect()->back()->with('error', 'Salary record not found');
        }

        // If PDF exists in database and filesystem
        if (!empty($salary['payslip']) && file_exists(FCPATH . $salary['payslip'])) {
            return $this->response->download(FCPATH . $salary['payslip'], null, true);
        }

        // Generate on-the-fly if not exists
        $employee = $this->employeeModel->find($salary['employee_id']);
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found');
        }

        $data = [
            'employee_id' => $employee['employee_id'],
            'employee_name' => $employee['first_name'] . ' ' . $employee['last_name'],
            'designation' => $employee['designation'],
            'department' => $employee['department'],
            'month_year' => date('F Y', strtotime("{$salary['year']}-{$salary['month']}-01")),
            'basic_salary' => $salary['basic_salary'],
            'allowances' => $salary['allowances'],
            'deductions' => $salary['deductions'],
            'net_salary' => $salary['net_salary'],
            'company_name' => 'Royalxpay Private Limited',
            'company_logo' => FCPATH . 'assets/images/royalxpay_logo.jpg',
            'company_address' => '123 Business Street, City, Country'
        ];

        $mpdf = $this->generatePdf($data);

        // Output as download
        return $mpdf->Output(
            'salary_slip_' . $data['employee_id'] . '_' .
                date('F_Y', strtotime("{$salary['year']}-{$salary['month']}-01")) . '.pdf',
            'D'
        );
    }

    public function getPdf($salaryId)
    {
        $salary = $this->salaryModel->find($salaryId);
        if (!$salary) {
            return redirect()->back()->with('error', 'Salary record not found');
        }

        if (!empty($salary['payslip']) && file_exists(FCPATH . $salary['payslip'])) {
            return $this->response->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="' . basename($salary['payslip']) . '"')
                ->setBody(file_get_contents(FCPATH . $salary['payslip']));
        }

        return redirect()->back()->with('error', 'Salary slip not found');
    }

    protected function generatePdf($data)
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 20,
            'margin_header' => 5,
            'margin_footer' => 10
        ]);

        $mpdf->SetAuthor($data['company_name']);
        $mpdf->SetTitle('Salary Slip - ' . $data['employee_name']);
        $mpdf->SetSubject('Salary Slip');

        $mpdf->AddPage();
        $html = view('admin/salary/salary_slip_template', $data);
        $mpdf->WriteHTML($html);

        return $mpdf;
    }
}
