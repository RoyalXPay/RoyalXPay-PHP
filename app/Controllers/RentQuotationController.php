<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\BanksModel;
use App\Models\ProductModel;
use App\Libraries\Pagination;
use App\Models\QuotationModel;
use App\Models\DeletedQuotation;
use App\Models\DeliveryTimeModel;
use App\Models\QuotationProductsModel;
use App\Models\TermsAndConditionsModel;
use App\Controllers\BaseController;

class RentQuotationController extends BaseController
{
    protected $db;

    public function __construct()
    {
        // Connect to the database
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        set_title('Quotation List | ' . SITE_NAME);

        $data = [
            'action' => "rent-quotations",
            'pageTitle' => "Quotation List",
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
        $termsModel = new TermsAndConditionsModel();
        $deliveryTimeModel = new DeliveryTimeModel();
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
        $data['terms'] = $termsModel->getTermsAndConditions();
        $data['deliveryTimes'] = $deliveryTimeModel->getDeliveryTimes();

        $data['rentProducts'] = $productModel->getRentProductsDetails();
        $data['quotationProducts'] = $quotationProductsModel->getQuotationProductsDetails();
        // Pagination logic
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 50;
        $totalRecord = $quotationModel->getRentQuotations($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetching results for the current page
        $data['results'] = $quotationModel->getRentQuotations($data['searchArray'], $startLimit, $Limit);

        return view('admin/rent/quotation/index', $data);
    }

    public function create()
    {
        $data = [];
        $usersModel = new UsersModel();
        $productModel = new ProductModel();
        $termsModel = new TermsAndConditionsModel();
        $deliveryTimeModel = new DeliveryTimeModel();

        $data['customers'] = $usersModel->getUsersDetails();
        $data['terms'] = $termsModel->getTermsAndConditions();
        $data['deliveryTimes'] = $deliveryTimeModel->getDeliveryTimes();
        $data['rentProducts'] = $productModel->getProductsWithDetails("rent");

        return view('admin/rent/quotation/create', $data);
    }

    public function store()
    {
        $session = session();
        $quotationId = $this->request->getPost('quotation_id');
        $quotationCode = $this->request->getPost('quotation_code');
        if (!$this->validate([
            'productIds' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Please select at least one product.'
                ],
            ],
            'quantity' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Please specify the quantity.'
                ],
            ],
            'start_date' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Start date is required.',
                    'valid_date' => 'Start date must be a valid date.'
                ],
            ],
            'end_date' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'End date is required.',
                    'valid_date' => 'End date must be a valid date.'
                ],
            ],
            'price_per_day' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Price per day is required.'
                ],
            ],
        ])) {
            $session->setFlashdata('error', $this->validator->getErrors());
            return redirect()->to($quotationId ? site_url('rent-quotations/edit/' . $quotationId) : site_url('rent-quotations/create'));
        }

        $productModel = new ProductModel();
        $quotationModel = new QuotationModel();
        $quotationProductsModel = new QuotationProductsModel();

        // If quotation ID exists, we are updating, otherwise creating
        if ($quotationId) {
            $quotation = $quotationModel->find($quotationId);

            if (!$quotation) {
                $session->setFlashdata('error', 'Quotation not found.');
                return redirect()->to(site_url('rent-quotations/create'));
            }
        }

        // Get input values
        $productIds = $this->request->getPost('productIds');
        $productIds = array_map('trim', explode(',', $productIds));

        $quantities = $this->request->getPost('quantity');
        $quantities = array_map('trim', explode(',', $quantities));

        $pricePerDay = $this->request->getPost('price_per_day');
        $pricePerDay = array_map('trim', explode(',', $pricePerDay));

        $customerId = $this->request->getPost('customer_id');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $discount = $this->request->getPost('discount') ?: 0;
        $status = $this->request->getPost('status');

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

        // Validate start and end dates
        $startDateTime = strtotime($startDate);
        $endDateTime = strtotime($endDate);
        if (!$startDateTime || !$endDateTime) {
            $session->setFlashdata('error', 'Invalid start or end date format.');
            return redirect()->to($quotationId ? site_url('rent-quotations/edit/' . $quotationId) : site_url('rent-quotations/create'));
        }
        // Allow single-day contracts (end date can be equal to start date)
        if ($endDateTime < $startDateTime) {
            $session->setFlashdata('error', 'End date cannot be before the start date.');
            return redirect()->to($quotationId ? site_url('rent-quotations/edit/' . $quotationId) : site_url('rent-quotations/create'));
        }

        // Initialize totals
        $totalPrice = 0;
        $noOfDays = ($endDateTime - $startDateTime) / (60 * 60 * 24) + 1;

        // Fetch the rent product details using IN query
        $rentProducts = $productModel->getProductsWithDetails("rent", $productIds);
        $rentProductsAssoc = [];
        foreach ($rentProducts as $rentProduct) {
            $rentProductsAssoc[$rentProduct->product_id] = $rentProduct;
        }

        // Start a transaction
        $db = \Config\Database::connect();
        $db->transStart();

        // Prepare data for quotation creation/updation
        $data = [
            'quotation_code' => $quotationId ? $quotationCode : $quotationModel->generateQuotationCode("rent"),
            'user_id' => $customerId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_price' => $totalPrice,
            'vat' => 0,
            'discount' => $discount,
            'final_payable_price' => 0,
            'quotation_type' => "rent",
            'citylight_logo' => $citylightLogo,
            'citylight_address' => $citylightAddress,
            'contact_details' => $contactDetails,
            'citylight_email' => $citylightEmail,
            'citylight_website' => $citylightWebsite,
            'bank_details' => $bankDetails,
            'delivery_time' => $deliveryTime,
            'stamp' => $stamp,
            'signature' => $signature,
            'notes' => $notes,
            'return_policies' => $returnPolicies,
            'terms_conditions' => $termsConditions,
        ];

        // If updating an existing quotation
        if ($quotationId) {
            if (!$quotationModel->update($quotationId, $data)) {
                $db->transRollback();
                $session->setFlashdata('error', 'Failed to update quotation. Please try again.');
                return redirect()->to(site_url('rent-quotations/edit/' . $quotationId));
            }

            // Get the existing products associated with this quotation
            $existingProducts = $quotationProductsModel
                ->where('quotation_id', $quotationId)
                ->findAll();

            // Create an array of the current product IDs in the quotation
            $existingProductIds = array_column($existingProducts, 'product_id');

            // Find products that were removed (present in the database but not in the new list)
            $removedProducts = array_diff($existingProductIds, $productIds);

            // If there are products to be removed, delete them from the `quotationProducts` table
            if (!empty($removedProducts)) {
                $quotationProductsModel->where('quotation_id', $quotationId)
                    ->whereIn('product_id', $removedProducts)
                    ->delete();
            }
        } else {
            $data["quotation_date"] = date('Y-m-d H:i:s');

            // If creating a new quotation, save the quotation first
            if (!$quotationModel->save($data)) {

                $db->transRollback();
                $session->setFlashdata('error', 'Failed to create quotation. Please try again.');
                return redirect()->to(site_url('rent-quotations/create'));
            }

            // Get the newly created quotation_id (after saving the quotation)
            $quotationId = $quotationModel->getInsertID();
        }

        // Now handle quotation products (add or update)
        foreach ($productIds as $index => $productId) {
            $quantity = isset($quantities[$index]) ? $quantities[$index] : 0;
            $price = isset($pricePerDay[$index]) ? $pricePerDay[$index] : 0;

            // Check if the product exists in the fetched list
            if (!isset($rentProductsAssoc[$productId])) {
                $db->transRollback();
                $session->setFlashdata('error', "Product with ID '{$productId}' not found.");
                return redirect()->to(site_url('rent-quotations'));
            }

            $rentProduct = $rentProductsAssoc[$productId];

            // If updating an existing quotations, calculate the quantity difference
            // $existingQuantity = 0;
            if ($quotationId) {
                $existingProduct = $quotationProductsModel
                    ->where('quotation_id', $quotationId)
                    ->where('product_id', $productId)
                    ->first();

                // if ($existingProduct) {
                //     $existingQuantity = $existingProduct['quantity'];
                // }
            }

            // Calculate the quantity difference
            // $quantityDifference = $quantity - $existingQuantity;

            // Validate stock availability only for the quantity difference
            // if ($rentProduct->available_quantity < $quantityDifference) {
            //     $db->transRollback();
            //     $session->setFlashdata('error', "Not enough stock available for product '{$rentProduct->product_name}'.");
            //     return redirect()->to(site_url('rent-quotations'));
            // }

            // if ($status == "active") {
            //     // Deduct the quantity from stock (if the quotations is active)
            //     $productModel->update($productId, [
            //         'available_quantity' => $rentProduct->available_quantity - $quantityDifference,
            //     ]);
            // }

            // Calculate price for each product
            $totalPrice += $price * $quantity * $noOfDays;

            // Prepare quotations product data for insert/update
            $quotationProductData = [
                'quotation_id' => $quotationId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
            ];

            if ($quotationId) {
                // Check if the product already exists in quotations_products
                if ($existingProduct) {
                    // Update the existing entry if quantity or price has changed
                    $quotationProductsModel->update($existingProduct['quotation_product_id'], $quotationProductData);
                } else {
                    // Insert the new product into quotations_products if not found
                    $quotationProductsModel->save($quotationProductData);
                }
            } else {
                // Insert a new quotations product entry for a new quotations
                $quotationProductsModel->save($quotationProductData);
            }
        }

        // Calculate VAT (5%) and final payable amount
        $discountAmount = ($totalPrice * $discount) / 100;
        $subtotalAfterDiscount = $totalPrice - $discountAmount;
        $vat = $subtotalAfterDiscount * 0.05;
        $finalPayablePrice = $subtotalAfterDiscount + $vat;

        // Update quotation with final price and VAT
        $quotationModel->update($quotationId, [
            'total_price' => $totalPrice,
            'vat' => $vat,
            'final_payable_price' => $finalPayablePrice,
        ]);

        // Commit the transaction
        $db->transComplete();

        if ($db->transStatus() === false) {
            $session->setFlashdata('error', "Failed to save quotation. Please try again.");
            return redirect()->to(site_url('rent-quotations'));
        }

        $session->setFlashdata('success', 'Quotation saved successfully.');
        return redirect()->to(site_url('rent-quotations'));
    }

    public function edit($quotationId)
    {

        $data = [];
        $session = session();
        $usersModel = new UsersModel();
        $quotationModel = new QuotationModel();
        $termsModel = new TermsAndConditionsModel();
        $deliveryTimeModel = new DeliveryTimeModel();
        $quotationProductsModel = new QuotationProductsModel();

        $data['customers'] = $usersModel->getUsersDetails();
        $data['terms'] = $termsModel->getTermsAndConditions();
        $data['deliveryTimes'] = $deliveryTimeModel->getDeliveryTimes();
        $quotationDetails = $quotationModel->getQuotationWithDetails($quotationId);
        $quotationProductsDetails = $quotationProductsModel->getRentQuotationProductsDetails($quotationId);

        $productIds = array_map(function ($product) {
            return $product['product_id'];
        }, $quotationProductsDetails);

        $startDate = $quotationDetails['start_date'];
        $endDate = $quotationDetails['end_date'];
        if ($startDate > $endDate) {
            $session->setFlashdata('error', 'Start date cannot be greater than end date.');
            return redirect()->to(site_url('rent-quotations/edit/' . $quotationId));
        }

        // Begin a database transaction to ensure atomicity of the updates
        $this->db->transStart();

        // Step 1: Update expired quotations
        $currentDate = date('Y-m-d');

        // Find quotations that are expired (end_date < currentDate) and are still active
        $expiredQuotations = $this->db->table('quotations')
            ->select('quotation_id')
            ->where('end_date <', $currentDate)
            ->where('status', 'active')
            ->get()
            ->getResult();

        // Extract the quotation_ids from the expired quotations
        $quotationIds = array_map(function ($quotation) {
            return $quotation->quotation_id;
        }, $expiredQuotations);

        // Check if there are expired quotations to process
        if (count($quotationIds) > 0) {
            // Step 1.1: Get the associated products of the expired quotations
            $quotationsProducts = $this->db->table('quotation_products')
                ->select('quotation_id, product_id, quantity')
                ->whereIn('quotation_id', $quotationIds)
                ->get()
                ->getResult();

            // Step 1.2: Update available quantity in products table for each expired quotation product
            foreach ($quotationsProducts as $quotationProduct) {
                $this->db->table('products')
                    ->where('product_id', $quotationProduct->product_id)
                    ->set('available_quantity', 'available_quantity + ' . $quotationProduct->quantity, false)
                    ->update();
            }

            // Step 1.3: Update quotations to "completed" status after processing product returns
            $this->db->table('quotations')
                ->whereIn('quotation_id', $quotationIds)
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
        $activeRentals = $this->db->table('quotation_products')
            ->select('quotation_products.product_id, SUM(quotation_products.quantity) as total_rented')
            ->join('quotations', 'quotations.quotation_id = quotation_products.quotation_id', 'left')
            ->where('quotations.status', 'active')
            ->groupStart()
            ->where('quotations.start_date <=', $endDate)
            ->where('quotations.end_date >=', $startDate)
            ->groupEnd()
            ->whereNotIn('quotation_products.product_id', $productIds)
            ->groupBy('quotation_products.product_id')
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

            // Subtract rented quantity if there are active quotations
            if (isset($rentedQuantities[$product->product_id])) {
                $availableQuantity -= $rentedQuantities[$product->product_id];
            }

            // Ensure available quantity is not negative
            $product->available_quantity = max($availableQuantity, 0);
        }

        // Commit the database transaction
        $this->db->transComplete();

        // Check if the transaction was successful
        if ($this->db->transStatus() === false) {
            $session->setFlashdata('error', 'Failed to update rent products.');
            return redirect()->to(site_url('rent-quotations/edit/' . $quotationId));
        }

        $quotationProductMap = [];
        foreach ($quotationProductsDetails as $quotationProduct) {
            $quotationProductMap[$quotationProduct['product_id']] = $quotationProduct;
        }

        $data['rentProducts'] = $products;
        $data['records'] = $quotationDetails;
        $data["quotationProductMap"] = $quotationProductMap;

        return view('admin/rent/quotation/create', $data);
    }

    public function showDetails($quotationId)
    {

        $data = array();
        $data['pageTitle'] = "Quotation Details";

        $quotationModel = new QuotationModel();
        $quotationProductsModel = new QuotationProductsModel();

        $data['record'] = $quotationModel->getQuotationWithDetails($quotationId);
        $data["record"]["quotationProducts"] = $quotationProductsModel->getRentQuotationProductsDetails($quotationId);

        return view('admin/rent/quotation/preview', $data);
    }

    public function delete($id)
    {
        // Validate that $id is numeric and not empty
        if (is_numeric($id) && !empty($id)) {
            try {
                // Check if the record exists
                $quotationModel = new QuotationModel();
                $quotation = $quotationModel->find($id);

                $productModel = new ProductModel();
                $deletedQuotationModel = new DeletedQuotation();
                $quotationProductsModel = new QuotationProductsModel();

                // If quotation is found and active
                if ($quotation && $quotation["status"] == 'active') {
                    // Get the quotation products related to the quotation
                    $quotationProducts = $quotationProductsModel->where('quotation_id', $id)->findAll();

                    // Loop through each quotation product to release quantity
                    foreach ($quotationProducts as $quotationProduct) {
                        $productId = $quotationProduct['product_id'];
                        $quantity = $quotationProduct['quantity'];

                        // Get the current product details from ProductModel
                        $rentProductDetails = $productModel->where('product_id', $productId)->first();

                        // If the product exists, update the available quantity
                        if ($rentProductDetails) {
                            // Increment the available_quantity based on the quotation quantity
                            $newAvailableQuantity = $rentProductDetails['available_quantity'] + (int)$quantity;

                            // Update the rent product's available quantity
                            $productModel->where('product_id', $productId)->set(['available_quantity' => $newAvailableQuantity])->update();
                        }
                    }
                }

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

    public function fetchRentProducts()
    {
        // Retrieve the start and end dates from the GET request
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        // Validate the dates to ensure they are provided
        if (!$startDate || !$endDate) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid dates provided.']);
        }

        // Ensure that start date is not greater than end date by directly comparing the strings
        $startDateObj = new \DateTime($startDate);
        $endDateObj = new \DateTime($endDate);
        if ($startDateObj > $endDateObj) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Start date cannot be greater than end date.']);
        }

        // Begin a database transaction to ensure atomicity of the updates
        $this->db->transStart();

        // Step 1: Update expired quotations
        $currentDate = date('Y-m-d');

        // Find quotations that are expired (end_date < currentDate) and are still active
        $expiredQuotations = $this->db->table('quotations')
            ->select('quotation_id')
            ->where('end_date <', $currentDate)
            ->where('status', 'active')
            ->get()
            ->getResult();

        // Extract the quotation_ids from the expired quotations
        $quotationIds = array_map(function ($quotation) {
            return $quotation->quotation_id;
        }, $expiredQuotations);

        // Check if there are expired quotations to process
        if (count($quotationIds) > 0) {
            // Step 1.1: Get the associated products of the expired quotations
            $quotationsProducts = $this->db->table('quotation_products')
                ->select('quotation_id, product_id, quantity')
                ->whereIn('quotation_id', $quotationIds)
                ->get()
                ->getResult();

            // Step 1.2: Update available quantity in products table for each expired quotation product
            foreach ($quotationsProducts as $quotationProduct) {
                $this->db->table('products')
                    ->where('product_id', $quotationProduct->product_id)
                    ->set('available_quantity', 'available_quantity + ' . $quotationProduct->quantity, false)
                    ->update();
            }

            // Step 1.3: Update quotations to "completed" status after processing product returns
            $this->db->table('quotations')
                ->whereIn('quotation_id', $quotationIds)
                ->update(['status' => 'completed']);
        }

        // Step 2: Fetch available rent products with additional details (without aliases)
        $products = $this->db->table('products')
            ->select('products.*, product_master.product_name, product_master.product_images, product_master.product_type as master_product_type, product_master.serial_number, sizes.size_in_inches, colors.name AS color_name, touch_types.name AS touch_name, glass_types.name AS glass_name')
            ->join('product_master', 'product_master.product_master_id = products.product_master_id', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->where('products.product_type', 'rent')
            ->get()
            ->getResult();

        // Fetch active rentals for the given date range (without aliases)
        $activeRentals = $this->db->table('quotation_products')
            ->select('quotation_products.product_id, SUM(quotation_products.quantity) AS total_rented')
            ->join('quotations', 'quotation_products.quotation_id = quotations.quotation_id', 'left')
            ->where('quotations.status', 'active')
            ->groupStart()
            ->where('quotations.start_date <=', $endDate)
            ->where('quotations.end_date >=', $startDate)
            ->groupEnd()
            ->groupBy('quotation_products.product_id')
            ->get()
            ->getResult();

        $rentedQuantities = [];
        foreach ($activeRentals as $rental) {
            $rentedQuantities[$rental->product_id] = $rental->total_rented;
        }

        // Adjust available quantity for each product
        foreach ($products as $product) {
            $availableQuantity = $product->quantity;

            // Subtract rented quantity if there are active quotations
            if (isset($rentedQuantities[$product->product_id])) {
                $availableQuantity -= $rentedQuantities[$product->product_id];
            }

            // Ensure available quantity is not negative
            $product->available_quantity = max($availableQuantity, 0);
        }

        // Commit the database transaction
        $this->db->transComplete();

        // Check if the transaction was successful
        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update rent products.']);
        }

        // Return the adjusted products as a JSON response
        return $this->response->setJSON(['status' => 'success', 'products' => $products]);
    }

    public function generateQuotation($quotationId)
    {
        $banksModel = new BanksModel();
        $bankDetails = $banksModel->first();
        $quotationModel = new QuotationModel();
        $quotationProductsModel = new QuotationProductsModel();

        $quotationDetails = $quotationModel->getQuotationWithDetails($quotationId);
        $quotationProductDetails = $quotationProductsModel->getRentQuotationProductsDetails($quotationId);

        // Validate start and end dates
        $startDateTime = strtotime($quotationDetails["start_date"]);
        $endDateTime = strtotime($quotationDetails["end_date"]);

        // Calculate rental duration
        $noOfDays = ($endDateTime - $startDateTime) / (60 * 60 * 24) + 1;

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

        // Initialize totals
        $subtotal = 0;
        $productNumber = 1;

        // Combine products with the same details
        $combinedProducts = [];

        foreach ($quotationProductDetails as $product) {
            $key = md5($product['product_name'] . $product['quantity'] . $quotationDetails['start_date'] . $quotationDetails['end_date'] . $product['price']);

            if (isset($combinedProducts[$key])) {
                $combinedProducts[$key]['quantity'] += $product['quantity'];
            } else {
                $combinedProducts[$key] = $product;
            }
        }

        // Start HTML content for PDF
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
                    RENT
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
                            <th style="padding: 10px; border: 3px solid white; width: 15%; font-weight: normal;">FROM</th>
                            <th style="padding: 10px; border: 3px solid white; width: 15%; font-weight: normal;">TO</th>
                            <th style="padding: 10px; border: 3px solid white; width: 14%; font-weight: normal;">PRICE</th>
                        </tr>
                    </thead>
                    <tbody>';

        $totalStringLength = "";
        foreach ($combinedProducts as $product) {
            $productTotal = $product['price'] * $product['quantity'] * $noOfDays;
            $totalStringLength .= strlen($product['product_name']) + strlen($product['description']);
            $html .= '
                    <tr style="background: linear-gradient(to right, #f8e6ff, #e5b9f2); color: black; font-weight: bold; border: 3px solid white;">
                        <td style="padding: 10px; border: 3px solid white;">' . $productNumber++ . '</td>
                        <td style="padding: 10px; border: 3px solid white; text-align: left;">
                            <strong style="font-size: 16px;">' . htmlspecialchars($product['product_name']) . '</strong><br>
                            <span style="font-size: 12px; font-weight: normal;">' . nl2br(htmlspecialchars($product['description'])) . '</span>
                        </td>
                        <td style="padding: 10px; border: 3px solid white; font-size: 16px;">' . number_format($product['quantity'], 0) . '</td>
                        <td style="padding: 10px; border: 3px solid white; font-size: 16px;">' . date('j/n/Y', strtotime($quotationDetails['start_date'])) . '</td>
                        <td style="padding: 10px; border: 3px solid white; font-size: 16px;">' . date('j/n/Y', strtotime($quotationDetails['end_date'])) . '</td>
                        <td style="padding: 10px; border: 3px solid white; font-size: 16px;">' . $productTotal . '</td>
                    </tr>';

            $subtotal += $productTotal;
        }

        // Apply discount
        $discountAmount = ($subtotal * $quotationDetails["discount"]) / 100;
        $subtotalAfterDiscount = $subtotal - $discountAmount;

        // Calculate VAT (5%) on the discounted amount
        $vat = $subtotalAfterDiscount * 0.05;

        // Total price should include the VAT after discount calculation
        $finalPayablePrice = $subtotalAfterDiscount + $vat;

        $html .= '</tbody></table>';

        $html .= '
                <div style="background-color: #8a2be2; height: 10px; padding: 5px; margin-top: 5px;">
                </div>';

        if ($totalStringLength >= 600) {
            $html .= '<pagebreak>';
        }
        $html .= '
            <div style="margin-top: 20px;"><br><br>
                <!-- Payment Terms Section -->
                <div style="margin-top: 10px; font-size: 14px;">
                    <div style="text-align: center;">
                        <strong style="font-size: 18px;">PAYMENT TERMS</strong>
                    </div><br><br>
                    <strong style="font-size: 12px;">Payment of the full amount in advance with insurance must be made by bank <br> transfer to account number</strong>
                </div>

                <!-- Company Account Details -->
                <div style="margin-top: 8px; font-size: 14px;">
                    <p><strong>Company Account:</strong></p>
                    <p>Vatin: <strong>OM1100285967</strong></p>
                    <p>Bank Name: <strong>' . $bankDetails["bank_name"] . '</strong></p>
                    <p>Account Name: <strong>' . $bankDetails["account_holder"] . '</strong></p>
                    <p>Swift Code: <strong>' . $bankDetails["swift_code"] . '</strong></p>
                    <p>Account Number: <strong>' . $bankDetails["account_number"] . '</strong></p>
                    <p>With a receipt sent to the number <strong>71716060</strong> along with the contract number.</p>
                </div><br>

                <!-- Price Summary Section (Right Side) -->
                <div style="position: relative; float: right; width: 35%; margin-top: -310px; text-align: right;">
                    <table style="width: 100%; background: linear-gradient(to right, #8a2be2, #c07ff8); color: white; font-size: 14px; padding: 10px; border-radius: 8px;">
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid white;">TOTAL PRICE BEFORE DISCOUNT :</td>
                            <td style="padding: 8px; border-bottom: 1px solid white;">' . number_format($subtotal, 2) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid white;">TOTAL PRICE AFTER DISCOUNT :</td>
                            <td style="padding: 8px; border-bottom: 1px solid white;">' . number_format($subtotalAfterDiscount, 2) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid white;">VAT (5%) :</td>
                            <td style="padding: 8px; border-bottom: 1px solid white;">' . number_format($vat, 2) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; font-weight: bold;">GRAND TOTAL :</td>
                            <td style="padding: 8px; font-weight: bold;">' . number_format($finalPayablePrice, 2) . '</td>
                        </tr>
                    </table>

                    <!-- Signature & Stamp -->
                    <div style="margin-top: 10px; text-align: center;">
                        <img src="' . $stampImagePath . '" alt="Stamp Image" style="max-width: 300px; height: auto;">
                    </div>
                </div>
            </div>
            <pagebreak />
            <!-- Warranty Terms Section -->
            <div style="margin-top: 20px; font-size: 14px;"><br><br>
                <div style="text-align: center;">
                    <strong style="font-size: 18px;">TERMS & CONDITIONS</strong>
                </div><br><br>
                <ol style="font-size: 13px;">
                    <li><strong>Payment:</strong>
                        <ul>
                            <li>50% advance along with purchase order.</li><br>
                            <li>The balance 50% to be paid on completion of the project.</li><br>
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
        $filename = "Rent_Quotations_" . $quotationId . ".pdf";
        $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
    }
}
