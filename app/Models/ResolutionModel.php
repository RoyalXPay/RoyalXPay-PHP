<?php

namespace App\Models;

use CodeIgniter\Model;

class ResolutionModel extends Model
{
    protected $table = 'resolutions';
    protected $primaryKey = 'resolution_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['resolution_type', 'created_at', 'updated_at'];

    /**
     * Method to get resolution data with search and pagination.
     */
    public function getResolutions($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        $builder = $this->db->table($this->table . ' as r');

        if ($countOnly) {
            $builder->select("COUNT(r.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('r.*');
        }

        // If there's a search term, apply it to the query
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('r.resolution_type', $searchTerm)
                ->groupEnd();
        }

        // Order by resolution_id (or another field if desired)
        $builder->orderBy("r.{$this->primaryKey}", 'DESC');

        // Apply pagination if needed
        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        // Execute the query
        $query = $builder->get();

        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        return $query->getResult();
    }
}
