<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\ProductModel;
use CodeIgniter\API\ResponseTrait;

class CartController extends BaseController
{
    use ResponseTrait;

    protected $cartModel;
    protected $productModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        if (!session()->has('user_id')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'login_required',
                    'message' => 'Please login to view your cart'
                ]);
            }

            return view('frontend/cart', [
                'total' => 0,
                'cartCount' => 0,
                'cartItems' => [],
                'showLoginModal' => true
            ]);
        }

        $userId = session()->get('user_id');
        $cartItems = $this->cartModel->getUserCartWithDetails($userId);

        $data = [
            'total' => $this->cartModel->getCartTotal($userId),
            'cartCount' => count($cartItems),
            'cartItems' => $cartItems,
            'showLoginModal' => false
        ];

        return view('frontend/cart', $data);
    }

    public function add()
    {
        if (!session()->has('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to add items to cart',
                'login_url' => site_url('login')
            ]);
        }

        $userId = session()->get('user_id');
        $productId = $this->request->getPost('product_id');
        $quantity = $this->request->getPost('quantity');

        // Validate input
        $rules = [
            'product_id' => 'required|numeric|is_not_unique[products.product_id]',
            'quantity' => 'required|numeric|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Check product stock
        $product = $this->productModel->find($productId);
        if (!$product || $product['available_quantity'] < $quantity) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Insufficient stock available'
            ]);
        }

        // Check if product already in cart
        $existingItem = $this->cartModel->where([
            'user_id' => $userId,
            'product_id' => $productId
        ])->first();

        $price = $product['product_type'] == "sale"
            ? $product['product_price']
            : $product['price_per_day'];

        try {
            if ($existingItem) {
                // Update quantity if exists (ensure it doesn't exceed stock)
                $newQuantity = $existingItem['quantity'] + $quantity;
                if ($newQuantity > $product['available_quantity']) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Requested quantity exceeds available stock'
                    ]);
                }

                $this->cartModel->update($existingItem['id'], [
                    'quantity' => $newQuantity,
                    'price' => $price
                ]);
            } else {
                // Add new item
                $this->cartModel->insert([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $price,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Return updated cart count and total
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cartCount' => $this->cartModel->getCartCount($userId),
                'cartTotal' => number_format($this->cartModel->getCartTotal($userId), 2)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Cart add error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating your cart'
            ]);
        }
    }

    public function update($id)
    {
        if (!session()->has('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'status' => 'unauthorized',
                'message' => 'Please login to update cart',
                'login_url' => site_url('login')
            ])->setStatusCode(401);
        }

        $userId = session()->get('user_id');
        $quantity = $this->request->getPost('quantity');

        // Validation rules
        $rules = [
            'quantity' => [
                'label' => 'Quantity',
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'greater_than' => 'Quantity must be at least 1'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'status' => 'validation_error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
                'error_summary' => $this->validator->getError('quantity')
            ])->setStatusCode(422);
        }

        // Get cart item with product details
        $cartItem = $this->cartModel->getCartItemWithProduct($id, $userId);

        if (!$cartItem) {
            return $this->response->setJSON([
                'success' => false,
                'status' => 'not_found',
                'message' => 'Cart item not found or doesn\'t belong to you'
            ])->setStatusCode(404);
        }

        // Check stock availability
        if ($quantity > $cartItem['available_quantity']) {
            return $this->response->setJSON([
                'success' => false,
                'status' => 'insufficient_stock',
                'message' => 'Only ' . $cartItem['available_quantity'] . ' items available',
                'maxAllowed' => $cartItem['available_quantity'],
                'currentQuantity' => $cartItem['quantity']
            ])->setStatusCode(400);
        }

        try {
            $this->cartModel->where(['cart_id' => $id, 'user_id' => $userId])
                ->set(['quantity' => $quantity])
                ->update();

            // Get updated cart totals
            $itemTotal = $quantity * $cartItem['price'];
            $cartTotal = $this->cartModel->getCartTotal($userId);
            $cartCount = $this->cartModel->getCartCount($userId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cart updated successfully',
                'data' => [
                    'itemTotal' => number_format($itemTotal, 2),
                    'itemTotalRaw' => $itemTotal,
                    'cartTotal' => number_format($cartTotal, 2),
                    'cartTotalRaw' => $cartTotal,
                    'cartCount' => $cartCount,
                    'newQuantity' => $quantity
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Cart update error - User: ' . $userId . ' - ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'status' => 'server_error',
                'message' => 'An error occurred while updating your cart',
                'error_details' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ])->setStatusCode(500);
        }
    }

    public function remove($id)
    {
        if (!session()->has('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to modify cart',
                'login_url' => site_url('login')
            ]);
        }

        $userId = session()->get('user_id');

        try {
            $deleted = $this->cartModel->where(['cart_id' => $id, 'user_id' => $userId])->delete();

            if (!$deleted) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Item not found in your cart'
                ]);
            }

            // Get updated cart data
            $cartCount = $this->cartModel->getCartCount($userId);
            $cartTotal = $this->cartModel->getCartTotal($userId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Item removed from cart',
                'cartCount' => $cartCount,
                'reload' => ($cartCount == 0),
                'subtotal' => number_format($cartTotal, 2),
                'cartTotal' => number_format($cartTotal, 2),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Cart remove error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while removing item from cart'
            ]);
        }
    }
}
