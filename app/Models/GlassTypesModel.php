<?php

namespace App\Models;

use CodeIgniter\Model;

class GlassTypesModel extends Model
{
    protected $table = 'glass_types';
    protected $primaryKey = 'glass_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = ['name', 'description', 'created_at', 'updated_at'];

    /**
     * Get Glass Types based on filters.
     *
     * @param array $searchArray Search criteria (optional)
     * @param string $offset Pagination offset (optional)
     * @param string $limit Pagination limit (optional)
     * @param string $countOnly If set, only return the total count (optional)
     * @return mixed
     */
    public function getGlassTypes($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Initialize the Query Builder
        $builder = $this->db->table($this->table . ' as g');

        // Select the necessary columns
        if ($countOnly) {
            $builder->select("COUNT(g.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('g.*');
        }

        // Apply search filter if provided
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('g.name', $searchTerm)
                ->orLike('g.description', $searchTerm)
                ->groupEnd();
        }

        // Apply ordering (latest first by default)
        $builder->orderBy("g.{$this->primaryKey}", 'DESC');

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
