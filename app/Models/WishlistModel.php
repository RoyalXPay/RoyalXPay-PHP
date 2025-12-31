<?php

namespace App\Models;

use CodeIgniter\Model;

class WishlistModel extends Model
{
    protected $table = 'wishlist';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'product_id', 'created_at'];
    protected $useTimestamps = false;

    public function getUserWishlist($userId)
    {
        return $this->select('wishlist.*, products.product_name, products.product_images, products.price_per_day')
            ->join('products', 'products.product_id = wishlist.product_id')
            ->where('wishlist.user_id', $userId)
            ->findAll();
    }

    public function isInWishlist($userId, $productId)
    {
        return $this->where([
            'user_id' => $userId,
            'product_id' => $productId
        ])->countAllResults() > 0;
    }
}
