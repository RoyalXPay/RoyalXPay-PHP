<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductMasterModel extends Model
{
    protected $table = 'product_master';
    protected $primaryKey = 'product_master_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'purchase_id',
        'product_type',
        'parent_master_id',
        'product_name',
        'description',
        'product_price',
        'serial_number',
        'discount_percentage',
        'final_product_price',
        'size_id',
        'color_id',
        'touch_id',
        'glass_id',
        'resolution_id',
        'product_images',
        'additional_information',
        'is_used',
        'created_at',
        'updated_at'
    ];

    /**
     * Method to get product_master with optional search, pagination, and count
     *
     * @param array $searchArray The search filters
     * @param string $offset The offset for pagination
     * @param string $limit The limit for pagination
     * @param string $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of product_master or count of records
     */
    public function getProducts(array $searchArray = [], string $offset = '', string $limit = '', string $countOnly = '')
    {
        $builder = $this->db->table('product_master');

        // If count is needed, select count
        if ($countOnly) {
            $builder->select("COUNT(product_master.product_master_id) as total_count");
        } else {
            $builder->select('product_master.*,
                sizes.size_in_inches,
                colors.name AS color_name,
                touch_types.name AS touch_name,
                glass_types.name AS glass_name,
                resolutions.resolution_type,
                product_master.product_type AS master_product_type
            ');
        }

        $builder->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->where('product_master.product_type !=', 'purchase');

        // Applying search filters
        if (!empty($searchArray)) {
            if (!empty($searchArray['txtsearch'])) {
                $builder->groupStart()
                    ->like('product_master.product_name', $searchArray['txtsearch'])
                    ->orLike('product_master.product_price', $searchArray['txtsearch'])
                    ->groupEnd();
            }
            if (!empty($searchArray['size'])) {
                $builder->where('sizes.size_id', $searchArray['size']);
            }
            if (!empty($searchArray['color'])) {
                $builder->where('colors.color_id', $searchArray['color']);
            }
            if (!empty($searchArray['touch_type'])) {
                $builder->where('touch_types.touch_id', $searchArray['touch_type']);
            }
            if (!empty($searchArray['glass_type'])) {
                $builder->where('glass_types.glass_id', $searchArray['glass_type']);
            }
            if (!empty($searchArray['resolution_type'])) {
                $builder->where('resolutions.resolution_id', $searchArray['resolution_type']);
            }
        }

        // Apply ordering by the primary key in descending order
        $builder->orderBy("product_master.{$this->primaryKey}", 'DESC');

        // Pagination
        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        // // Show the generated SQL query in a readable format
        // $sql = $builder->getCompiledSelect();
        // echo "<pre>Generated Query:\n" . $sql . "</pre>";

        // Execute query
        $query = $builder->get();
        if ($countOnly) {
            return $query->getRow()->total_count ?? 0;
        }

        return $query->getResult();
    }

    // Get all product_master with detailed information
    public function getAllProductWithDetails()
    {
        return $this->select('
            product_master.*,
            sizes.size_in_inches,
            colors.name AS color_name,
            touch_types.name AS touch_name,
            glass_types.name AS glass_name,
            resolutions.resolution_type
        ')

            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->where('product_master.product_type !=', 'purchase')
            ->findAll();
    }

    // Get product with detailed information by product ID
    public function getProductWithDetails($productId)
    {
        return $this->select('
            product_master.*,
            sizes.size_in_inches,
            colors.name AS color_name,
            touch_types.name AS touch_name,
            glass_types.name AS glass_name,
            resolutions.resolution_type
        ')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->where('product_master.product_type !=', 'purchase')
            ->where('product_master.product_master_id', $productId)
            ->first();
    }

    // Search product_master based on given product IDs
    public function searchProducts($productIds)
    {
        if (!is_array($productIds)) {
            $productIds = [$productIds];
        }

        return $this->db->table('product_master')
            ->select('
            product_master.*,
            sizes.size_in_inches,
            colors.name AS color_name,
            touch_types.name AS touch_name,
            glass_types.name AS glass_name,
            resolutions.resolution_type
        ')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->where('product_master.product_type !=', 'purchase')
            ->whereIn('product_master.product_master_id', $productIds)
            ->get()
            ->getResult();
    }

    /** Get all records for a given purchase_id. */
    public function getProductsByPurchaseId($purchaseId)
    {
        return $this->where('purchase_id', $purchaseId)->findAll();
    }

    /** Get a specific product record by purchase_id and product_master_id. */
    public function getProductByPurchaseIdAndMasterId($purchaseId, $productMasterId)
    {
        return $this->where('purchase_id', $purchaseId)
            ->where('product_master_id', $productMasterId)
            ->where('product_master.product_type', 'purchase')
            ->first();
    }

    public function getPurchaseProductsDetails($productMasterId = null)
    {
        $this->select('
            product_master.*,
            sizes.size_in_inches,
            colors.name AS color_name,
            touch_types.name AS touch_name,
            glass_types.name AS glass_name,
            resolutions.resolution_type,
            product_master.product_type as master_product_type
        ')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->where('product_master.product_type', 'purchase');

        // Apply condition for is_used
        if ($productMasterId) {
            // Show all used products (is_used = 0) + include the given productMasterId even if is_used = 1
            $this->groupStart()
                ->where('product_master.is_used', 0)
                ->orWhere('product_master.product_master_id', $productMasterId)
                ->groupEnd();
        } else {
            // Default behavior: Only fetch products where is_used = 1
            $this->where('product_master.is_used', 0);
        }

        return $this->findAll();
    }
}
