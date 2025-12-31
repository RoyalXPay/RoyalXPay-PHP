<?php

namespace App\Controllers;

use App\Models\SizesModel;
use App\Models\ColorsModel;
use App\Models\ProductModel;
use App\Models\CompanyModel;
use App\Libraries\Pagination;
use App\Models\PurchaseModel;
use App\Models\ResolutionModel;
use App\Models\GlassTypesModel;
use App\Models\TouchTypesModel;
use App\Models\ProductMasterModel;
use App\Models\PurchaseItemsModel;
use App\Controllers\BaseController;

class PurchaseController extends BaseController
{
    protected $pagination;
    protected $sizesModel;
    protected $colorsModel;
    protected $productModel;
    protected $companyModel;
    protected $purchaseModel;
    protected $glassTypesModel;
    protected $touchTypesModel;
    protected $resolutionModel;
    protected $purchaseItemsModel;
    protected $productMasterModel;

    public function __construct()
    {
        // Initialize models
        $this->pagination = new Pagination();
        $this->sizesModel = new SizesModel();
        $this->colorsModel = new ColorsModel();
        $this->productModel = new ProductModel();
        $this->companyModel = new CompanyModel();
        $this->purchaseModel = new PurchaseModel();
        $this->glassTypesModel = new GlassTypesModel();
        $this->touchTypesModel = new TouchTypesModel();
        $this->resolutionModel = new ResolutionModel();
        $this->productMasterModel = new ProductMasterModel();
        $this->purchaseItemsModel = new PurchaseItemsModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        set_title('Purchase List | ' . SITE_NAME);

        $data = [
            'action' => "purchases",
            'pageTitle' => "Purchase List",
            'startLimit' => 0,
            'reverse' => 0,
            'pagination' => '',
            'results' => [],
            'searchArray' => []
        ];

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

        $Limit = 50;
        $page = (int) $this->request->getGet('page') ?: 1;
        $totalRecord = $this->purchaseModel->getPurchaseDetails($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['startLimit'] = $startLimit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['pagination'] = $this->pagination->getPaginate($totalRecord, $page, $Limit);

        $purchaseItems = $this->purchaseItemsModel->getAllPurchaseItemsWithProduct();
        $purchaseProductArray = $this->productMasterModel
            ->select('purchase_id, parent_master_id, product_type, serial_number')
            ->where('product_type', 'purchase')
            ->findAll();

        $purchaseProductRecords = $this->productMasterModel
            ->select('product_master.purchase_id, product_master.product_master_id, product_master.parent_master_id, product_master.serial_number, product_master.product_type as master_product_type, products.product_type, products.product_source, products.quantity')
            ->join('products', 'products.product_master_id = product_master.product_master_id', 'left')
            ->where('product_master.product_type', 'purchase')
            ->findAll();

        $inventorySummaryArray = [];

        foreach ($purchaseProductRecords as $productRecord) {
            $currentPurchaseId = $productRecord['purchase_id'];
            $currentParentMasterId = $productRecord['parent_master_id'];

            // Initialize the array structure if not exists
            if (!isset($inventorySummaryArray[$currentPurchaseId][$currentParentMasterId])) {
                $inventorySummaryArray[$currentPurchaseId][$currentParentMasterId] = [
                    'rent' => 0,
                    'sale' => 0,
                    'available' => 0
                ];
            }

            $currentProductType = strtolower($productRecord['product_type']);

            if ($currentProductType == "rent") {
                $inventorySummaryArray[$currentPurchaseId][$currentParentMasterId]["rent"] = $productRecord['quantity'];
            } else if ($currentProductType == "sale") {
                $inventorySummaryArray[$currentPurchaseId][$currentParentMasterId]["sale"] = $productRecord['quantity'];
            } else {
                // Initialize available if not set, then increment
                if (!isset($inventorySummaryArray[$currentPurchaseId][$currentParentMasterId]["available"])) {
                    $inventorySummaryArray[$currentPurchaseId][$currentParentMasterId]["available"] = 0;
                }
                $inventorySummaryArray[$currentPurchaseId][$currentParentMasterId]["available"] += 1;
            }
        }

        $serialNumbers = [];

        foreach ($purchaseProductArray as $item) {
            $purchaseId = $item['purchase_id'];

            // Initialize the array for each purchase_id if not already set
            if (!isset($serialNumbers[$purchaseId])) {
                $serialNumbers[$purchaseId] = [];
            }

            // Add the serial number to the corresponding purchase_id
            $serialNumbers[$purchaseId][] = $item['serial_number'];
        }

        $totalQuantities = [];

        foreach ($purchaseItems as $item) {
            $purchaseId = $item['purchase_id'];

            if (!isset($totalQuantities[$purchaseId])) {
                $totalQuantities[$purchaseId] = 0;
            }

            $totalQuantities[$purchaseId] += $item['quantity'];
        }

        $data['serialNumbers'] = $serialNumbers;
        $data['totalQuantities'] = $totalQuantities;
        $data['inventorySummaryArray'] = $inventorySummaryArray;
        $data['results'] = $this->purchaseModel->getPurchaseDetails($data['searchArray'], $startLimit, $Limit);

        return view('admin/purchase/index', $data);
    }

    public function create()
    {
        set_title('Create Purchase | ' . SITE_NAME);

        $data = [];
        $data['products'] = $this->productMasterModel->getProducts();
        $data['companies'] = $this->companyModel->getCompanyDetails();

        return view('admin/purchase/create', $data);
    }

    public function edit($purchaseId)
    {
        set_title('Edit Purchase | ' . SITE_NAME);

        $data = [];
        $session = session();

        // Fetch necessary data
        $data['products'] = $this->productMasterModel->getProducts();
        $data['companies'] = $this->companyModel->getCompanyDetails();
        $purchaseProductsArray = $this->productMasterModel->getProductsByPurchaseId($purchaseId);
        $purchaseItemsArray = $this->purchaseItemsModel->getPurchaseItemsWithProduct($purchaseId);

        // Get the purchase details
        $purchaseDetails = $this->purchaseModel->find($purchaseId);
        if (!$purchaseDetails) {
            $session->setFlashdata('error', "Purchase record not found.");
            return redirect()->to(site_url('purchases'));
        }

        $serialNumberArray = [];

        foreach ($purchaseProductsArray as $purchaseProductDetails) {
            $productId = $purchaseProductDetails['purchase_id'];
            $parentMasterId = $purchaseProductDetails['parent_master_id'];

            // Initialize array if it doesn't exist
            if (!isset($serialNumberArray[$productId][$parentMasterId])) {
                $serialNumberArray[$productId][$parentMasterId] = [
                    'serial_numbers' => []
                ];
            }

            // Append serial number if available
            if (!empty($purchaseProductDetails['serial_number'])) {
                $serialNumberArray[$productId][$parentMasterId]['serial_numbers'][] = $purchaseProductDetails['serial_number'];
            }
        }

        $productDetails = [];

        foreach ($purchaseItemsArray as $item) {
            $productId = $item['product_master_id'];
            $serialNumbers = $serialNumberArray[$item['purchase_id']][$productId]["serial_numbers"] ?? [];

            $productDetails[$productId] = [
                'product_master_id' => $productId,
                'quantity' => $item['quantity'],
                'price_per_unit' => $item['price_per_unit'],
                'cbm_amount' => $item['cbm_amount'],
                'shipment_price' => $item['shipment_price'],
                'serial_numbers' => $serialNumbers
            ];
        }

        $purchaseDetails["purchaseItems"] = $productDetails;
        $data['record'] = $purchaseDetails;

        // Pass data to the view
        return view('admin/purchase/create', $data);
    }

    public function save()
    {
        $session = session();
        $validation = \Config\Services::validation();
        $purchaseId = $this->request->getPost('purchase_id');

        // Validate input
        if (!$this->validate([
            'company_id' => 'required',
            'productIds' => 'required',
            'quantity' => 'required',
        ])) {
            $session->setFlashdata('error', $validation->getErrors());
            if ($purchaseId) {
                return redirect()->to(site_url('purchases/edit/' . $purchaseId));
            } else {
                return redirect()->to(site_url('purchases/create'));
            }
        }

        // Get input values
        $companyId = $this->request->getPost('company_id');
        $productIds = $this->request->getPost('productIds');
        $quantities = $this->request->getPost('quantity');
        $pricesPerUnit = $this->request->getPost('price_per_unit');
        $cbmAmount = $this->request->getPost('cbm_amount');
        $shipmentPrice = $this->request->getPost('shipment_price');

        // Agent Details
        $agentPrice = (float) $this->request->getPost('agent_price') ?: 0;
        $agentCBM = $this->request->getPost('agent_cbm');
        $agentPaymentStatus = $this->request->getPost('agent_payment_status');
        $agentPaymentMethod = $this->request->getPost('agent_payment_method');
        $agentAttachmentPath = $this->handleFileUpload('agent_attachment', $purchaseId, 'agent_attachments');
        $agentStatus = $this->request->getPost('agent_status');
        $agentNotes = $this->request->getPost('agent_notes');

        // Payment Details
        $wasChinaPaid = $this->request->getPost('was_china_paid');
        $paymentDate = $this->request->getPost('payment_date');
        $paymentStatus = $this->request->getPost('payment_status');
        $paymentMethod = $this->request->getPost('payment_method');
        $receiptFilePath = $this->handleFileUpload('uploaded_receipt', $purchaseId, 'receipts');
        $purchaseStatus = $this->request->getPost('purchase_status');
        $notes = $this->request->getPost('notes');

        // Filter and map selected products
        $selectedProductIds = array_filter($productIds, fn($productId) => !empty($productId));
        $selectedQuantities = array_map(fn($index) => $quantities[$index], array_keys($selectedProductIds));
        $selectedCBMAmount = array_map(fn($index) => $cbmAmount[$index], array_keys($selectedProductIds));
        $selectedPricesPerUnit = array_map(fn($index) => $pricesPerUnit[$index], array_keys($selectedProductIds));

        $masterProducts = $this->productMasterModel->searchProducts($selectedProductIds);
        $masterProductsAssoc = [];
        foreach ($masterProducts as $productDetails) {
            $masterProductsAssoc[$productDetails->product_master_id] = [
                'product_master_id' => $productDetails->product_master_id,
                'product_name' => $productDetails->product_name,
                'description' => $productDetails->description,
                'product_price' => $productDetails->product_price,
                'discount_percentage' => $productDetails->discount_percentage,
                'final_product_price' => $productDetails->final_product_price,
                'size_id' => $productDetails->size_id,
                'color_id' => $productDetails->color_id,
                'touch_id' => $productDetails->touch_id,
                'glass_id' => $productDetails->glass_id,
                'resolution_id' => $productDetails->resolution_id,
                'product_images' => $productDetails->product_images,
                'additional_information' => $productDetails->additional_information
            ];
        }

        // Start a transaction
        $db = \Config\Database::connect();
        $db->transStart();

        if ($purchaseId) {
            // Get existing purchase items
            $existingItems = $this->purchaseItemsModel
                ->where('purchase_id', $purchaseId)
                ->findAll();

            // Convert to array of product_master_ids
            $existingProductIds = array_column($existingItems, 'product_master_id');

            // Find items to delete (exist in DB but not in current selection)
            $itemsToDelete = array_diff($existingProductIds, $selectedProductIds);

            // Delete the unchecked items
            if (!empty($itemsToDelete)) {
                $this->purchaseItemsModel
                    ->where('purchase_id', $purchaseId)
                    ->whereIn('product_master_id', $itemsToDelete)
                    ->delete();

                // Also delete related serial numbers in product_master table
                $this->productMasterModel
                    ->where('purchase_id', $purchaseId)
                    ->whereIn('parent_master_id', $itemsToDelete)
                    ->delete();
            }
        }

        $totalCBM = 0;
        $totalAmount = 0;
        $totalProductAmount = 0;

        // Iterate through selected products to calculate totals
        foreach ($selectedProductIds as $index => $productId) {
            $quantity = (float) $selectedQuantities[$index];
            $price = (float) $selectedPricesPerUnit[$index];
            $cbm = (float) $selectedCBMAmount[$index];
            $shipment = (float) $shipmentPrice[$index];

            if ($quantity <= 0 || $price <= 0) {
                $db->transRollback();
                $session->setFlashdata('error', "Invalid quantity or price for product '{$productId}'");
                if ($purchaseId) {
                    return redirect()->to(site_url('purchases/edit/' . $purchaseId));
                } else {
                    return redirect()->to(site_url('purchases/create'));
                }
            }

            // Calculate total for this product row (quantity * price + shipment price + cbm)
            $productTotal = ($quantity * $price) + $shipment + $cbm;
            $totalProductAmount += $productTotal;
            $totalAmount += $productTotal;
            $totalCBM += $cbm;
        }

        // Get the values for Export Wooden Case and Send to Foshan Warehouse
        $exportWoodenCase = (float) $this->request->getPost('export_wooden_case') ?? 0;
        $sendToFoshanWarehouse = (float) $this->request->getPost('send_to_foshan_warehouse') ?? 0;

        $totalAmount += ($exportWoodenCase + $sendToFoshanWarehouse);

        // Add Agent CBM to total CBM
        $totalCBM += $agentCBM;

        $finalTotalAmount = $totalAmount + $agentPrice;

        // Prepare the data for saving the purchase
        $purchaseData = [
            'company_id' => $companyId,
            'product_amount' => $totalProductAmount,
            'export_wooden_case' => $exportWoodenCase,
            'send_to_warehouse' => $sendToFoshanWarehouse,
            'total_amount' => $totalAmount,
            'was_china_paid' => $wasChinaPaid,
            'payment_date' => $paymentDate,
            'payment_status' => $paymentStatus,
            'payment_method' => $paymentMethod,
            'purchase_status' => $purchaseStatus,
            'uploaded_receipt' => $receiptFilePath,
            'notes' => $notes,
            'agent_price' => $agentPrice,
            'agent_cbm' => $agentCBM,
            'final_total_amount' => $finalTotalAmount,
            'agent_payment_method' => $agentPaymentMethod,
            'agent_payment_status' => $agentPaymentStatus,
            'total_cbm' => $totalCBM,
            'agent_status' => $agentStatus,
            'agent_attachment' => $agentAttachmentPath,
            'agent_notes' => $agentNotes,
        ];

        $updateMode = false;
        if ($purchaseId) {
            $updateMode = true;
            if (!$this->purchaseModel->update($purchaseId, $purchaseData)) {
                $db->transRollback();
                $session->setFlashdata('error', 'Failed to update purchase. Please try again.');
                return redirect()->to(site_url('purchases/edit/' . $purchaseId));
            }
        } else {
            if (!$this->purchaseModel->save($purchaseData)) {
                $db->transRollback();
                $session->setFlashdata('error', 'Failed to create purchase. Please try again.');
                return redirect()->to(site_url('purchases/create'));
            }

            $purchaseId = $this->purchaseModel->getInsertID();
        }

        $allSerialNumbers = [];
        foreach ($selectedProductIds as $productId) {
            // Handle serial numbers if available
            $allSerialNumbers[] = $_POST['serial_numbers'][$productId] ?? [];
        }

        // Flatten the array
        $flattenedSerialNumbers = array_merge(...$allSerialNumbers);

        foreach ($selectedProductIds as $index => $productId) {
            // Handle serial numbers if available
            $serialNumbers = $_POST['serial_numbers'][$productId] ?? [];
            if (!empty($serialNumbers)) {

                // Get existing serial numbers for this product and purchase
                $existingSerialNumbers = $this->productMasterModel
                    ->where('purchase_id', $purchaseId)
                    ->whereNotIn('serial_number', $flattenedSerialNumbers)
                    ->findAll();

                // Delete the serial numbers that are no longer submitted
                if (!empty($existingSerialNumbers)) {
                    foreach ($existingSerialNumbers as $serialNumberToDelete) {
                        $this->productMasterModel->where('serial_number', $serialNumberToDelete['serial_number'])
                            ->where('purchase_id', $purchaseId)
                            ->delete();
                    }
                }
            }
        }

        $allSerialNumbers = [];
        // Handle purchase items (add, update, or delete serial numbers)
        if ($purchaseId) {
            foreach ($selectedProductIds as $index => $productId) {
                $quantity = (float) $selectedQuantities[$index];
                $price = (float) $selectedPricesPerUnit[$index];
                $cbm = (float) $selectedCBMAmount[$index];
                $shipment = (float) $shipmentPrice[$index];

                $purchaseItemData = [
                    'purchase_id' => $purchaseId,
                    'product_master_id' => $productId,
                    'quantity' => $quantity,
                    'price_per_unit' => $price,
                    'cbm_amount' => $cbm,
                    'shipment_price' => $shipment,
                ];

                // Handle serial numbers if available
                $serialNumbers = $_POST['serial_numbers'][$productId] ?? [];
                if (!empty($serialNumbers)) {

                    // Insert new serial numbers
                    foreach ($serialNumbers as $serialNumber) {
                        if (in_array($serialNumber, $allSerialNumbers)) {
                            $productName = $masterProductsAssoc[$productId]['product_name'] ?? 'Unknown Product';
                            $session->setFlashdata('error', 'Duplicate serial number detected for product: ' . $productName . ' (Serial Number: ' . $serialNumber . ')');
                            if ($updateMode) {
                                return redirect()->to(site_url('purchases/edit/' . $purchaseId));
                            } else {
                                return redirect()->to(site_url('purchases/create'));
                            }
                        }

                        // Add the serial number to the global array
                        $allSerialNumbers[] = $serialNumber;

                        // Check if the serial number already exists in the database
                        $existingSerialNumber = $this->productMasterModel
                            ->where('serial_number', $serialNumber)
                            ->where('purchase_id !=', $purchaseId)
                            ->where('parent_master_id !=', $productId)
                            ->first();

                        if (!empty($existingSerialNumber)) {
                            // If serial number exists for another purchase or product, trigger an error
                            $productName = $masterProductsAssoc[$productId]['product_name'] ?? 'Unknown Product';

                            $session->setFlashdata('error', 'Duplicate serial number detected for product: ' . $productName . ' (Serial Number: ' . $serialNumber . ')');
                            if ($updateMode) {
                                return redirect()->to(site_url('purchases/edit/' . $purchaseId));
                            } else {
                                return redirect()->to(site_url('purchases/create'));
                            }
                        }

                        $isProductExisting = $this->productMasterModel
                            ->where('serial_number', $serialNumber)
                            ->where('purchase_id', $purchaseId)
                            ->where('parent_master_id', $productId)
                            ->first();

                        if (empty($isProductExisting)) {
                            try {
                                $data = [
                                    'product_type' => "purchase",
                                    'purchase_id' => $purchaseId,
                                    'parent_master_id' => $productId,
                                    'product_name' => $masterProductsAssoc[$productId]['product_name'],
                                    'description' => $masterProductsAssoc[$productId]['description'],
                                    'product_price' => $masterProductsAssoc[$productId]['product_price'],
                                    'serial_number' => $serialNumber,
                                    'size_id' => $masterProductsAssoc[$productId]['size_id'],
                                    'color_id' => $masterProductsAssoc[$productId]['color_id'],
                                    'glass_id' => $masterProductsAssoc[$productId]['glass_id'],
                                    'touch_id' => $masterProductsAssoc[$productId]['touch_id'],
                                    'resolution_id' => $masterProductsAssoc[$productId]['resolution_id'],
                                    'discount_percentage' => $masterProductsAssoc[$productId]['discount_percentage'],
                                    'final_product_price' => $masterProductsAssoc[$productId]['final_product_price'],
                                    'product_images' => $masterProductsAssoc[$productId]['product_images'],
                                    'additional_information' => $masterProductsAssoc[$productId]['additional_information'],
                                ];

                                $this->productMasterModel->save($data);
                            } catch (\Exception $e) {
                                log_message('error', 'Error saving serial number: ' . $e->getMessage());
                                session()->setFlashdata('error', 'Failed to save purchase. Error with serial number: ' . $serialNumber);
                                return redirect()->to(site_url('purchases'));
                            }
                        }
                    }
                }

                // Save or update purchase item
                $existingItem = $this->purchaseItemsModel
                    ->where('purchase_id', $purchaseId)
                    ->where('product_master_id', $productId)
                    ->first();

                try {
                    // Save or update purchase item
                    if ($existingItem) {
                        $this->purchaseItemsModel->update($existingItem['purchase_item_id'], $purchaseItemData);
                    } else {
                        $this->purchaseItemsModel->save($purchaseItemData);
                    }
                } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
                    log_message('error', 'Database Error: ' . $e->getMessage());
                }
            }
        }

        // Complete the transaction
        $db->transComplete();

        if ($db->transStatus() === false) {
            $session->setFlashdata('error', 'Failed to save purchase. Please try again.');
            return redirect()->to(site_url('purchases/create'));
        }

        $session->setFlashdata('success', 'Purchase saved successfully.');
        return redirect()->to(site_url('purchases'));
    }
    private function handleFileUpload($inputName, $purchaseId, $folder)
    {
        $file = $this->request->getFile($inputName);
        $filePath = '';

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/' . $folder;
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            if ($purchaseId) {
                $existingPurchase = $this->purchaseModel->find($purchaseId);
                if ($existingPurchase && !empty($existingPurchase[$inputName])) {
                    $oldFilePath = FCPATH . 'uploads/' . $folder . '/' . $existingPurchase[$inputName];
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
            }

            $newFileName = $file->getRandomName();
            if ($file->move($uploadPath, $newFileName)) {
                $filePath = $newFileName;
            }
        }

        return $filePath;
    }

