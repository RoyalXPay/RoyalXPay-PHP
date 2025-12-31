<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = ['user_id', 'product_id', 'company', 'country', 'address', 'city', 'state', 'pincode', 'order_notes', 'subtotal', 'shipping_cost', 'total', 'status'];

    public function getOrdersDetails($searchArray = [], $offset = '', $limit = '', $countOnly = false)
    {
        $builder = $this->db->table('orders');

        if ($countOnly) {
            $builder->select("COUNT(orders.order_id) as total_count");
        } else {
            $builder->select('orders.*, users.username, users.name, users.email');
        }

        $builder->join('users', 'users.user_id = orders.user_id', 'left');

        // Add user_id filter if provided
        if (!empty($searchArray['user_id'])) {
            $builder->where('orders.user_id', $searchArray['user_id']);
        }

        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('users.username', $searchTerm)
                ->orLike('users.email', $searchTerm)
                ->orLike('orders.total', $searchTerm)
                ->orLike('orders.status', $searchTerm)
                ->groupEnd();
        }

        if (!empty($searchArray['status'])) {
            $builder->where('orders.status', $searchArray['status']);
        }

        $builder->orderBy("orders.order_id", 'DESC');

        if ($limit !== '' && $offset !== '') {
            $builder->limit($limit, $offset);
        }

        $query = $builder->get();

        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        $orders = $query->getResult();

        // Now fetch items for each order
        if (!empty($orders)) {
            $orderIds = array_column($orders, 'order_id');
            $itemsBuilder = $this->db->table('order_items')
                ->select('order_items.*, products.*, product_master.*')
                ->join('products', 'products.product_id = order_items.product_id', 'left')
                ->join('product_master', 'product_master.product_master_id = products.product_master_id', 'left')
                ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
                ->join('colors', 'colors.color_id = product_master.color_id', 'left')
                ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
                ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
                ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
                ->whereIn('order_items.order_id', $orderIds)
                ->orderBy('order_items.order_id', 'DESC');

            $items = $itemsBuilder->get()->getResult();

            // Group items by order_id
            $groupedItems = [];
            foreach ($items as $item) {
                $groupedItems[$item->order_id][] = $item;
            }

            // Attach items to orders
            foreach ($orders as $order) {
                $order->items = $groupedItems[$order->order_id] ?? [];
            }
        }

        return $orders;
    }
}
