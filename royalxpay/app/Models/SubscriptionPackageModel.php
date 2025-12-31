<?php

namespace App\Models;

use CodeIgniter\Model;

class SubscriptionPackageModel extends Model
{
    protected $table = 'subscription_packages';
    protected $primaryKey = 'id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = ['title', 'description', 'price', 'duration', 'image', 'status', 'created_at', 'updated_at'];

    /**
     * Method to get subscription packages with optional search, pagination, and count.
     *
     * @param array $searchArray The search filters
     * @param string $offset The offset for pagination
     * @param string $limit The limit for pagination
     * @param string $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of subscription packages or count of records
     */
    public function getSubscriptionPackages($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Start building the query
        $builder = $this->db->table($this->table . ' as sp');

        // If only count is required, select the count of records
        if ($countOnly) {
            $builder->select("COUNT(sp.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('sp.*');
        }

        // If search term is provided, filter by title, description, or status
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('sp.title', $searchTerm)
                ->orLike('sp.description', $searchTerm)
                ->groupEnd();
        }

        // Filter by status if provided
        if (!empty($searchArray['status'])) {
            $builder->whereIn('sp.status', $searchArray['status']);
        }

        // Order the results by the primary key (id) in descending order
        $builder->orderBy("sp.{$this->primaryKey}", 'DESC');

        // If pagination is required, add limit and offset
        if ($limit !== '' && $offset !== '') {
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
