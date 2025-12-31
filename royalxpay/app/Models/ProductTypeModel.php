<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductTypeModel extends Model
{
    protected $table = 'product_types';
    protected $primaryKey = 'id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['name', 'description', 'created_at', 'updated_at'];

    /**
     * Get product types based on filters.
     *
     * @param array $searchArray Search criteria (optional)
     * @param string $offset Pagination offset (optional)
     * @param string $limit Pagination limit (optional)
     * @param string $countOnly If set, only return the total count (optional)
     * @return mixed
     */
    public function getProductTypes($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Initialize the Query Builder
        $builder = $this->db->table($this->table . ' as t');

        // Select the necessary columns
        if ($countOnly) {
            $builder->select("COUNT(t.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('t.*');
        }

        // Apply search filter if provided
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('t.name', $searchTerm)
                ->orLike('t.description', $searchTerm)
                ->groupEnd();
        }

        // Apply ordering
        $builder->orderBy("t.{$this->primaryKey}", 'DESC');

        // Apply limit and offset if provided
        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        // Execute the query
        $query = $builder->get();

        // Return the count or the results
        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        return $query->getResult();
    }
}
