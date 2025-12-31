<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\UsersModel;
use App\Models\SizesModel;
use App\Models\ColorsModel;
use App\Models\ProductModel;
use App\Libraries\Pagination;
use App\Models\ResolutionModel;
use App\Models\GlassTypesModel;
use App\Models\TouchTypesModel;
use App\Controllers\BaseController;

class HomeController extends BaseController
{

    protected $db;
    protected $cartModel;
    protected $pagination;
    protected $sizesModel;
    protected $colorsModel;
    protected $productModel;
    protected $glassTypesModel;
    protected $touchTypesModel;
    protected $resolutionModel;

    public function __construct()
    {
        // Initialize models
        $this->db = \Config\Database::connect();
        $this->cartModel = new CartModel();
        $this->pagination = new Pagination();
        $this->sizesModel = new SizesModel();
        $this->colorsModel = new ColorsModel();
        $this->productModel = new ProductModel();
        $this->glassTypesModel = new GlassTypesModel();
        $this->touchTypesModel = new TouchTypesModel();
        $this->resolutionModel = new ResolutionModel();
    }

    public function index()
    {
        set_title('Dashboard | ' . SITE_NAME);

        $data = [];
        return view('frontend/index', $data);
    }

    public function showRentProducts()
    {
        set_title('Rent Products | ' . SITE_NAME);

        // Retrieve the start and end dates from the GET request
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        // Initialize data array for the view
        $data = [];
        $data['rentProducts'] = [];

        // Validate the dates to ensure they are provided
        if (!$startDate || !$endDate) {
            session()->setFlashdata('error', 'Please select both start and end dates.');
            return view('frontend/rent_products', $data);
        }

        // Ensure that start date is not greater than end date
        $startDateObj = new \DateTime($startDate);
        $endDateObj = new \DateTime($endDate);
        if ($startDateObj > $endDateObj) {
            session()->setFlashdata('error', 'Start date cannot be greater than end date.');
            return view('frontend/rent_products', $data);
        }

        // Begin a database transaction to ensure atomicity of the updates
        $this->db->transStart();

        try {
            // Step 1: Update expired contracts
            $currentDate = date('Y-m-d');

            // Find contracts that are expired (end_date < currentDate) and are still active
            $expiredContracts = $this->db->table('contracts')
                ->select('contract_id')
                ->where('end_date <', $currentDate)
                ->where('status', 'active')
                ->get()
                ->getResult();

            // Extract the contract_ids from the expired contracts
            $contractIds = array_map(function ($contract) {
                return $contract->contract_id;
            }, $expiredContracts);

            // Check if there are expired contracts to process
            if (count($contractIds) > 0) {
                // Step 1.1: Get the associated products of the expired contracts
                $contractProducts = $this->db->table('contract_products')
                    ->select('product_id, quantity, contract_id')
                    ->whereIn('contract_id', $contractIds)
                    ->get()
                    ->getResult();

                // Step 1.2: Update available quantity in rent_products table for each expired contract product
                foreach ($contractProducts as $contractProduct) {
                    $this->db->table('products')
                        ->where('product_id', $contractProduct->product_id)
                        ->set('available_quantity', 'available_quantity + ' . $contractProduct->quantity, false)
                        ->update();
                }

                // Step 1.3: Update contracts to "completed" status after processing product returns
                $this->db->table('contracts')
                    ->whereIn('contract_id', $contractIds)
                    ->update(['status' => 'completed']);
            }

            // Step 2: Fetch available rent products with additional details
            $products = $this->db->table('products')
                ->select('products.*, product_master.product_name, product_master.product_type as master_product_type, product_master.product_images, product_master.serial_number, sizes.size_in_inches, colors.name as color_name, touch_types.name as touch_name, glass_types.name as glass_name')
                ->join('product_master', 'product_master.product_master_id = products.product_master_id', 'left')
                ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
                ->join('colors', 'colors.color_id = product_master.color_id', 'left')
                ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
                ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
                ->where('products.product_type', 'rent')
                ->get()
                ->getResult();

            // Fetch active rentals for the given date range
            $activeRentals = $this->db->table('contract_products')
                ->select('contract_products.product_id, SUM(contract_products.quantity) as total_rented')
                ->join('contracts', 'contract_products.contract_id = contracts.contract_id', 'left')
                ->where('contracts.status', 'active')
                ->groupStart()
                ->where('contracts.start_date <=', $endDate)
                ->where('contracts.end_date >=', $startDate)
                ->groupEnd()
                ->groupBy('contract_products.product_id')
                ->get()
                ->getResult();


            // Map rented quantities by product_id for fast lookup
            $rentedQuantities = [];
            foreach ($activeRentals as $rental) {
                $rentedQuantities[$rental->product_id] = $rental->total_rented;
            }

            // Adjust available quantity for each product
            foreach ($products as $product) {
                $availableQuantity = $product->quantity;

                // Subtract rented quantity if there are active contracts
                if (isset($rentedQuantities[$product->product_id])) {
                    $availableQuantity -= $rentedQuantities[$product->product_id];
                }

                // Ensure available quantity is not negative
                $product->available_quantity = max($availableQuantity, 0);
            }

            // Commit the database transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {


                log_message('error', 'Transaction failed: ' . print_r($this->db->error(), true));
                session()->setFlashdata('error', 'Failed to update rent products. Please try again.');
                $this->db->transRollback(); // Explicit rollback
                return view('frontend/rent_products', $data);
            }

            $this->db->transComplete();
        } catch (\Exception $e) {
            log_message('error', 'Transaction exception: ' . $e->getMessage());
            $this->db->transRollback();
            session()->setFlashdata('error', 'System error occurred');
            return view('frontend/rent_products', $data);
        }

