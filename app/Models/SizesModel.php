<?php

namespace App\Models;

use CodeIgniter\Model;

class SizesModel extends Model
{
    protected $table = 'sizes';
    protected $primaryKey = 'size_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['size_in_inches', 'created_at', 'updated_at'];

    /**
     * Method to get sizes with optional search, pagination, and count
     *
     * @param array $searchArray The search filters
     * @param string $offset The offset for pagination
     * @param string $limit The limit for pagination
     * @param string $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of sizes or count of records
     */
    public function getSizes($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Start building the query
        $builder = $this->db->table($this->table . ' as s');

        // If only count is required, select the count of records
        if ($countOnly) {
            $builder->select("COUNT(s.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('s.*');
        }

        // If search term is provided, filter by size (in inches) or size label
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('s.size_in_inches', $searchTerm)
                ->groupEnd();
        }

        // Order the results by the primary key (size_id) in descending order
        $builder->orderBy("s.{$this->primaryKey}", 'DESC');

        // If pagination is required, add limit and offset
        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        // Execute the query and get the results
        $query = $builder->get();

        // Return the count if requested, otherwise return the results
        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        return $query->getResult();
    }
}
