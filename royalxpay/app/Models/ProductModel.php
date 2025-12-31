<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'product_master_id',
        'product_type',
        'product_source',
        'price_per_day',
        'product_price',
        'discount_percentage',
        'final_product_price',
        'start_date',
        'end_date',
        'quantity',
        'available_quantity',
        'created_at',
        'updated_at'
    ];

    /**
     * Method to get products available for rent with optional search, pagination, and count
     *
     * @param array $searchArray The search filters
     * @param string $offset The offset for pagination
     * @param string $limit The limit for pagination
     * @param string $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of products or count of records
     */
    public function getRentProductsDetails($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Initialize the Query Builder
        $builder = $this->db->table($this->table);

        if ($countOnly) {
            $builder->select("COUNT(products.{$this->primaryKey}) as total_count");
        } else {
            // Select columns from the products table
            $builder->select('
                products.*,
                product_master.product_name,
                product_master.serial_number,
                product_master.description,
                product_master.product_images,
                sizes.size_in_inches,
                colors.name as color_name,
                touch_types.name as touch_name,
                glass_types.name as glass_name,
                resolutions.resolution_type
            ');
        }

        // Join with the product_master table to get the details of the product
        $builder->join('product_master', 'product_master.product_master_id = products.product_master_id', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left');

        // Filter by product type (rent)
        $builder->where('products.product_type', 'rent');

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
        $builder->orderBy("products.{$this->primaryKey}", 'DESC');

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

    /**
     * Method to get products available for sale with optional search, pagination, and count
     *
     * @param array $searchArray The search filters
     * @param string $offset The offset for pagination
     * @param string $limit The limit for pagination
     * @param string $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of products or count of records
     */
    public function getSaleProductsDetails($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Initialize the Query Builder
        $builder = $this->db->table($this->table);

        if ($countOnly) {
            $builder->select("COUNT(products.{$this->primaryKey}) as total_count");
        } else {
            // Select columns from the products table
            $builder->select('
                products.*,
                product_master.product_name,
                product_master.serial_number,
                product_master.description,
                product_master.product_images,
                sizes.size_in_inches,
                colors.name as color_name,
                touch_types.name as touch_name,
                glass_types.name as glass_name,
                resolutions.resolution_type,
                product_master.product_type AS master_product_type
            ');
        }

        // Join with the product_master table to get the details of the product
        $builder->join('product_master', 'product_master.product_master_id = products.product_master_id', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left');

        // Filter by product type (sale)
        $builder->where('products.product_type', 'sale');

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
        $builder->orderBy("products.{$this->primaryKey}", 'DESC');

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

    public function getProductsWithDetails($productType = null, $productIds = null)
    {
        // Build the query base
        $builder = $this->db->table('products')
            ->select('
                products.*,
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

            ->join('product_master', 'product_master.product_master_id = products.product_master_id', 'left')
            ->join('sizes', 'sizes.size_id = product_master.size_id', 'left')
            ->join('colors', 'colors.color_id = product_master.color_id', 'left')
            ->join('touch_types', 'touch_types.touch_id = product_master.touch_id', 'left')
            ->join('glass_types', 'glass_types.glass_id = product_master.glass_id', 'left')
            ->join('resolutions', 'resolutions.resolution_id = product_master.resolution_id', 'left');

        // Check if a specific product type ('rent' or 'sale') is provided and filter accordingly
        if ($productType !== null) {
            $builder->where('products.product_type', $productType);
        }

        // Check if product IDs are provided (for searching multiple products)
        if ($productIds !== null) {
            if (!is_array($productIds)) {
                $builder->where('products.product_id', $productIds);
            } else {
                $builder->whereIn('products.product_id', $productIds);
            }
        }
        // If productIds is not passed for single product retrieval, fetch results
        if (!is_array($productIds)) {
            return $builder->get()->getRow();
        } else {
            return $builder->get()->getResult();
        }
    }
}
