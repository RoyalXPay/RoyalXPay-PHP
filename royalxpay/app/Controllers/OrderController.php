<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\UsersModel;
use App\Models\OrderModel;
use App\Models\SizesModel;
use App\Models\ColorsModel;
use App\Models\ProductModel;
use App\Libraries\Pagination;
use App\Models\OrderItemModel;
use App\Models\ResolutionModel;
use App\Models\GlassTypesModel;
use App\Models\TouchTypesModel;
use App\Models\ProductMasterModel;
use App\Controllers\BaseController;

class OrderController extends BaseController
{
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
        set_title('Order List | ' . SITE_NAME);

        $data = [
            'action' => "orders",
            'pageTitle' => "Order List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => []
        ];

        // Initialize models
        $orderModel = new OrderModel();
        $customPagination = new Pagination();

        $searchFields = ['txtsearch'];
        foreach ($searchFields as $field) {
            $value = $this->request->getGet($field);
            if ($value) {
                $data[$field] = $value;
                $data['searchArray'][$field] = $value;
            }
        }

        $data['sizes'] = $this->sizesModel->findAll();
        $data['colors'] = $this->colorsModel->findAll();
        $data['touchTypes'] = $this->touchTypesModel->findAll();
        $data['glassTypes'] = $this->glassTypesModel->findAll();
        $data['resolutions'] = $this->resolutionModel->findAll();

        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 50;
        $totalRecord = $orderModel->getOrdersDetails($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $orderModel->getOrdersDetails($data['searchArray'], $startLimit, $Limit);

        return view('admin/orders/index', $data);
    }

    public function edit($id)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->find($id);
        if ($order) {
            return $this->response->setJSON($order);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Order not found']);
        }
    }

    public function save()
    {
        $validation = \Config\Services::validation();

        // Validate input
        if (!$this->validate([
            'product_master_id' => 'required',
            'rent_or_sale' => 'required',
            'customer_name' => 'required',
            'customer_mobile' => 'required|numeric',
            'quantity' => 'required|integer|min_length[1]',
            'payment_status' => 'required',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        $orderModel = new OrderModel();
        $orderId = $this->request->getPost('order_id');

        // Get existing order details if updating
        $existingOrder = null;
        if ($orderId) {
            $existingOrder = $orderModel->find($orderId);
            if (!$existingOrder) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Order not found for updating.',
                ]);
            }
        }

        // Price and discount calculations
        $price = (float) $this->request->getPost('product_price');
        $discount = (float) $this->request->getPost('discount');
        $finalPrice = $price * (1 - ($discount / 100));

        // Capture the data from the form
        $data = [
            'product_master_id' => $this->request->getPost('product_master_id'),
            'rent_or_sale' => $this->request->getPost('rent_or_sale'),
            'customer_name' => $this->request->getPost('customer_name'),
            'customer_mobile' => $this->request->getPost('customer_mobile'),
            'quantity' => $this->request->getPost('quantity'),
            'product_price' => $this->request->getPost('product_price'),
            'discount' => $this->request->getPost('discount'),
            'final_product_price' => $finalPrice,
            'payment_status' => $this->request->getPost('payment_status'),
            'payment_details' => $this->request->getPost('payment_details'),
        ];

        // Add rent-specific fields if 'Rent' is selected
        if ($this->request->getPost('rent_or_sale') === 'Rent') {
            $data['start_date'] = $this->request->getPost('start_date');
            $data['end_date'] = $this->request->getPost('end_date');
        }

        // Handle receipt file upload (single file expected)
        $uploadedReceipt = $this->request->getFile('uploaded_receipt');
        if ($uploadedReceipt && $uploadedReceipt->isValid()) {
            // If there's an existing file, delete it before uploading the new one
            if ($existingOrder && $existingOrder['uploaded_receipt']) {
                $oldFilePath = FCPATH . 'uploads/receipts/' . $existingOrder['uploaded_receipt'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath); // Delete the old file
                }
            }

            // Move the new uploaded receipt
            $receiptName = time() . '_' . $uploadedReceipt->getRandomName();
            if ($uploadedReceipt->move(FCPATH . 'uploads/receipts', $receiptName)) {
                $data['uploaded_receipt'] = $receiptName;
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => 'Failed to upload the receipt.',
                ]);
            }
        } elseif ($existingOrder && !$uploadedReceipt->isValid()) {
            // If no new file is uploaded and it's an update, keep the old file
            $data['uploaded_receipt'] = $existingOrder['uploaded_receipt'];
        }

        if ($orderId) {
            // Update existing order
            $data['order_id'] = $orderId;
            if ($orderModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Order updated successfully',
                ]);
            }
        } else {
            // Create new order
            if ($orderModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Order added successfully',
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save order. Please try again.',
        ]);
    }

    public function showDetails($orderId)
    {
        $data = array();
        set_title('Order Details | ' . SITE_NAME);

        $data['pageTitle'] = "Order Details";
        $orderModel = new OrderModel();
        $data['record'] = $orderModel
            ->where('order_id', $orderId)
            ->first();

        return view('admin/orders/preview', $data);
    }

    public function viewOrderDetails($orderId)
    {
        // Initialize models
        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemModel();


        // Get order with all customer details (no user verification for admin)
        $order = $orderModel->select('orders.*, users.user_id, users.username, users.name, users.email, users.phone')
            ->join('users', 'users.user_id = orders.user_id', 'left')
            ->where('orders.order_id', $orderId)
            ->first();

        if (!$order) {
            return redirect()->to('admin/orders')->with('error', 'Order not found');
        }

        // Get order items with full product details
        $items = $orderItemModel->select('order_items.*,
                                    product_master.*,
                                    sizes.size_in_inches as size_name,
                                    colors.name as color_name,
                                    touch_types.name as touch_name,
                                    resolutions.resolution_type as resolution_name,
                                    glass_types.name as glass_name')
            ->join('products', 'products.product_id = order_items.product_id', 'left')
            ->join('product_master', 'product_master.product_master_id = products.product_master_id', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->where('order_items.order_id', $orderId)
            ->findAll();

        $data = [
            'title' => 'Order #' . $order['order_id'],
            'order' => $order,
            'items' => $items,
            'customer' => $order,
            'pageTitle' => "Order Details"
        ];


        return view('admin/orders/preview', $data);
    }

    public function delete()
    {
        $orderId = $this->request->getPost('orderId');

        if (is_numeric($orderId) && !empty($orderId)) {
            try {
                $orderModel = new OrderModel();
                $orderModel->where('order_id', $orderId)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Order deleted successfully.',
                ]);
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
