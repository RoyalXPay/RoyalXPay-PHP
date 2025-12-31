<?php

namespace App\Controllers;

use App\Libraries\Pagination;
use App\Models\SizesModel;
use App\Models\UsersModel;
use App\Models\ColorsModel;
use App\Models\ProductModel;
use App\Models\ContractModel;
use App\Models\QuotationModel;
use App\Models\ResolutionModel;
use App\Models\GlassTypesModel;
use App\Models\TouchTypesModel;
use App\Models\ProductMasterModel;
use App\Models\ContractProductsModel;
use App\Models\ContractDeliveryModel;
use App\Models\ContractPaymentsModel;
use App\Models\QuotationProductsModel;
use App\Controllers\BaseController;

class ReportsController extends BaseController
{

    protected $sizesModel;
    protected $usersModel;
    protected $colorsModel;
    protected $productModel;
    protected $productMasterModel;

    protected $glassTypesModel;
    protected $touchTypesModel;
    protected $resolutionModel;
    protected $customPagination;

    protected $quotationModel;
    protected $quotationProductsModel;

    protected $contractModel;
    protected $contractProductsModel;
    protected $contractDeliveryModel;
    protected $contractPaymentsModel;
    // Constructor to initialize models
    public function __construct()
    {
        // Initialize models
        $this->usersModel = new UsersModel();
        $this->customPagination = new Pagination();

        $this->sizesModel = new SizesModel();
        $this->colorsModel = new ColorsModel();
        $this->productModel = new ProductModel();
        $this->glassTypesModel = new GlassTypesModel();
        $this->touchTypesModel = new TouchTypesModel();
        $this->resolutionModel = new ResolutionModel();
        $this->productMasterModel = new ProductMasterModel();

        $this->quotationModel = new QuotationModel();
        $this->quotationProductsModel = new QuotationProductsModel();

        $this->contractModel = new ContractModel();
        $this->contractProductsModel = new ContractProductsModel();
        $this->contractDeliveryModel = new ContractDeliveryModel();
        $this->contractPaymentsModel = new ContractPaymentsModel();
  $this->initializePrivileges(); // This calls method from BaseController

    }

