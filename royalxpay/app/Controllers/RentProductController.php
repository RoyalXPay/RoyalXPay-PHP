<?php

namespace App\Controllers;

use App\Models\SizesModel;
use App\Models\ColorsModel;
use App\Models\ProductModel;
use App\Libraries\Pagination;
use App\Models\ResolutionModel;
use App\Models\GlassTypesModel;
use App\Models\TouchTypesModel;
use App\Models\ProductMasterModel;
use App\Controllers\BaseController;

class RentProductController extends BaseController
{
    protected $db;
    protected $pagination;
    protected $sizesModel;
    protected $colorsModel;
    protected $productModel;
    protected $glassTypesModel;
    protected $touchTypesModel;
    protected $resolutionModel;
    protected $productMasterModel;

    public function __construct()
    {
        // Initialize models
        $this->db = \Config\Database::connect();
        $this->pagination = new Pagination();
        $this->sizesModel = new SizesModel();
        $this->colorsModel = new ColorsModel();
        $this->productModel = new ProductModel();
        $this->glassTypesModel = new GlassTypesModel();
        $this->touchTypesModel = new TouchTypesModel();
        $this->resolutionModel = new ResolutionModel();
        $this->productMasterModel = new ProductMasterModel();
    }

    public function index()
    {
        set_title('Product List | ' . SITE_NAME);

        $data = [
            'action' => "rent-products",
            'pageTitle' => "Product List",
            'startLimit' => 0,
            'reverse' => 0,
            'pagination' => '',
            'results' => [],
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

        // Pagination logic
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 50;  // You can change the limit as needed
        $startLimit = ($page - 1) * $Limit;
        $totalRecord = $this->productModel->getRentProductsDetails($data['searchArray'], '', '', '1');
        $data['startLimit'] = $startLimit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['pagination'] = $this->pagination->getPaginate($totalRecord, $page, $Limit);
        $data['results'] = $this->productModel->getRentProductsDetails($data['searchArray'], $startLimit, $Limit);

        return view('admin/rent/products/index', $data);
    }

    public function create()
    {
        set_title('Create Product | ' . SITE_NAME);
        $data = [];
        return view('admin/rent/products/create', $data);
    }

    public function edit($productId)
    {

        set_title('Edit Product | ' . SITE_NAME);

        $data = [];
        $productDetails = $this->productModel->find($productId);
        $data['masterProducts'] = $this->productMasterModel->getProducts();
        $data['purchaseProducts'] = $this->productMasterModel->getPurchaseProductsDetails($productDetails["product_master_id"]);

        $data["productDetails"] = $productDetails;
        return view('admin/rent/products/create', $data);
    }

    public function store()
    {
        // Get the product ID from POST (for update or create)
        $rentProductId = $this->request->getPost('product_id');

        // Validation
        $session = session();
        $validation = \Config\Services::validation();
        if (!$this->validate([
            'product_master_id' => 'required',
            'quantity' => 'required|integer',
            'price_per_day' => 'required|decimal',
        ])) {

            $session->setFlashdata('error', $validation->getErrors());
            if ($rentProductId) {
                return redirect()->to(site_url('rent-products/edit/' . $rentProductId));
            } else {
                return redirect()->to(site_url('rent-products/create'));
            }
        }

        // Get the input values
        $productSource = $this->request->getPost('product_source');
        $quantity = $this->request->getPost('quantity');
        $pricePerDay = $this->request->getPost('price_per_day');
        $productSourceId = $this->request->getPost('product_master_id');

        // Set start date as the current date
        $startDate = date('Y-m-d');
        // Set end date as 365 days after the current date
        $endDate = date('Y-m-d', strtotime('+365 days'));

        $data = [
            'product_master_id' => $productSourceId,
            'product_type' => "rent",
            'product_source' => $productSource,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'quantity' => $quantity,
            'available_quantity' => $quantity,
            'price_per_day' => $pricePerDay,
        ];

        // Check if the product exists
        $existingProduct = $this->productModel->find($rentProductId);

        if ($existingProduct) {
            // If product exists, update it
            if ($this->productModel->update($rentProductId, $data)) {
                $session->setFlashdata('success', 'Product updated successfully');
                return redirect()->to(site_url('rent-products/'));
            } else {
                $session->setFlashdata('error', 'Failed to update product. Please try again.');
                return redirect()->to(site_url('rent-products/edit/' . $rentProductId));
            }
        } else {
            // If product doesn't exist, create it
            if ($this->productModel->insert($data)) {

                if ($productSource === "purchase_products") {
                    $updateDetails = ["is_used" => 1];
                    if (!$this->productMasterModel->update($productSourceId, $updateDetails)) {
                        $session->setFlashdata('error', 'Failed to mark the product as used. Please try again.');
                        return redirect()->to(site_url('rent-products/'));
                    }
                }

                $session->setFlashdata('success', 'Product added successfully');
                return redirect()->to(site_url('rent-products/'));
            } else {
                $session->setFlashdata('error', 'Failed to add product. Please try again.');
                return redirect()->to(site_url('rent-products/create'));
            }
        }
    }

    public function showDetails($productId)
    {
        $data = array();
        $data['pageTitle'] = "Product Details";
        $data['record'] = $this->productModel
            ->getProductsWithDetails("rent", $productId);

        return view('admin/rent/products/preview', $data);
    }

    public function delete($productId)
    {
        // Validate that $productId is numeric and not empty
        if (!is_numeric($productId) || empty($productId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid product ID.',
            ]);
        }

        try {
            // Fetch the product details
            $product = $this->productModel->find($productId);

            // If no record is found, return an error response
            if (!$product) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Product not found.',
                ]);
            }

            // If product_source is 'purchase_products', update is_used to 0
            if ($product['product_source'] === "purchase_products") {
                $updateData = ['is_used' => 0];
                if (!$this->productMasterModel->update($product['product_master_id'], $updateData)) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Failed to update product source.',
                    ]);
                }
            }

            // Proceed with deletion
            $this->productModel->delete($productId);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Product deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again.',
            ]);
        }
    }
}
