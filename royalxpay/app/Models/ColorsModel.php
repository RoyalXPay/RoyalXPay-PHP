<?php

namespace App\Models;

use CodeIgniter\Model;

class ColorsModel extends Model
{
    protected $table = 'colors';
    protected $primaryKey = 'color_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['name', 'description', 'created_at', 'updated_at'];

    /**
     * Method to get color types with optional search, pagination, and count
     *
     * @param array $searchArray The search filters
     * @param string $offset The offset for pagination
     * @param string $limit The limit for pagination
     * @param string $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of colors or count of records
     */
    public function getColors($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Start building the query
        $builder = $this->db->table($this->table);

        // If only count is required, select the count of records
        if ($countOnly) {
            $builder->select("COUNT(colors.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('colors.*');
        }

        // If search term is provided, filter by name or description
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('colors.name', $searchTerm)
                ->orLike('colors.description', $searchTerm)
                ->groupEnd();
        }

        // Order the results by the primary key (color_id) in descending order
        $builder->orderBy("colors.{$this->primaryKey}", 'DESC');

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
