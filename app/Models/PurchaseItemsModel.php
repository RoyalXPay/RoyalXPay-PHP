<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseItemsModel extends Model
{
    protected $table = 'purchase_items';
    protected $primaryKey = 'purchase_item_id';
    protected $allowedFields = ['purchase_id', 'product_master_id', 'price_per_unit', 'quantity', 'cbm_amount', 'shipment_price'];

    public function getAllPurchaseItemsWithProduct()
    {
        return $this->select('
                purchase_items.*,
                product_master.product_name,
                product_master.serial_number,
                product_master.description,
                product_master.product_price,
                product_master.product_images,
                sizes.size_in_inches,
                colors.name AS color_name,
                touch_types.name AS touch_name,
                glass_types.name AS glass_name,
                resolutions.resolution_type,
                product_master.product_type AS master_product_type
            ')
            // Join with the product_master table
            ->join('product_master', 'product_master.product_master_id = purchase_items.product_master_id', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->findAll();
    }

    public function getPurchaseItemsWithProduct($purchaseId)
    {
        // Select the columns needed from purchase_items and related tables
        return $this->select('
                purchase_items.*,
                product_master.product_name,
                product_master.serial_number,
                product_master.description,
                product_master.product_price,
                product_master.product_images,
                sizes.size_in_inches,
                colors.name AS color_name,
                touch_types.name AS touch_name,
                glass_types.name AS glass_name,
                resolutions.resolution_type,
                product_master.product_type AS master_product_type
            ')
            // Join with the product_master table
            ->join('product_master', 'product_master.product_master_id = purchase_items.product_master_id', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')

            // Apply the where condition for filtering by purchase_id
            ->where('purchase_items.purchase_id', $purchaseId)
            ->findAll();
    }
}
