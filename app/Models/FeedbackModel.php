<?php

namespace App\Models;

use CodeIgniter\Model;

class FeedbackModel extends Model
{
    protected $table  = 'feedbacks';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $allowedFields = ['customer_name', 'email', 'phone', 'feedback', 'rating', 'status', 'created_at', 'updated_at'];

    public function getFeedbacksDetails($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Initialize the Query Builder
        $builder = $this->db->table($this->table . ' as t');

        // Select the necessary columns
        if ($countOnly) {
            $builder->select("COUNT(t.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('t.*');
        }

        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('t.customer_name', $searchTerm)
                ->orLike('t.email', $searchTerm)
                ->orLike('t.phone', $searchTerm)
                ->groupEnd();
        }

        // Apply ordering
        $builder->orderBy("t.{$this->primaryKey}", 'DESC');

        // Limit the results if limit and offset are provided
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
