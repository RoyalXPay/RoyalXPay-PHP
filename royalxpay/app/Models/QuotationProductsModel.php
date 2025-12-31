<?php

namespace App\Models;

use CodeIgniter\Model;

class QuotationProductsModel extends Model
{
    protected $table         = 'quotation_products';
    protected $primaryKey    = 'quotation_product_id';
    protected $protectFields = true;
    protected $useAutoIncrement = true;
    protected $allowedFields = ['quotation_id', 'product_master_id', 'product_id', 'quantity', 'price', 'discount'];

    public function getQuotationProductsDetails()
    {
        // Use the model directly to fetch data
        return $this->findAll();
    }

    public function getRentQuotationProductsDetails($quotationId)
    {
        // Fetch quotation details and related information
        return $this->select('
            quotation_products.product_id,
            product_master.product_name,
            product_master.description,
            product_master.serial_number,
            quotation_products.quantity,
            quotation_products.price,
            product_master.product_images,
            sizes.size_in_inches,
            colors.name as color_name,
            touch_types.name as touch_name,
            glass_types.name as glass_name,
            resolutions.resolution_type,
            product_master.product_type AS master_product_type
        ')
            ->join('products', 'products.product_id = quotation_products.product_id', 'left')
            ->join('product_master', 'product_master.product_master_id = products.product_master_id', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->where('quotation_products.quotation_id', $quotationId)
            ->findAll();
    }

    public function getRentQuotationProducts($quotationIds)
    {
        // Ensure $quotationIds is an array
        if (!is_array($quotationIds)) {
            $quotationIds = [$quotationIds];
        }

        // Fetch quotation details and related information for multiple quotations
        return $this->select('
            quotation_products.quotation_id,
            quotation_products.product_id,
            product_master.product_name,
            product_master.description,
            product_master.serial_number,
            quotation_products.quantity,
            quotation_products.price,
            product_master.product_images,
            sizes.size_in_inches,
            colors.name as color_name,
            touch_types.name as touch_name,
            glass_types.name as glass_name,
            resolutions.resolution_type,
            product_master.product_type AS master_product_type
        ')
            ->join('products', 'products.product_id = quotation_products.product_id', 'left')
            ->join('product_master', 'product_master.product_master_id = products.product_master_id', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->whereIn('quotation_products.quotation_id', $quotationIds)
            ->findAll();
    }

    public function getSaleQuotationProductsDetails($quotationId)
    {
        return $this->select('
            quotation_products.product_master_id,
            quotation_products.product_id,
            product_master.product_name,
            product_master.description,
            product_master.serial_number,
            quotation_products.quantity,
            quotation_products.price,
            quotation_products.discount,
            product_master.product_images,
            sizes.size_in_inches,
            colors.name as color_name,
            touch_types.name as touch_name,
            glass_types.name as glass_name,
            resolutions.resolution_type,
            product_master.product_type AS master_product_type
        ')
            ->join('products', 'products.product_id = quotation_products.product_id', 'left')
            ->join('product_master', 'product_master.product_master_id = IFNULL(quotation_products.product_master_id, products.product_master_id)', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->where('quotation_products.quotation_id', $quotationId)
            ->findAll();
    }

    public function getSaleQuotationProducts($quotationIds)
    {
        // Ensure $quotationIds is an array
        if (!is_array($quotationIds)) {
            $quotationIds = [$quotationIds];
        }

        // Fetch quotation details and related information for multiple quotations
        return $this->select('
            quotation_products.quotation_id,
            quotation_products.product_id,
            product_master.product_name,
            product_master.description,
            product_master.serial_number,
            quotation_products.quantity,
            quotation_products.price,
            product_master.product_images,
            sizes.size_in_inches,
            colors.name as color_name,
            touch_types.name as touch_name,
            glass_types.name as glass_name,
            resolutions.resolution_type,
            product_master.product_type AS master_product_type
        ')
            ->join('products', 'products.product_id = quotation_products.product_id', 'left')
            ->join('product_master', 'product_master.product_master_id = IFNULL(quotation_products.product_master_id, products.product_master_id)', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->whereIn('quotation_products.quotation_id', $quotationIds)
            ->findAll();
    }
}
