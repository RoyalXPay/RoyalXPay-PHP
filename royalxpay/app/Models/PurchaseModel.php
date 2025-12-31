<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseModel extends Model
{
    protected $table = 'purchases';
    protected $primaryKey = 'purchase_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'company_id',
        'export_wooden_case',
        'send_to_warehouse',
        'total_amount',
        'was_china_paid',
        'payment_date',
        'payment_method',
        'payment_status',
        'uploaded_receipt',
        'notes',
        'purchase_status',
        'agent_price',
        'agent_cbm',
        'agent_payment_method',
        'agent_payment_status',
        'agent_attachment',
        'agent_notes',
        'agent_status',
        'created_at',
        'updated_at'
    ];

    /**
     * Method to get purchase details with optional search, pagination, and count
     *
     * @param array $searchArray The search filters
     * @param string $offset The offset for pagination
     * @param string $limit The limit for pagination
     * @param string $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of purchases or count of records
     */
    public function getPurchaseDetails($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Initialize the Query Builder
        $builder = $this->db->table('purchases');

        // Select the necessary columns
        if ($countOnly) {
            // Only select the count of records
            $builder->select("COUNT(purchases.purchase_id) as total_count");
        } else {
            // Select columns from purchases table and related details from the companies table
            $builder->select('purchases.*, companies.company_name');
        }

        // Join with the companies table to get the details of the company
        $builder->join('companies', 'companies.company_id = purchases.company_id', 'left');

        // Check if there is a search term and apply it to relevant fields in both tables
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('purchases.payment_status', $searchTerm)
                ->orLike('companies.company_name', $searchTerm)
                ->orLike('companies.email', $searchTerm)
                ->groupEnd();
        }

        // Apply ordering by the primary key (purchase_id) in descending order
        $builder->orderBy('purchases.purchase_id', 'DESC');

        // Apply pagination if limit and offset are provided
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

    // Get a specific purchase with company details
    public function getPurchaseWithCompany($purchaseId)
    {
        return $this->select('purchases.*, companies.company_name')
            ->join('companies', 'companies.company_id = purchases.company_id', 'left')
            ->where('purchases.purchase_id', $purchaseId)
            ->first();
    }
}