    public function index()
    {
        set_title('Customer Report | ' . SITE_NAME);

        $data = [
            'action' => "reports/customer-report",
            'pageTitle' => "Customer Report",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        // Collect search criteria
        $searchFields = $this->request->getGet();
        foreach ($searchFields as $field => $searchValue) {
            if ($field) {
                $data['searchArray'][$field] = $searchValue;
            }
        }

        $data['sizes'] = $this->sizesModel->findAll();
        $data['colors'] = $this->colorsModel->findAll();
        $data['touchTypes'] = $this->touchTypesModel->findAll();
        $data['glassTypes'] = $this->glassTypesModel->findAll();
        $data['resolutions'] = $this->resolutionModel->findAll();

        $data['customers'] = $this->usersModel->getUsersDetails();
        $data['productMaster'] = $this->productMasterModel->getProducts();

        // Pagination logic
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 200;
        $contractCounts = $this->contractModel->contractReport($data['searchArray'], '', '', '1');
        $quotationCounts = $this->quotationModel->quotationReport($data['searchArray'], '', '', '1');
        $totalRecord = $contractCounts + $quotationCounts;
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $this->customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetching results for the current page
        $contractRecords = $this->contractModel->contractReport($data['searchArray'], $startLimit, $Limit);
        $quotationRecords = $this->quotationModel->quotationReport($data['searchArray'], $startLimit, $Limit);

        // Convert the stdClass objects to normal arrays and add transaction_type, with handling for empty arrays
        $contractRecordsArray = !empty($contractRecords) ? array_map(function ($contract) {
            $contractArray = (array) $contract;
            $contractArray['transaction_type'] = 'contract';
            return $contractArray;
        }, $contractRecords) : [];

        $quotationRecordsArray = !empty($quotationRecords) ? array_map(function ($quotation) {
            $quotationArray = (array) $quotation;
            $quotationArray['transaction_type'] = 'quotation';
            return $quotationArray;
        }, $quotationRecords) : [];

        // Merge the two arrays into results if either is not empty
        $data['results'] = array_merge($contractRecordsArray, $quotationRecordsArray);
        return view('admin/reports/customer_report', $data);
    }

    public function rentProductReport()
    {
        set_title('Product Report | ' . SITE_NAME);

        $data = [
            'action' => "reports/product-reports",
            'pageTitle' => "Product Report",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        // Collect search criteria
        $searchFields = $this->request->getGet();
        foreach ($searchFields as $field => $searchValue) {
            if ($field) {
                $data['searchArray'][$field] = $searchValue;
            }
        }

        $data['sizes'] = $this->sizesModel->findAll();
        $data['colors'] = $this->colorsModel->findAll();
        $data['touchTypes'] = $this->touchTypesModel->findAll();
        $data['glassTypes'] = $this->glassTypesModel->findAll();
        $data['resolutions'] = $this->resolutionModel->findAll();

        $data['customers'] = $this->usersModel->getUsersDetails();
        $data['productMaster'] = $this->productMasterModel->getProducts();

        // Pagination logic
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 200;
        $contractCounts = $this->contractModel->rentContractReport($data['searchArray'], '', '', '1');
        $quotationCounts = $this->quotationModel->rentQuotationReport($data['searchArray'], '', '', '1');
        $totalRecord = $contractCounts + $quotationCounts;
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $this->customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetching results for the current page
        $contractRecords = $this->contractModel->rentContractReport($data['searchArray'], $startLimit, $Limit);
        $quotationRecords = $this->quotationModel->rentQuotationReport($data['searchArray'], $startLimit, $Limit);

        // Convert the stdClass objects to normal arrays and add transaction_type, with handling for empty arrays
        $contractRecordsArray = !empty($contractRecords) ? array_map(function ($contract) {
            $contractArray = (array) $contract;
            $contractArray['transaction_type'] = 'contract';
            return $contractArray;
        }, $contractRecords) : [];

        $quotationRecordsArray = !empty($quotationRecords) ? array_map(function ($quotation) {
            $quotationArray = (array) $quotation;
            $quotationArray['transaction_type'] = 'quotation';
            return $quotationArray;
        }, $quotationRecords) : [];

        // Merge the two arrays into results if either is not empty
        $data['results'] = array_merge($contractRecordsArray, $quotationRecordsArray);

        return view('admin/reports/rent_product_report', $data);
    }

    public function saleProductReport()
    {
        set_title('Product Report | ' . SITE_NAME);

        $data = [
            'action' => "reports/product-reports",
            'pageTitle' => "Product Report",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        // Collect search criteria
        $searchFields = $this->request->getGet();
        foreach ($searchFields as $field => $searchValue) {
            if ($field) {
                $data['searchArray'][$field] = $searchValue;
            }
        }

        $data['sizes'] = $this->sizesModel->findAll();
        $data['colors'] = $this->colorsModel->findAll();
        $data['touchTypes'] = $this->touchTypesModel->findAll();
        $data['glassTypes'] = $this->glassTypesModel->findAll();
        $data['resolutions'] = $this->resolutionModel->findAll();

        $data['customers'] = $this->usersModel->getUsersDetails();
        $data['productMaster'] = $this->productMasterModel->getProducts();

        // Pagination logic
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 200;
        $contractCounts = $this->contractModel->saleContractReport($data['searchArray'], '', '', '1');
        $quotationCounts = $this->quotationModel->saleQuotationReport($data['searchArray'], '', '', '1');
        $totalRecord = $contractCounts + $quotationCounts;
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $this->customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetching results for the current page
        $contractRecords = $this->contractModel->saleContractReport($data['searchArray'], $startLimit, $Limit);
        $quotationRecords = $this->quotationModel->saleQuotationReport($data['searchArray'], $startLimit, $Limit);

        // Convert the stdClass objects to normal arrays and add transaction_type, with handling for empty arrays
        $contractRecordsArray = !empty($contractRecords) ? array_map(function ($contract) {
            $contractArray = (array) $contract;
            $contractArray['transaction_type'] = 'contract';
            return $contractArray;
        }, $contractRecords) : [];

        $quotationRecordsArray = !empty($quotationRecords) ? array_map(function ($quotation) {
            $quotationArray = (array) $quotation;
            $quotationArray['transaction_type'] = 'quotation';
            return $quotationArray;
        }, $quotationRecords) : [];

        // Merge the two arrays into results if either is not empty
        $data['results'] = array_merge($contractRecordsArray, $quotationRecordsArray);

        return view('admin/reports/sale_product_report', $data);
    }
}
