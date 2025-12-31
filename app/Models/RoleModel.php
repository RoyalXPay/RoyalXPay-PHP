<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'role_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = ['role_name','merchant_id', 'description', 'role_permission', 'created_at', 'updated_at'];

    /**
     * Method to get role details with optional search, pagination, and count
     *
     * @param array $searchArray The search filters
     * @param string $offset The offset for pagination
     * @param string $limit The limit for pagination
     * @param string $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of role details or count of records
     */
    public function getRoles($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Start building the query
        $builder = $this->db->table($this->table);

        // If only count is required, select the count of records
        if ($countOnly) {
            $builder->select("COUNT(roles.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('roles.*');
        }

        // If search term is provided, filter by role name
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('roles.role_name', $searchTerm)
                ->groupEnd();
        }

        // Order the results by the primary key (role_id) in descending order
        $builder->orderBy("roles.{$this->primaryKey}", 'DESC');

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
