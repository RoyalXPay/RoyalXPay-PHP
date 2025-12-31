<?php

namespace App\Controllers;

use App\Libraries\EmailSms;
use App\Models\ProductModel;
use App\Libraries\Paginationnew;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as excel;

class FinancialServicesController extends BaseController
{
    protected $session;
    protected $productModel;
    protected $isAdminLoggedIn;

    public function __construct()
    {
        $this->session = session();
        $this->productModel = new ProductModel();
        $this->isAdminLoggedIn = $this->session->get('isAdminLoggedIn');
    }

    public function traditionalBanking()
    {
        if (!$this->isAdminLoggedIn) {
            return redirect()->to(site_url('admin'));
        }

        $data = [
            'action' => "traditinal-banking",
            'pagetitle' => "Users Details",
        ];

        $paginationnew = new Paginationnew();
        $searchArray = [];
        $txtsearch = $this->request->getGet('txtsearch');
        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        if ($txtsearch) {
            $searchArray['txtsearch'] = $txtsearch;
        }

        if ($startDate) {
            $searchArray['startDate'] = $startDate;
        }

        if ($endDate) {
            $searchArray['endDate'] = $endDate;
        }

        $page = $this->request->getGet('page');
        $page = $page ? $page : 1;
        $Limit = PER_PAGE_RECORD;

        $referralCode = $this->session->get('refer_code');
        $admin_type = $this->session->get('admin_type');

        $totalRecord = $this->productModel->getData($searchArray, '', '', '1', $admin_type, $referralCode);
        $startLimit = ($page - 1) * $Limit;

        $data['reverse'] = $totalRecord - ($page - 1) * $Limit;
        $data['startLimit'] = $startLimit;

        $pagination = $paginationnew->getPaginate($totalRecord, $page, $Limit);
        $data['txtsearch'] = $txtsearch;
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $pagination;
        $data['endDate']  = $endDate;
        $data['startDate']  = $startDate;
        $data["searchArray"] = $searchArray;
        $data["admin_type"] = $admin_type;

        if (!empty($admin_type) && in_array($admin_type, ['partner', 'associate_partner', 'distributor', 'merchant', 'admin'])) {
            $data['results'] = $this->productModel->getData($searchArray, $startLimit, $Limit, false, $admin_type, $referralCode);
        } else {
            $data['results'] = '';
        }

        $this->template->render('admintemplate', 'contents', 'admin/traditionalBanking/index', $data);
    }

    public function showUserDetails()
    {

        if (!$this->isAdminLoggedIn) {
            return redirect()->to(site_url('admin'));
        }

        set_title('Preview | ' . SITE_NAME);
        $data['pagetitle'] = "User Details";

        $id = $this->request->getGet('id');
        $data['userDetails'] = $this->productModel->where('product_id', $id)->first();

        $this->template->render('admintemplate', 'contents', 'admin/traditionalBanking/preview', $data);
    }

    public function delete()
    {
        $id = $this->request->getGet('id');

        if (is_numeric($id) && !empty($id)) {

            try {
                $this->productModel->where('product_id', $id)->delete();

                $this->session->setFlashdata('message', 'Deleted successfully.');
            } catch (\Exception $e) {

                $this->session->setFlashdata('errmessage', $e->getMessage());
            }
        } else {

            $this->session->setFlashdata('errmessage', 'something went wrong...');
        }

        return redirect()->to(site_url('traditinal-banking'));
    }

