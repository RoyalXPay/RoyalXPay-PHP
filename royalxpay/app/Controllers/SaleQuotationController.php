<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\BanksModel;
use App\Models\ProductModel;
use App\Libraries\Pagination;
use App\Models\QuotationModel;
use App\Models\DeletedQuotation;
use App\Models\DeliveryTimeModel;
use App\Models\ProductMasterModel;
use App\Models\QuotationProductsModel;
use App\Models\TermsAndConditionsModel;
use App\Controllers\BaseController;

class SaleQuotationController extends BaseController
{
    public function index()
    {
        set_title('Sale Quotation List | ' . SITE_NAME);

        $data = [
            'action' => "sale-quotations",
            'pageTitle' => "Sale Quotation List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        // Initialize models and pagination
        $usersModel = new UsersModel();
        $productModel = new ProductModel();
        $customPagination = new Pagination();
        $quotationModel = new QuotationModel();
        $productMasterModel = new ProductMasterModel();
        $quotationProductsModel = new QuotationProductsModel();

        // Collect search criteria
        $searchFields = ['txtsearch'];
        foreach ($searchFields as $field) {
            $value = $this->request->getGet($field);
            if ($value) {
                $data[$field] = $value;
                $data['searchArray'][$field] = $value;
            }
        }

        $data['customers'] = $usersModel->getUsersDetails();
        $data['products'] = $productMasterModel->getProducts();
        $data['saleProducts'] = $productModel->getSaleProductsDetails();
        $data['quotationsProducts'] = $quotationProductsModel->getQuotationProductsDetails();

        // Pagination logic
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 50;
        $totalRecord = $quotationModel->getSaleQuotations($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetching results for the cursale page
        $data['results'] = $quotationModel->getSaleQuotations($data['searchArray'], $startLimit, $Limit);

        return view('admin/sale/quotation/index', $data);
    }

    public function getProductsRows()
    {
        // Get the requested productSource from GET data
        $productSource = $this->request->getGet('product_source');

        // Validate the productSource to ensure it's either 'products' or 'sale_products'
        if (!in_array($productSource, ['products', 'sale_products'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid product specified.'
            ]);
        }

        // Initialize models and pagination
        $data = [];
        $productModel = new ProductModel();
        $productMasterModel = new ProductMasterModel();

        try {
            if ($productSource == 'products') {
                // Fetch product details
                $data['product_source'] = "products";
                $data['products'] = $productMasterModel->getProducts();
                if (empty($data['products'])) {
                    $data['message'] = 'No products found.';
                }
            } else if ($productSource === 'sale_products') {
                // Fetch sale product details
                $data['product_source'] = "sale_products";
                $data['saleProducts'] = $productModel->getSaleProductsDetails();
                if (empty($data['saleProducts'])) {
                    $data['message'] = 'No sale products found.';
                }
            }

            // If we have any data, return it as JSON
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function create()
    {
        $data = [];
        // Initialize models and pagination
        $usersModel = new UsersModel();
        $productModel = new ProductModel();
        $termsModel = new TermsAndConditionsModel();
        $deliveryTimeModel = new DeliveryTimeModel();
        $productMasterModel = new ProductMasterModel();

        $data['customers'] = $usersModel->getUsersDetails();
        $data['deliveryTimes'] = $deliveryTimeModel->getDeliveryTimes();
        $data['termsAndConditions'] = $termsModel->getTermsAndConditions();

        $masterProducts = $productMasterModel->getProducts();
        $saleProducts = $productModel->getSaleProductsDetails();

        $combinedProducts = [];
        foreach ($masterProducts as $product) {
            $product->product_source = 'master';
            $product->unique_id = 'master_' . $product->product_master_id;
            $combinedProducts[] = $product;
        }
        foreach ($saleProducts as $product) {
            unset($product->product_master_id);
            $product->product_source = 'sale';
            $product->unique_id = 'sale_' . $product->product_id;
            $combinedProducts[] = $product;
        }

        $data['products'] = $combinedProducts;

        return view('admin/sale/quotation/create', $data);
    }

    public function edit($quotationId)
    {
        $data = [];
        $usersModel = new UsersModel();
        $productModel = new ProductModel();
        $termsModel = new TermsAndConditionsModel();
        $deliveryTimeModel = new DeliveryTimeModel();
        $productMasterModel = new ProductMasterModel();

        $quotationModel = new QuotationModel();
        $quotationProductsModel = new QuotationProductsModel();

        $data['customers'] = $usersModel->getUsersDetails();
        $data['deliveryTimes'] = $deliveryTimeModel->getDeliveryTimes();
        $data['termsAndConditions'] = $termsModel->getTermsAndConditions();

        $masterProducts = $productMasterModel->getProducts();
        $saleProducts = $productModel->getSaleProductsDetails();

        $combinedProducts = [];
        foreach ($masterProducts as $product) {
            $product->product_source = 'master';
            $product->unique_id = 'master_' . $product->product_master_id;
            $combinedProducts[] = $product;
        }
        foreach ($saleProducts as $product) {
            unset($product->product_master_id);
            $product->product_source = 'sale';
            $product->unique_id = 'sale_' . $product->product_id;
            $combinedProducts[] = $product;
        }

        $quotationDetails = $quotationModel->getQuotationWithDetails($quotationId);
        $quotationProductDetails = $quotationProductsModel->getSaleQuotationProductsDetails($quotationId);

        $quotationProducts = [];
        foreach ($quotationProductDetails as $key => $productDetails) {

            if (!empty($productDetails['product_master_id'])) {
                unset($productDetails['product_id']);
                $productDetails["product_source"] = "master";
                $quotationProducts['master_' . $productDetails['product_master_id']] = $productDetails;
            } else {
                unset($productDetails['product_master_id']);
                $productDetails["product_source"] = "sale";
                $quotationProducts['sale_' . $productDetails['product_id']] = $productDetails;
            }
        }

        $data['products'] = $combinedProducts;
        $data["quotationDetails"] = $quotationDetails;
        $data["quotationDetails"]["quotationProducts"] = $quotationProducts;

        return view('admin/sale/quotation/create', $data);
    }

    public function saveQuotation()
    {
        $session = session();
        $quotationId = $this->request->getPost('quotation_id');
        $quotationCode = $this->request->getPost('quotation_code');

        if (!$this->validate([
            'user_id' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'User customer is required.'
                ],
            ],
            'productIds' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Please select at least one product.'
                ],
            ],
            'quantity' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Please specify the quantity.'
                ],
            ],
            'price_per_unit' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Price per unit is required.'
                ],
            ],
            'final_payable_price' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Final payable price is required.'
                ],
            ],
        ])) {
            $session->setFlashdata('error', $this->validator->getErrors());
            if ($quotationId) {
                return redirect()->to(site_url('sale-quotations/edit/' . $quotationId));
            } else {
                return redirect()->to(site_url('sale-quotations/create'));
            }
        }

        $productModel = new ProductModel();
        $quotationModel = new QuotationModel();
        $productMasterModel = new ProductMasterModel();
        $quotationProductsModel = new QuotationProductsModel();

        // Get only the checked products
        $productIds = array_keys($this->request->getPost('productIds'));
        $customerId = $this->request->getPost('user_id');
        // Get all POST data
        $postData = $this->request->getPost();

        // Initialize arrays to store product data
        $productsData = [];
        $masterIds = [];
        $saleIds = [];
        foreach ($productIds as $uniqueId) {
            // Extract source and original ID
            list($source, $id) = explode('_', $uniqueId, 2);

            if ($source === 'master') {
                $masterIds[] = $id;
            } else {
                $saleIds[] = $id;
            }

            $productsData[] = [
                'id' => $id,
                'source' => $source,
                'unique_id' => $uniqueId,
                'quantity' => $postData['quantity'][$uniqueId] ?? 1,
                'price' => $postData['price_per_unit'][$uniqueId] ?? 0,
                'discount' => $postData['discount'][$uniqueId] ?? 0
            ];
        }

        $productsAssoc = [];
        if (!empty($masterIds)) {
            $products = $productMasterModel->searchProducts($masterIds);
            foreach ($products as $product) {
                $productsAssoc['master_' . $product->product_master_id] = (array) $product;
            }
        }

        if (!empty($saleIds)) {
            $saleProducts = $productModel->getProductsWithDetails("sale", $saleIds);
            foreach ($saleProducts as $product) {
                $productsAssoc['sale_' . $product->product_id] = (array) $product;
            }
        }

        $termsConditions = $this->request->getPost('terms_conditions');
        $citylightLogo = $this->request->getPost('citylight_logo') ? 1 : 0;
        $citylightAddress = $this->request->getPost('citylight_address') ? 1 : 0;
        $contactDetails = $this->request->getPost('contact_details') ? 1 : 0;
        $citylightEmail = $this->request->getPost('citylight_email') ? 1 : 0;
        $citylightWebsite = $this->request->getPost('citylight_website') ? 1 : 0;
        $bankDetails = $this->request->getPost('bank_details') ? 1 : 0;
        $stamp = $this->request->getPost('stamp') ? 1 : 0;
        $signature = $this->request->getPost('signature') ? 1 : 0;
        $returnPolicies = $this->request->getPost('return_policies') ? 1 : 0;
        $notes = $this->request->getPost('notes');
        $deliveryTime = $this->request->getPost('delivery_time');
        $termsConditions = $this->request->getPost('terms_conditions');

        // Start a transaction
        $db = \Config\Database::connect();
        $db->transStart();

        // Recalculate total price, VAT, and final payable price
        $vatRate = 5;
        $totalPrice = 0;
        $totalDiscount = 0;

        foreach ($productsData as $index => $details) {

            $quantity = $details['quantity'] ?? 0;
            $price = $details['price'] ?? 0;
            $discount = (float)$details['discount'] ?? 0;

            if ($quantity <= 0 || $price <= 0) {
                $db->transRollback();
                $session->setFlashdata('error', "Invalid quantity or price for product" . $productsAssoc[$details['unique_id']]['product_name']);
                if ($quotationId) {
                    return redirect()->to(site_url('sale-quotations/edit/' . $quotationId));
                } else {
                    return redirect()->to(site_url('sale-quotations/create'));
                }
            }

            // Calculate the total amount for this product
            $productTotal = $quantity * $price;

            // Calculate the discount for this product
            $productDiscount = ($productTotal * $discount) / 100;
            $totalPrice += $productTotal;
            $totalDiscount += $productDiscount;

            // Check if the product exists
            if (!isset($productsAssoc[$details['unique_id']])) {
                $db->transRollback();
                $session->setFlashdata('error', "Product with ID " . $productsAssoc[$details['unique_id']]['product_name'] . " not found in source '{$details['source']}'.");
                if ($quotationId) {
                    return redirect()->to(site_url('sale-quotations/edit/' . $quotationId));
                } else {
                    return redirect()->to(site_url('sale-quotations/create'));
                }
            }

            $product = $productsAssoc[$details['unique_id']];

            // if ($details['source'] == 'sale') {

            //     $quotationProduct = $quotationProductsModel
            //         ->where('quotation_id', $quotationId)
            //         ->where('product_id', $details['id'])
            //         ->find();

            //     if (!empty($quotationProduct)) {
            //         $previousQty = $quotationProduct[0]["quantity"];
            //         $product["available_quantity"] = $product["available_quantity"] + $previousQty;
            //     }
            // }

            // Validate stock for 'sale' source
            if ($details['source'] == 'sale' && $product["quantity"] < $quantity) {
                $db->transRollback();
                $session->setFlashdata('error', "Not enough stock for product '{$product['product_name']}'.");
                if ($quotationId) {
                    return redirect()->to(site_url('sale-quotations/edit/' . $quotationId));
                } else {
                    return redirect()->to(site_url('sale-quotations/create'));
                }
            }

            // Deduct stock if necessary
            // if ($details['source'] == 'sale') {
            //     $productModel->update($details['id'], [
            //         'available_quantity' => $product["available_quantity"] - $quantity,
            //     ]);
            // }
        }

        // Calculate VAT and final payable amount
        $vatAmount = ($vatRate / 100) * ($totalPrice - $totalDiscount);
        $finalPayableAmount = $totalPrice - $totalDiscount + $vatAmount;

        $data = [
            'quotation_code'     => $quotationId ? $quotationCode : $quotationModel->generateQuotationCode("sale"),
            'user_id'            => $customerId,
            'quotation_type'     => "sale",
            'total_price'       => $totalPrice,
            'vat'                => $vatAmount,
            'discount'           => $totalDiscount,
            'terms_conditions'   => $termsConditions,
            'final_payable_price' => $finalPayableAmount,
            'citylight_logo'     => $citylightLogo,
            'citylight_address'  => $citylightAddress,
            'contact_details'    => $contactDetails,
            'citylight_email'    => $citylightEmail,
            'citylight_website'  => $citylightWebsite,
            'bank_details'       => $bankDetails,
            'delivery_time'      => $deliveryTime,
            'stamp'              => $stamp,
            'signature'          => $signature,
            'notes'              => $notes,
            'return_policies'    => $returnPolicies,
            'terms_conditions'   => $termsConditions,
            'final_payable_price' => $finalPayableAmount,
        ];

        $updateMode = false;
        if ($quotationId) {
            $updateMode = true;
            if (!$quotationModel->update($quotationId, $data)) {
                $db->transRollback();
                $session->setFlashdata('error', 'Failed to update quotation. Please try again.');
                return redirect()->to(site_url('sale-quotations/edit/' . $quotationId));
            }
        } else {
            // Create new contract
            $data["quotation_date"] = date('Y-m-d H:i:s');

            if (!$quotationModel->save($data)) {
                $db->transRollback();
                $session->setFlashdata('error', 'Failed to create quotation. Please try again.');
                return redirect()->to(site_url('sale-quotations/create'));
            }

            $quotationId = $quotationModel->getInsertID();
        }

        // Delete removed contract products that are not in the submitted productIds
        if (!empty($quotationId)) {
            // Get all existing products for this contract
            $existingProducts = $quotationProductsModel
                ->where('quotation_id', $quotationId)
                ->findAll();

            // Prepare arrays of submitted product IDs by source
            $submittedMasterIds = [];
            $submittedSaleIds = [];

            foreach ($productsData as $product) {
                if ($product['source'] === 'master') {
                    $submittedMasterIds[] = $product['id'];
                } else {
                    $submittedSaleIds[] = $product['id'];
                }
            }

            // Identify products to delete
            $productsToDelete = [];

            foreach ($existingProducts as $existing) {
                $shouldDelete = false;

                if ($existing['product_master_id'] && !in_array($existing['product_master_id'], $submittedMasterIds)) {
                    $shouldDelete = true;
                }
                if ($existing['product_id'] && !in_array($existing['product_id'], $submittedSaleIds)) {
                    $shouldDelete = true;
                }

                if ($shouldDelete) {
                    $productsToDelete[] = $existing['quotation_product_id'];
                }
            }

            // Delete the products that were removed
            if (!empty($productsToDelete)) {
                $quotationProductsModel->whereIn('quotation_product_id', $productsToDelete)->delete();
            }
        }

        // Handle contract products (add or update)
        foreach ($productsData as $index => $details) {

            $quantity = $details['quantity'] ?? 0;
            $price = $details['price'] ?? 0;
            $discount = (float)$details['discount'] ?? 0;

            // Check if the product already exists in the contract_products table
            $existingProduct = $quotationProductsModel->where([
                'quotation_id' => $quotationId,
                'product_master_id'  => $details['source'] == 'sale' ? null : $details['id'],
                'product_id' => $details['source'] === 'sale' ? $details['id'] : null,
            ])->first();

            $quotationProductData = [
                'quotation_id'     => $quotationId,
                'product_master_id' => $details['source'] == 'sale' ? null : $details['id'],
                'product_id' => $details['source'] === 'sale' ? $details['id'] : null,
                'quantity'        => $quantity,
                'price'           => $price,
                'discount'        => $discount,
            ];
            if ($existingProduct) {
                // Update the existing product entry
                $quotationProductsModel->update($existingProduct['quotation_product_id'], $quotationProductData);
            } else {
                // Insert a new product entry
                $quotationProductsModel->insert($quotationProductData);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            $session->setFlashdata('error', "Failed to save quotation. Please try again.");
            if ($updateMode) {
                return redirect()->to(site_url('sale-quotations/edit/' . $quotationId));
            } else {
                return redirect()->to(site_url('sale-quotations'));
            }
        }

        $session->setFlashdata('success', "Quotation saved successfully.");
        return redirect()->to(site_url('sale-quotations'));
    }

    public function showDetails($quotationId)
    {

        $data = array();
        $data['pageTitle'] = "Quotation Details";

        $quotationModel = new QuotationModel();
        $quotationProductsModel = new QuotationProductsModel();

        $data['record'] = $quotationModel->getQuotationWithDetails($quotationId);
        $data["record"]["quotationProducts"] = $quotationProductsModel->getSaleQuotationProductsDetails($quotationId);

        return view('admin/sale/quotation/preview', $data);
    }

    public function delete($id)
    {
        // Validate that $id is numeric and not empty
        if (is_numeric($id) && !empty($id)) {
            try {
                // Check if the record exists
                $quotationModel = new QuotationModel();
                $quotation = $quotationModel->find($id);
                $deletedQuotationModel = new DeletedQuotation();

                // If no record is found, return an error response
                if (!$quotation) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Quotation not found.',
                    ]);
                }

                // Insert deleted contract code into deleted_contracts table.
                $deletedQuotationModel->insert([
                    'quotation_code' => $quotation['quotation_code'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                // Proceed with deletion
                $quotationModel->where('quotation_id', $id)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Quotation deleted successfully.',
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'An unexpected error occurred. Please try again.',
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again.',
            ]);
        }
    }

    public function generateQuotation($quotationId)
    {
        // Initialize the models
        $banksModel = new BanksModel();
        $bankDetails = $banksModel->first();
        $quotationModel = new QuotationModel();
        $quotationProductsModel = new QuotationProductsModel();

        // Fetch quotation and product details
        $quotationDetails = $quotationModel->getQuotationWithDetails($quotationId);
        $quotationProductDetails = $quotationProductsModel->getSaleQuotationProductsDetails($quotationId);

        // Base URL for the images
        $productImagesPath = base_url('uploads/products/');
        $stampImagePath = base_url('assets/images/stamp.png');
        $headerImagePath = base_url('assets/images/quotation_header.png');
        $firstPageFooter = base_url('assets/images/footer_first.jpg');

        // Load mPDF Library with custom margins
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_top' => 25,
            'margin_right' => 2,
            'margin_bottom' => 0,
            'margin_left' => 3
        ]);

        // Header
        $mpdf->SetHTMLHeader('
            <div style="position: absolute; top: 0; left: 0; width: 100%;">
                <img src="' . $headerImagePath . '" alt="Contract Header" width="100%">
            </div>
        ', 'O');

        // Footer
        $mpdf->SetHTMLFooter('
            <div style="position: absolute; bottom: 5px; left: 0; height: 30px; width: 100%; text-align: center;">
                <img src="' . $firstPageFooter . '" style="width:100%;" />
            </div>
        ', 'O');

        // Initialize counters for totals
        $productNumber = 1;
        $totalPriceBeforeDiscount = 0;
        $totalPriceAfterDiscount = 0;
        $totalDiscount = 0;
        $combinedProducts = [];

        foreach ($quotationProductDetails as $product) {
            $key = md5($product['product_name'] . $product['discount'] . $product['quantity'] . $product['price']);
            if (isset($combinedProducts[$key])) {
                $combinedProducts[$key]['quantity'] += $product['quantity'];
            } else {
                $combinedProducts[$key] = $product;
            }
        }

        // Start HTML content for the PDF
        $html = '
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    height: 100%;
                    position: relative;
                }
            </style>
        </head>
        <body>
            <div style="text-align: center; background-color: #8a2be2; color: white; font-size: 18px; font-weight: bold; padding: 5px; margin-top: 20px;">
                SALE
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 70%; padding: 10px; font-weight: bold; background-color: #e8d7eb;">
                        CLIENT NAME
                        <span style="font-weight: normal;">' . $quotationDetails["customer_name"] . '</span>
                    </td>
                    <td style="width: 5px;"></td>
                    <td style="width: 30%; padding: 10px; font-weight: bold; background-color: #f5ebf8;">
                        CONTRACT NO
                        <span style="font-weight: normal; color: #512b58;">' . $quotationDetails["quotation_code"] . '</span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 70%; padding: 10px; font-weight: bold; background-color: #e8d7eb;">
                        CLIENT NUMBER
                        <span style="font-weight: normal; color: #512b58;">' . $quotationDetails["phone"] . '</span>
                    </td>
                    <td style="width: 5px;"></td>
                    <td style="width: 30%; padding: 10px; font-weight: bold; background-color: #f5ebf8;">
                        CONTRACT DATE 
                        <span style="font-weight: normal; color: #512b58;">' . date('d/m/Y', strtotime($quotationDetails["quotation_date"])) . '</span>
                    </td>
                </tr>
            </table>';

        $html .= '
            <div style="text-align: center; background-color: #8a2be2; color: white; font-size: 18px; font-weight: bold; padding: 5px; margin-top: 20px;">
                DETAILS
            </div>';

        $html .= '
            <table style="width: 100%; border-collapse: collapse; text-align: center; font-size: 14px; margin-top: 10px;">
                <thead>
                    <tr style="background: linear-gradient(to right, #d88aff, #e5b9f2); color: black; border: 3px solid white;">
                        <th style="padding: 10px; border: 3px solid white; width: 6%; font-weight: normal;">NO</th>
                        <th style="padding: 10px; border: 3px solid white; width: 40%; font-weight: normal;">ITEM DESCRIPTION</th>
                        <th style="padding: 10px; border: 3px solid white; width: 10%; font-weight: normal;">QTY</th>
                        <th style="padding: 10px; border: 3px solid white; width: 15%; font-weight: normal;">UNIT PRICE</th>
                        <th style="padding: 10px; border: 3px solid white; width: 15%; font-weight: normal;">DISCOUNT</th>
                        <th style="padding: 10px; border: 3px solid white; width: 14%; font-weight: normal;">PRICE</th>
                    </tr>
                </thead>
                <tbody>';

        // Iterate over selected products
        $totalStringLength = "";
        foreach ($combinedProducts as $product) {
            // Calculate the total price before discount
            $productTotalBeforeDiscount = $product['price'] * $product['quantity'];
            $totalStringLength .= strlen($product['product_name']) + strlen($product['description']);
            // Calculate the discount
            $productDiscount = ($productTotalBeforeDiscount * $product['discount']) / 100;
            $productTotalAfterDiscount = $productTotalBeforeDiscount - $productDiscount;
            $priceAfterDiscountPrice = $product['price'] - ($product['price'] * $product['discount']) / 100;

            // Accumulate totals
            $totalPriceBeforeDiscount += $productTotalBeforeDiscount;
            $totalDiscount += $productDiscount;
            $totalPriceAfterDiscount += $productTotalAfterDiscount;

            // Build table row for product details
            $html .= '
                <tr style="background: linear-gradient(to right, #f8e6ff, #e5b9f2); color: black; font-weight: bold; border: 3px solid white;">
                    <td style="padding: 10px; border: 3px solid white;">' . $productNumber++ . '</td>
                    <td style="padding: 10px; border: 3px solid white; text-align: left;">
                        <strong style="font-size: 16px;">' . htmlspecialchars($product['product_name']) . '</strong><br>
                        <span style="font-size: 12px; font-weight: normal;">' . nl2br(htmlspecialchars($product['description'])) . '</span>
                    </td>
                    <td style="padding: 10px; border: 3px solid white; font-size: 16px;">' . number_format($product['quantity'], 0) . '</td>
                    <td style="padding: 10px; border: 3px solid white; font-size: 16px;">' . number_format($product['price'], 0) . '</td>
                    <td style="padding: 10px; border: 3px solid white; font-size: 16px;">' . number_format($productDiscount, 2) . '</td>
                    <td style="padding: 10px; border: 3px solid white; font-size: 16px;">' . number_format($productTotalAfterDiscount, 2) . '</td>
                </tr>';
        }

        // Calculate VAT (5%)
        $vat = $totalPriceAfterDiscount * 0.05;

        // Calculate final payable amount (total after discount + VAT)
        $finalPayableAmount = $totalPriceAfterDiscount + $vat;

        $html .= '</tbody></table>';

        $html .= '
            <div style="background-color: #8a2be2; height: 10px; padding: 5px; margin-top: 5px;">
            </div>';

        if ($totalStringLength >= 600) {
            $html .= '<pagebreak>';
        }
        // Terms and conditions
        $html .= '
            <div style="margin-top: 20px;"><br><br>
                <!-- Payment Terms Section -->
                <div style="margin-top: 10px; font-size: 14px;">
                    <div style="text-align: center;">
                        <strong style="font-size: 18px;">PAYMENT TERMS</strong>
                    </div><br><br>
                    <strong style="font-size: 12px;">Payment of the full amount in advance with insurance must be made by bank <br> transfer to account number</strong>
                </div>';

        // Company Account Details
        $html .= '
            <div style="margin-top: 8px; font-size: 14px;">
                <p><strong>Company Account:</strong></p>
                <p>Vatin: <strong>OM1100285967</strong></p>
                <p>Bank Name: <strong>' . $bankDetails["bank_name"] . '</strong></p>
                <p>Account Name: <strong>' . $bankDetails["account_holder"] . '</strong></p>
                <p>Swift Code: <strong>' . $bankDetails["swift_code"] . '</strong></p>
                <p>Account Number: <strong>' . $bankDetails["account_number"] . '</strong></p>
                <p>With a receipt sent to the number <strong>71716060</strong> along with the contract number.</p>
            </div>';

        // Price Summary Section
        $html .= '
            <div style="position: relative; float: right; width: 35%; margin-top: -290px; text-align: right;">
                <table style="width: 100%; background: linear-gradient(to right, #8a2be2, #c07ff8); color: white; font-size: 14px; padding: 10px; border-radius: 8px;">
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid white;">TOTAL PRICE BEFORE DISCOUNT :</td>
                        <td style="padding: 8px; border-bottom: 1px solid white;">' . number_format($totalPriceBeforeDiscount, 2) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid white;">TOTAL PRICE AFTER DISCOUNT :</td>
                        <td style="padding: 8px; border-bottom: 1px solid white;">' . number_format($totalPriceAfterDiscount, 2) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid white;">VAT (5%) :</td>
                        <td style="padding: 8px; border-bottom: 1px solid white;">' . number_format($vat, 2) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">GRAND TOTAL :</td>
                        <td style="padding: 8px; font-weight: bold;">' . number_format($finalPayableAmount, 2) . '</td>
                    </tr>
                </table>';

        // Signature & Stamp
        $html .= '
            <div style="margin-top: 10px; text-align: center;">
                <img src="' . $stampImagePath . '" alt="Stamp Image" style="max-width: 300px; height: auto;">
            </div>
        </div>';

        $html .= '
        <pagebreak />
            <!-- TERMS & CONDITIONS Section -->
            <div style="margin-top: 20px; font-size: 14px;"><br><br>
                <div style="text-align: center;">
                    <strong style="font-size: 18px;">TERMS & CONDITIONS</strong>
                </div><br><br>
                <ol style="font-size: 13px;">
                    <li><strong>Payment:</strong>
                        <ul>
                            <li>70% advance along with purchase order.</li><br>
                            <li>The balance 30% to be paid on completion of the project.</li><br>
                        </ul>
                    </li>
                    <li><strong>Warranty:</strong>
                        <ul>
                            <li>1 year warranty on any manufacturing defect.</li><br>
                            <li>The warranty covers quality issues of products but excludes problems caused by human error or external factors.</li><br>
                            <li>If a fault occurs, please send detailed pictures and videos. Our engineers will check the details to offer repair instructions or remote-control assistance (e.g., video calls, Zoom meetings).</li><br>
                            <li>If the fault cannot be fixed remotely, we will send free parts for replacement and guide you on how to repair them.</li><br>
                        </ul>
                    </li>
                    <li><strong>Delivery Period:</strong>
                        <ul>
                            <li>1-2 weeks, or as discussed and agreed upon.</li>
                            <li>45-50 days if the product is not available, or as discussed and agreed upon.</li><br>
                        </ul>
                    </li>
                    <li><strong>Validity of the Quotation:</strong>
                        <ul>
                            <li>30 days from the date of issue.</li><br>
                        </ul>
                    </li>
                    <li><strong>Revision Clause:</strong>
                        <ul>
                            <li>This quotation is subject to revision in case of any change in size, specification, quantity, location, and scope of work.</li><br>
                        </ul>
                    </li>
                    <li><strong>Delivery Timelines:</strong>
                        <ul>
                            <li>Delivery timelines are indicative and subject to customs inspections and clearance of partial or full items by border crossing countries.</li><br>
                        </ul>
                    </li>
                    <li><strong>Shipment Dates:</strong>
                        <ul>
                            <li>Shipment dates are subject to obtaining authority approvals on necessary documents and conformity certification.</li><br>
                        </ul>
                    </li>
                </ol>
            </div>
        ';

        $html .= '
        <div class="products-images" style="text-align: center; margin: 20px 3px;">';

        $displayedImages = [];

        foreach ($combinedProducts as $product) {
            $imagePaths = explode(',', $product['product_images']);

            foreach ($imagePaths as $image) {
                $imageSrc = $productImagesPath . trim($image);

                if (!in_array($imageSrc, $displayedImages)) {
                    $html .= '<pagebreak /><br><br>';

                    $html .= '
                        <div style="margin: 10px;">
                            <h4>' . htmlspecialchars($product['product_name']) . '</h4>
                            <img src="' . htmlspecialchars($imageSrc) . '" alt="' . htmlspecialchars($product['product_name']) . '" style="max-width: 80%; height: auto; border: 1px solid #ccc;">
                        </div>';

                    $displayedImages[] = $imageSrc;
                }
            }
        }

        $html .= '</div>';

        $html .= '</body></html>';

        // Write the HTML content to the PDF
        $mpdf->WriteHTML($html);

        // File name for the invoice
        $filename = "Sale_Quotations_" . $quotationId . ".pdf";
        $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
    }
}
