<?php

namespace App\Models;

use CodeIgniter\Model;

class ContractDeliveryModel extends Model
{
    protected $table = 'contract_delivery';
    protected $primaryKey = 'delivery_id';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['contract_id', 'delivery_date', 'delivery_agent', 'delivery_status', 'image', 'signature', 'notes'];

    // Get contract deliveries with optional filtering, pagination, and count
    public function getContractDeliveries($contractId = null, $searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Initialize the Query Builder
        $builder = $this->db->table('contract_delivery');

        // Join contracts table to get related contract information
        $builder->join('contracts', 'contracts.contract_id = contract_delivery.contract_id', 'left');

        // Select columns based on count or full data
        if ($countOnly) {
            $builder->select("COUNT(contract_delivery.delivery_id) as total_count");
        } else {
            $builder->select('contract_delivery.*, contracts.contract_code, contracts.total_amount as contract_total_amount, contracts.start_date, contracts.end_date, contracts.status as contract_status, contracts.contract_date, contracts.final_payable_amount');
        }

        // Apply search filter if provided
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('contract_delivery.delivery_agent', $searchTerm)
                ->orLike('contract_delivery.notes', $searchTerm)
                ->orLike('contracts.contract_code', $searchTerm)
                ->groupEnd();
        }

        // Filter by contract ID if provided
        if ($contractId) {
            $builder->where('contract_delivery.contract_id', $contractId);
        }

        // Order results by delivery_id in descending order
        $builder->orderBy("contract_delivery.delivery_id", 'DESC');

        // Apply pagination if provided
        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        // Execute the query
        $query = $builder->get();

        // Return either count or results
        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        return $query->getResult();
    }

    public function getLastDelivery()
    {
        $subQuery = "(SELECT MAX(delivery_id) as latest_delivery_id, contract_id FROM contract_delivery GROUP BY contract_id) as latest";

        $builder = $this->db->table('contract_delivery cd');
        $builder->join($subQuery, 'cd.contract_id = latest.contract_id AND cd.delivery_id = latest.latest_delivery_id', 'inner');
        $builder->select('cd.*');

        return $builder->get()->getResultArray();
    }
}
