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

class SaleProductController extends BaseController
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
        set_title('Sale Product List | ' . SITE_NAME);

        $data = [
            'action' => "sale-products",
            'pageTitle' => "Sale Product List",
            'results' => [],
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
        $Limit = 50;  // You can change the limit as needed
        $totalRecord = $this->productModel->getSaleProductsDetails($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['startLimit'] = $startLimit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['pagination'] = $this->pagination->getPaginate($totalRecord, $page, $Limit);

        // Fetching sale results for the current page
        $data['results'] = $this->productModel->getSaleProductsDetails($data['searchArray'], $startLimit, $Limit);

        return view('admin/sale/products/index', $data);
    }

    public function create()
    {

        set_title('Create Product | ' . SITE_NAME);
        $data = [];
        return view('admin/sale/products/create', $data);
    }

    public function edit($productId)
    {

        set_title('Edit Product | ' . SITE_NAME);
        $data = [];
        $productDetails = $this->productModel->find($productId);
        $data['masterProducts'] = $this->productMasterModel->getProducts();
        $data['purchaseProducts'] = $this->productMasterModel->getPurchaseProductsDetails($productDetails["product_master_id"]);

        $data['productDetails'] = $productDetails;

        return view('admin/sale/products/create', $data);
    }

    public function store()
    {
        $session = session();
        $validation = \Config\Services::validation();
        $productId = $this->request->getPost('product_id');

        // Validate input
        if (!$this->validate([
            'product_master_id' => 'required',
            'quantity' => 'required|integer',
            'product_price' => 'required|decimal',
        ])) {

            $session->setFlashdata('error', $validation->getErrors());
            if ($productId) {
                return redirect()->to(site_url('sale-products/edit/' . $productId));
            } else {
                return redirect()->to(site_url('sale-products/create'));
            }
        }

        $productSource = $this->request->getPost('product_source');
        $price = $this->request->getPost('product_price');
        $discount = (float) ($this->request->getPost('discount') ?? 0);
        $quantity = $this->request->getPost('quantity');
        $productSourceId = $this->request->getPost('product_master_id');

        // Calculate the total price before discount
        $totalPrice = $price * $quantity;

        // Calculate the final price after discount directly in this method
        $discountAmount = $totalPrice * ($discount / 100);
        $finalPrice = $totalPrice - $discountAmount;

        // Round final price to 3 decimal places
        $finalPrice = round($finalPrice, 3);

        // Prepare the data to be saved or updated
        $data = [
            'product_master_id' => $productSourceId,
            'product_type' => "sale",
            'product_source' => $productSource,
            'quantity' => $quantity,
            'available_quantity' => $quantity,
            'product_price' => $price,
            'discount_percentage' => $discount,
            'final_product_price' => $finalPrice,
        ];

        // Save or update based on the existence of productId
        $productId = $this->request->getPost('product_id');

        if ($productId) {
            // Update existing product
            $existingProduct = $this->productModel->find($productId);
            if (!$existingProduct) {
                $session->setFlashdata('error', 'Product not found');
                return redirect()->to(site_url('sale-products/'));
            }

            if ($this->productModel->update($productId, $data)) {
                $session->setFlashdata('success', 'Product updated successfully');
                return redirect()->to(site_url('sale-products/'));
            } else {
                $session->setFlashdata('error', 'Failed to update product. Please try again.');
                return redirect()->to(site_url('sale-products/edit/' . $productId));
            }
        } else {
            // Create new product
            if ($this->productModel->insert($data)) {

                if ($productSource === "purchase_products") {
                    $updateDetails = ["is_used" => 1];
                    if (!$this->productMasterModel->update($productSourceId, $updateDetails)) {
                        $session->setFlashdata('error', 'Failed to mark the product as used. Please try again.');
                        return redirect()->to(site_url('rent-products/'));
                    }
                }

                $session->setFlashdata('success', 'Product added successfully');
                return redirect()->to(site_url('sale-products/'));
            } else {
                $session->setFlashdata('error', 'Failed to add product. Please try again.');
                return redirect()->to(site_url('sale-products/create'));
            }
        }
    }

    public function showDetails($productId)
    {

        $data = array();
        $data['pageTitle'] = "Product Details";
        $productModel = new ProductModel();
        $data['record'] = $productModel
            ->getProductsWithDetails("sale", $productId);

        return view('admin/sale/products/preview', $data);
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
