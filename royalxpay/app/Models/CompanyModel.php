<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'company_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['company_name', 'mobile_number', 'alt_mobile_number', 'email', 'address', 'status', 'created_at', 'updated_at'];

    public function getCompanyDetails($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Initialize the Query Builder
        $builder = $this->db->table($this->table);

        // Select the necessary columns
        if ($countOnly) {
            $builder->select("COUNT(companies.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('companies.*');
        }

        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('companies.company_name', $searchTerm)
                ->orLike('companies.email', $searchTerm)
                ->orLike('companies.mobile_number', $searchTerm)
                ->orLike('companies.alt_mobile_number', $searchTerm)
                ->groupEnd();
        }

        // Apply ordering
        $builder->orderBy("companies.{$this->primaryKey}", 'DESC');

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
