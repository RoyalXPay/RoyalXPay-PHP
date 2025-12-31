<?php

namespace App\Controllers;

use App\Models\WishlistModel;
use CodeIgniter\API\ResponseTrait;

class WishlistController extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        helper(['form', 'url', 'session']);
    }

    public function index()
    {
        $wishlistModel = new WishlistModel();
        $userId = session()->get('user_id') ?? null;

        if (!$userId) {
            return redirect()->to('login')->with('error', 'Please login to view your wishlist');
        }

        $data = [
            'wishlistItems' => $wishlistModel->getUserWishlist($userId)
        ];

        return view('wishlist/index', $data);
    }

    public function add()
    {
        $wishlistModel = new WishlistModel();
        $userId = session()->get('user_id');

        if (!$userId) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please login to add items to wishlist',
                    'loginUrl' => site_url('login')
                ]);
            }
            return redirect()->to('login')->with('error', 'Please login to add items to wishlist');
        }

        $productId = $this->request->getPost('product_id');

        if (!$productId) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Product ID is required'
                ]);
            }
            return redirect()->back()->with('error', 'Product ID is required');
        }

        // Check if already in wishlist
        if ($wishlistModel->isInWishlist($userId, $productId)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Product is already in your wishlist'
                ]);
            }
            return redirect()->back()->with('info', 'Product is already in your wishlist');
        }

        $data = [
            'user_id' => $userId,
            'product_id' => $productId,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $wishlistModel->insert($data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Product added to wishlist successfully',
                'wishlistCount' => $wishlistModel->where('user_id', $userId)->countAllResults()
            ]);
        }

        return redirect()->back()->with('success', 'Product added to wishlist successfully');
    }

    public function remove($id)
    {
        $wishlistModel = new WishlistModel();
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('login')->with('error', 'Please login to manage your wishlist');
        }

        $wishlistModel->where(['id' => $id, 'user_id' => $userId])->delete();

        return redirect()->to('wishlist')->with('success', 'Item removed from wishlist');
    }

    public function moveToCart($id)
    {
        $wishlistModel = new WishlistModel();
        $cartModel = new \App\Models\CartModel();
        $productModel = new \App\Models\ProductModel();
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('login')->with('error', 'Please login to manage your wishlist');
        }

        // Get wishlist item
        $wishlistItem = $wishlistModel->where(['id' => $id, 'user_id' => $userId])->first();

        if (!$wishlistItem) {
            return redirect()->to('wishlist')->with('error', 'Item not found in your wishlist');
        }

        // Get product details
        $product = $productModel->find($wishlistItem['product_id']);

        if (!$product) {
            return redirect()->to('wishlist')->with('error', 'Product not found');
        }

        // Add to cart
        $cartData = [
            'user_id' => $userId,
            'product_id' => $product->product_id,
            'quantity' => 1,
            'price' => $product->price_per_day,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $cartModel->insert($cartData);

        // Remove from wishlist
        $wishlistModel->delete($id);

        return redirect()->to('cart')->with('success', 'Item moved to cart successfully');
    }
}
