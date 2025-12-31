<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\UsersModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\OrderItemModel;
use App\Controllers\BaseController;

class CheckoutController extends BaseController
{
    protected $cartModel;
    protected $usersModel;
    protected $productModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->usersModel = new UsersModel();
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        set_title('Checkout | ' . SITE_NAME);

        // 1. Get the logged-in user's ID
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('cart')->with('error', 'Please login to proceed to checkout');
        }

        // 2. Get cart items for this user
        $userDetails = $this->usersModel->find($userId);
        $cartItems = $this->cartModel->getUserCartWithDetails($userId);

        if (empty($cartItems)) {
            return redirect()->back()->with('error', 'Your cart is empty');
        }

        // 3. Handle shipping method (from URL parameter or session)
        $shippingOption = $this->request->getGet('ship');
        $shippingOption = base64_decode($shippingOption);

        $shippingCost = 0;

        // Calculate shipping cost based on method
        switch ($shippingOption) {
            case 'local':
                $shippingOption = 'Local Pickup';
                break;
            case 'flat':
                $shippingCost = 10.00;
                $shippingOption = 'Flat Rate';
                break;
            default:
                $shippingCost = 0;
                $shippingOption = 'Free Shipping';
        }

        // 4. Calculate totals
        $subtotal = $this->cartModel->getCartTotal($userId);
        $total = $subtotal + $shippingCost;

        // 5. Pass all data to view
        $data = [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shippingCost' => $shippingCost,
            'total' => $total,
            'userDetails' => $userDetails
        ];

        return view('frontend/checkout', $data);
    }

    public function placeOrder()
    {
        $userId = session()->get('user_id');

        // Validate form
        if (!$this->validate([
            'country' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'pincode' => 'required|numeric'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get cart items
        $cartModel = new CartModel();
        $cartItems = $cartModel->getUserCart($userId);

        if (empty($cartItems)) {
            return redirect()->back()->with('error', 'Your cart is empty');
        }

        // Calculate totals
        $subtotal = array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $cartItems));

        $shippingCost = 10.00;
        $total = $subtotal + $shippingCost;

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create order
            $orderModel = new OrderModel();
            $orderData = [
                'user_id' => $userId,
                'company' => $this->request->getPost('company'),
                'country' => $this->request->getPost('country'),
                'address' => $this->request->getPost('address'),
                'city' => $this->request->getPost('city'),
                'state' => $this->request->getPost('state'),
                'pincode' => $this->request->getPost('pincode'),
                'phone' => $this->request->getPost('phone'),
                'order_notes' => $this->request->getPost('order_notes'),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'status' => 'pending'
            ];

            $orderId = $orderModel->insert($orderData);
            
            // Add order items
            $orderItemModel = new OrderItemModel();
            $orderItems = array_map(function ($item) use ($orderId) {
                return [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
            }, $cartItems);

            $orderItemModel->insertBatch($orderItems);

            // Clear cart
            $cartModel->where('user_id', $userId)->delete();

            // Commit transaction
            $db->transComplete();

            return redirect()->to('checkout/success/' . $orderId)
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Order placement failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Order failed. Please try again.');
        }
    }

    public function success($orderId)
    {
        $userId = session()->get('user_id');

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);

        // Check if order exists and belongs to the current user
        if (!$order || $order['user_id'] != $userId) {
            return redirect()->to('/')->with('error', 'Invalid order');
        }

        $orderItemModel = new OrderItemModel();
        $items = $orderItemModel->where('order_id', $orderId)->findAll();

        return view('frontend/checkout_success', [
            'order' => $order,
            'items' => $items
        ]);
    }
}
