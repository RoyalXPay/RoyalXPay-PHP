<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table = 'cart';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'product_id', 'quantity', 'price', 'created_at'];
    protected $useTimestamps = false;

    public function getCartCount($userId)
    {
        return $this->where('user_id', $userId)
            ->countAllResults();
    }

    public function getCartTotal($userId)
    {
        $items = $this->where('user_id', $userId)->findAll();
        $total = 0;

        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    public function getUserCart($userId)
    {
        return $this->select('
            cart.*,
            product_master.product_name,
            product_master.description,
            product_master.serial_number,
            product_master.product_images,
            sizes.size_in_inches,
            colors.name as color_name,
            touch_types.name as touch_name,
            glass_types.name as glass_name,
            resolutions.resolution_type
        ')
            ->join('products', 'products.product_id = cart.product_id', 'left')
            ->join('product_master', 'product_master.product_master_id = products.product_master_id', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->where('cart.user_id', $userId)
            ->findAll();
    }

    public function getUserCartWithDetails($userId)
    {
        return $this->db->table('cart')
            ->select('
                cart.*,
                product_master.product_name,
                product_master.description,
                product_master.serial_number,
                product_master.product_images,
                sizes.size_in_inches,
                colors.name as color_name,
                touch_types.name as touch_name,
                glass_types.name as glass_name,
                resolutions.resolution_type,
                products.available_quantity
            ')
            ->join('products', 'products.product_id = cart.product_id', 'left')
            ->join('product_master', 'product_master.product_master_id = products.product_master_id', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->where('cart.user_id', $userId)
            ->get()
            ->getResultArray();
    }


    public function getCartItemWithProduct($id, $userId)
    {
        return $this->db->table('cart')
            ->select('
            cart.*,
            product_master.product_name,
            product_master.description,
            product_master.serial_number,
            product_master.product_images,
            sizes.size_in_inches,
            colors.name as color_name,
            touch_types.name as touch_name,
            glass_types.name as glass_name,
            resolutions.resolution_type,
            products.available_quantity
        ')
            ->join('products', 'products.product_id = cart.product_id', 'left')
            ->join('product_master', 'product_master.product_master_id = products.product_master_id', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->where('cart.cart_id', $id)
            ->where('cart.user_id', $userId)
            ->get()
            ->getRowArray();
    }
}
