<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'order_item_id';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = ['order_id', 'product_id', 'product_name', 'quantity', 'price'];

    /**
     * Get items for a specific order
     */
    public function getItemsByOrderId($orderId)
    {
        return $this->where('order_id', $orderId)->findAll();
    }

    /**
     * Get best selling products
     */
    public function getBestSellingProducts($limit = 5)
    {
        return $this->select('product_id, product_name, SUM(quantity) as total_sold')
            ->groupBy('product_id, product_name')
            ->orderBy('total_sold', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get items with product details
     */
    public function getItemsWithProducts($orderId)
    {
        return $this->select('order_items.*, products.product_name, products.slug, products.image')
            ->join('products', 'products.product_id = order_items.product_id', 'left')
            ->where('order_items.order_id', $orderId)
            ->findAll();
    }
}
