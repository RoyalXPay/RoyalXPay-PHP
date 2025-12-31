<?php

namespace App\Models;

use CodeIgniter\Model;

class ContractProductsModel extends Model
{
    protected $table         = 'contract_products';
    protected $primaryKey    = 'contract_product_id';
    protected $protectFields = true;
    protected $useAutoIncrement = true;
    protected $allowedFields = ['contract_id', 'product_master_id', 'product_id', 'quantity', 'price', 'discount'];

    public function getContractProductsDetails()
    {
        // Use the model directly to fetch data
        return $this->findAll();
    }

    public function getRentContractProductsDetails($contractId)
    {
        // Fetch quotation details and related information
        return $this->select('
            contract_products.product_id,
            product_master.product_name,
            product_master.description,
            product_master.serial_number,
            product_master.product_images,
            contract_products.quantity,
            contract_products.price,
            sizes.size_in_inches,
            colors.name as color_name,
            touch_types.name as touch_name,
            glass_types.name as glass_name,
            resolutions.resolution_type
        ')
            ->join('products', 'products.product_id = contract_products.product_id', 'left')
            ->join('product_master', 'product_master.product_master_id = products.product_master_id', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->where('contract_products.contract_id', $contractId)
            ->findAll();
    }

    public function rentContractProducts($contractIds)
    {

        // Ensure $contractIds is an array
        if (!is_array($contractIds)) {
            $contractIds = [$contractIds];
        }
        // Fetch quotation details and related information
        return $this->select('
          contract_products.contract_id,
            contract_products.product_id,
            product_master.product_name,
            product_master.description,
            product_master.serial_number,
            contract_products.quantity,
            contract_products.price,
            product_master.product_images, 
            sizes.size_in_inches,
            colors.name as color_name,
            touch_types.name as touch_name,
            glass_types.name as glass_name,
            resolutions.resolution_type
        ')
            ->join('products', 'products.product_id = contract_products.product_id', 'left')
            ->join('product_master', 'product_master.product_master_id = products.product_master_id', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->whereIn('contract_products.contract_id', $contractIds)
            ->findAll();
    }

    public function getSaleContractProductsDetails($contractId)
    {
        return $this->select('
            contract_products.product_id,
            contract_products.product_master_id,
            product_master.product_name, 
            product_master.description,
            product_master.serial_number,
            contract_products.quantity, 
            contract_products.price,
            contract_products.discount, 
            product_master.product_images, 
            sizes.size_in_inches, 
            colors.name as color_name, 
            touch_types.name as touch_name, 
            glass_types.name as glass_name, 
            resolutions.resolution_type
        ')
            ->join('products', 'products.product_id = contract_products.product_id', 'left')
            ->join('product_master', 'product_master.product_master_id = IFNULL(contract_products.product_master_id, products.product_master_id)', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->where('contract_products.contract_id', $contractId)
            ->findAll();
    }

    public function saleContractProducts($contractIds)
    {

        // Ensure $contractIds is an array
        if (!is_array($contractIds)) {
            $contractIds = [$contractIds];
        }
        // Fetch quotation details and related information
        return $this->select('
            contract_products.contract_id,
            contract_products.product_id,
            product_master.product_name,
            product_master.description,
            product_master.serial_number,
            contract_products.quantity,
            contract_products.price,
            product_master.product_images, 
            sizes.size_in_inches,
            colors.name as color_name,
            touch_types.name as touch_name,
            glass_types.name as glass_name,
            resolutions.resolution_type
        ')
            ->join('products', 'products.product_id = contract_products.product_id', 'left')
            ->join('product_master', 'product_master.product_master_id = IFNULL(contract_products.product_master_id, products.product_master_id)', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->whereIn('contract_products.contract_id', $contractIds)
            ->findAll();
    }
}
