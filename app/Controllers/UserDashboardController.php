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

class UserDashboardController extends BaseController
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
        set_title('Dashboard | ' . SITE_NAME);

        // Instantiate your models
        $orderModel = new OrderModel();
        $data['orderCount'] = $orderModel->countAll();

        return view('frontend/dashboard', $data);
    }

    public function orders()
    {
        // Get current user ID from session
        $userId = session()->get('user_id');

        $data = [
            'action' => "user/orders",
            'pageTitle' => "Order List",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => ['user_id' => $userId]
        ];

        // Initialize models
        $orderModel = new OrderModel();
        $customPagination = new Pagination();
        $orderItemModel = new OrderItemModel();

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

        return view('frontend/my_orders', $data);
    }

    public function viewOrderDetails($orderId)
    {
        // Get current user ID from session
        $userId = session()->get('user_id');

        // Initialize models
        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemModel();

        // Get order with user verification and join customer details
        $order = $orderModel->select('orders.*, users.name, users.email, users.phone')
            ->join('users', 'users.user_id = orders.user_id', 'left')
            ->where('orders.order_id', $orderId)
            ->where('orders.user_id', $userId)
            ->first();

        if (!$order) {
            return redirect()->to('user/orders')->with('error', 'Order not found');
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
            'items' => $items
        ];

        return view('frontend/order_details', $data);
    }
}