        // Pass the products to the view
        $data['rentProducts'] = $products;
        session()->setFlashdata('success', 'Rent products loaded successfully.');

        return view('frontend/rent_products', $data);
    }

    public function showSaleProducts()
    {
        set_title('Sale Products | ' . SITE_NAME);
        $data = [
            'action' => "products/sale",
            'pageTitle' => "Sale Products",
            'saleProducts' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'searchArray' => [],
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

        // Pagination logic
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 9;  // You can change the limit as needed
        $totalRecord = $this->productModel->getSaleProductsDetails($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['startLimit'] = $startLimit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['pagination'] = $this->pagination->getPaginate($totalRecord, $page, $Limit);

        // Fetching sale saleProducts for the current page
        $data['saleProducts'] = $this->productModel->getSaleProductsDetails($data['searchArray'], $startLimit, $Limit);

        return view('frontend/sale_products', $data);
    }

    public function showProductDetail($productId)
    {
        set_title('Product Details | ' . SITE_NAME);

        $data = [
            'action' => "products/rent",
            'pageTitle' => "Product List",
            'startLimit' => 0,
            'reverse' => 0,
            'pagination' => '',
            'rentProducts' => [],
            'searchArray' => []
        ];

        $data['pageTitle'] = "Product Details";
        $data['productDetails'] = $this->productModel
            ->getProductsWithDetails("rent", $productId);

        // Collect search criteria
        $searchFields = $this->request->getGet();
        foreach ($searchFields as $field => $searchValue) {

            $data['searchArray'][$field] = $searchValue;
        }

        $data['sizes'] = $this->sizesModel->findAll();
        $data['colors'] = $this->colorsModel->findAll();
        $data['touchTypes'] = $this->touchTypesModel->findAll();
        $data['glassTypes'] = $this->glassTypesModel->findAll();
        $data['resolutions'] = $this->resolutionModel->findAll();

        // Pagination logic
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 9;  // You can change the limit as needed
        $startLimit = ($page - 1) * $Limit;
        $totalRecord = $this->productModel->getRentProductsDetails($data['searchArray'], '', '', '1');
        $data['startLimit'] = $startLimit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['pagination'] = $this->pagination->getPaginate($totalRecord, $page, $Limit);
        $data['rentProducts'] = $this->productModel->getRentProductsDetails($data['searchArray'], $startLimit, $Limit);

        return view('frontend/product_detail', $data);
    }

    public function saleProductDetails($productId)
    {

        $data = [
            'action' => "sale/products/details/" . $productId,
            'pageTitle' => "Sale Product Details",
            'saleProducts' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'searchArray' => [],
        ];

        $productModel = new ProductModel();
        $data['productDetails'] = $productModel
            ->getProductsWithDetails("sale", $productId);

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

        // Pagination logic
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 9;  // You can change the limit as needed
        $totalRecord = $this->productModel->getSaleProductsDetails($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['startLimit'] = $startLimit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['pagination'] = $this->pagination->getPaginate($totalRecord, $page, $Limit);

        // Fetching sale saleProducts for the current page
        $data['saleProducts'] = $this->productModel->getSaleProductsDetails($data['searchArray'], $startLimit, $Limit);

        return view('frontend/sale_product_details', $data);
    }

    public function wishlist()
    {
        set_title('Wishlist | ' . SITE_NAME);

        $data = [];
        // Load the view
        return view('frontend/wishlist', $data);
    }
}