    public function showDetails($purchaseId)
    {
        $data = array();
        set_title('Purchase Details | ' . SITE_NAME);
        $data['pageTitle'] = "Purchase Details";

        $data['record'] = $this->purchaseModel->getPurchaseWithCompany($purchaseId);
        $purchaseProductsArray = $this->productMasterModel->getProductsByPurchaseId($purchaseId);
        $purchaseItemsArray = $this->purchaseItemsModel->getPurchaseItemsWithProduct($purchaseId);

        $purchaseProductRecords = $this->productMasterModel
            ->select('product_master.purchase_id, product_master.product_master_id, product_master.parent_master_id, product_master.serial_number, product_master.product_type as master_product_type, products.product_type, products.product_source, products.quantity')
            ->join('products', 'products.product_master_id = product_master.product_master_id', 'left')
            ->where('product_master.product_type', 'purchase')
            ->findAll();

        $inventorySummaryArray = [];

        foreach ($purchaseProductRecords as $productRecord) {
            $currentPurchaseId = $productRecord['purchase_id'];
            $currentParentMasterId = $productRecord['parent_master_id'];

            // Initialize the array structure if not exists
            if (!isset($inventorySummaryArray[$currentPurchaseId][$currentParentMasterId])) {
                $inventorySummaryArray[$currentPurchaseId][$currentParentMasterId] = [
                    'rent' => 0,
                    'sale' => 0,
                    'available' => 0
                ];
            }

            $currentProductType = strtolower($productRecord['product_type']);

            if ($currentProductType == "rent") {
                $inventorySummaryArray[$currentPurchaseId][$currentParentMasterId]["rent"] = $productRecord['quantity'];
            } else if ($currentProductType == "sale") {
                $inventorySummaryArray[$currentPurchaseId][$currentParentMasterId]["sale"] = $productRecord['quantity'];
            } else {
                // Initialize available if not set, then increment
                if (!isset($inventorySummaryArray[$currentPurchaseId][$currentParentMasterId]["available"])) {
                    $inventorySummaryArray[$currentPurchaseId][$currentParentMasterId]["available"] = 0;
                }
                $inventorySummaryArray[$currentPurchaseId][$currentParentMasterId]["available"] += 1;
            }
        }

        $serialNumberArray = [];

        foreach ($purchaseProductsArray as $purchaseProductDetails) {
            $parentMasterId = $purchaseProductDetails['parent_master_id'];

            // Initialize array if it doesn't exist
            if (!isset($serialNumberArray[$parentMasterId])) {
                $serialNumberArray[$parentMasterId] = [
                    'serial_numbers' => []
                ];
            }

            // Append serial number if available
            if (!empty($purchaseProductDetails['serial_number'])) {
                $serialNumberArray[$parentMasterId]['serial_numbers'][] = $purchaseProductDetails['serial_number'];
            }
        }

        $productDetails = [];

        foreach ($purchaseItemsArray as $item) {
            $productId = $item['product_master_id'];
            $serialNumbers = $serialNumberArray[$productId]["serial_numbers"] ?? [];

            $productDetails[$productId] = $item;
            $productDetails[$productId]['serialNumbers'] = $serialNumbers;
        }

        $data['inventorySummaryArray'] = $inventorySummaryArray;
        $data['purchaseItems'] = $productDetails;
        return view('admin/purchase/preview', $data);
    }

    public function delete()
    {
        $purchaseId = $this->request->getPost('purchaseId');

        if (is_numeric($purchaseId) && !empty($purchaseId)) {
            try {
                $purchase = $this->purchaseModel->find($purchaseId);

                if ($purchase) {
                    // Check if the uploaded file exists and delete it
                    if (!empty($purchase['uploaded_receipt'])) {
                        $receiptFilePath = FCPATH . 'uploads/receipts/' . $purchase['uploaded_receipt'];

                        if (file_exists($receiptFilePath)) {
                            unlink($receiptFilePath); // Delete the file
                        }
                    }

                    // Delete all products associated with the given purchase ID
                    $this->productMasterModel->where('purchase_id', $purchaseId)->delete();

                    // Now delete the purchase record
                    $this->purchaseModel->where('purchase_id', $purchaseId)->delete();

                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Purchase deleted successfully, and the uploaded file has been removed.',
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Purchase record not found.',
                    ]);
                }
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again.',
            ]);
        }
    }
}
