<?php

namespace App\Models;

use CodeIgniter\Model;

class BanksModel extends Model
{
    protected $table = 'banks';
    protected $primaryKey = 'bank_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = ['account_holder', 'bank_name', 'account_number', 'ifsc_code', 'branch_name', 'swift_code', 'created_at', 'updated_at'];

    /**
     * Method to get bank details with optional search, pagination, and count
     *
     * @param array $searchArray The search filters
     * @param string $offset The offset for pagination
     * @param string $limit The limit for pagination
     * @param string $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of bank details or count of records
     */
    public function getBanks($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Start building the query
        $builder = $this->db->table($this->table);

        // If only count is required, select the count of records
        if ($countOnly) {
            $builder->select("COUNT(banks.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('banks.*');
        }

        // If search term is provided, filter by bank name, account number, IFSC code, account holder name, or SWIFT code
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('banks.account_holder', $searchTerm)
                ->orLike('banks.bank_name', $searchTerm)
                ->orLike('banks.account_number', $searchTerm)
                ->orLike('banks.ifsc_code', $searchTerm)
                ->orLike('banks.swift_code', $searchTerm)
                ->groupEnd();
        }

        // Order the results by the primary key (bank_id) in descending order
        $builder->orderBy("banks.{$this->primaryKey}", 'DESC');

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