    public function uploadUserData()
    {
        $filename = $this->request->getFile('excelFile');
        $rules = $this->validate([
            'excelFile' => 'uploaded[excelFile]|max_size[excelFile,500]|ext_in[excelFile,csv,xlsx]',
        ]);
        if ($rules == true) {
            $originalFileName = $filename->getName();

            $tempFileName = $filename->getTempName();
            $arr_file = explode(".", $originalFileName);
            $extension = end($arr_file);
            if ('csv' == $extension) {
                $reader = new Csv();
            } else {
                $reader = new excel();
            }
            $spreadsheet = $reader->load($tempFileName);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            if (!empty($sheetData)) {
                for ($i = 1; $i < count($sheetData); $i++) {

                    $first_name = $sheetData[$i][0];
                    $last_name = $sheetData[$i][1];
                    $mobile_no = $sheetData[$i][2];
                    $email = $sheetData[$i][3];
                    $referred_by = $sheetData[$i][4];
                    $pan = $sheetData[$i][5];
                    $dob = $sheetData[$i][6];
                    $company = $sheetData[$i][7];
                    $occupation = $sheetData[$i][8];
                    $monthly_salary = $sheetData[$i][9];
                    $itr_amount = $sheetData[$i][10];
                    $gender = $sheetData[$i][11];
                    $pincode = $sheetData[$i][12];
                    $address = $sheetData[$i][13];
                    $landmark = $sheetData[$i][14];
                    $state = $sheetData[$i][15];
                    $category = $sheetData[$i][16];
                    $category_id = $sheetData[$i][17];

                    $data = [
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'mobile_no' => $mobile_no,
                        'email' => $email,
                        'referred_by' => $referred_by,
                        'pan' => $pan,
                        'dob' => $dob,
                        'company' => $company,
                        'occupation' => $occupation,
                        'monthly_salary' => $monthly_salary,
                        'itr_amount' => $itr_amount,
                        'gender' => $gender ?? '',
                        'pincode' => $pincode ?? '',
                        'address' => $address ?? '',
                        'landmark' => $landmark ?? '',
                        'state' => $state ?? '',
                        'category' => $category ?? '',
                        // 'category_id' => $sheetData[$i][18] ?? '',
                    ];
                    $this->productModel->insert($data);
                }
                $this->session->setFlashdata('message', 'wallet point added successfully.');
            } else {
                $this->session->setFlashdata('message', 'File is Empty or not valid data.');
            }
        } else {

            $this->session->setFlashdata('errmessage', 'Please Uploade Csv or excel file...');
        }
        return redirect()->to(site_url("traditinal-banking"));
    }

    public function export()
    {
        $result = $this->productModel->get()->getResultArray();
        // print_r($result);exit;
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue("A1", "Sl");
        $sheet->setCellValue("B1", "Name");
        $sheet->setCellValue("C1", "Email");
        $sheet->setCellValue("D1", "Mobile");
        $sheet->setCellValue("E1", "Pan");
        $sheet->setCellValue("F1", "Refer By");
        $sheet->setCellValue("G1", "D.O.B");
        $sheet->setCellValue("H1", "Company");
        $sheet->setCellValue("I1", "Occupation");
        $sheet->setCellValue("J1", "Monthly Salary");
        $sheet->setCellValue("K1", "ITR Amount");
        $sheet->setCellValue("L1", "Gender");
        $sheet->setCellValue("M1", "Pincode");
        $sheet->setCellValue("N1", "Address");
        $sheet->setCellValue("O1", "Landmark");
        $sheet->setCellValue("P1", "State");
        $sheet->setCellValue("Q1", "Category");
        $sheet->setCellValue("R1", "Created At");
        $sheet->setCellValue("S1", "Updated At");

        $count = 2;
        foreach ($result as $value) {
            $sheet->setCellValue("A" . $count, $count - 1);
            $sheet->setCellValue("B" . $count, $value['first_name'] . ' ' . $value['last_name']);
            $sheet->setCellValue("C" . $count, $value['email']);
            $sheet->setCellValue("D" . $count, $value['mobile_no']);
            $sheet->setCellValue("E" . $count, $value['pan']);
            $sheet->setCellValue("F" . $count, $value['referred_by']);
            $sheet->setCellValue("G" . $count, $value['dob']);
            $sheet->setCellValue("H" . $count, $value['company']);
            $sheet->setCellValue("I" . $count, $value['occupation']);
            $sheet->setCellValue("J" . $count, $value['monthly_salary']);
            $sheet->setCellValue("K" . $count, $value['itr_amount']);
            $sheet->setCellValue("L" . $count, $value['gender']);
            $sheet->setCellValue("M" . $count, $value['pincode']);
            $sheet->setCellValue("N" . $count, $value['address']);
            $sheet->setCellValue("O" . $count, $value['landmark']);
            $sheet->setCellValue("P" . $count, $value['state']);
            $sheet->setCellValue("Q" . $count, $value['category']);
            $sheet->setCellValue("R" . $count, $value['created_at']);
            $sheet->setCellValue("S" . $count, $value['updated_at']);

            $count++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = "traditional-banking.xlsx";
        $writer->save($fileName);

        return $this->response->download($fileName, null)->setFileName($fileName);
    }
}
