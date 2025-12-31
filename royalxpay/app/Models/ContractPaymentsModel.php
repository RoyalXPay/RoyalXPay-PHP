<?php

namespace App\Models;

use CodeIgniter\Model;

class ContractPaymentsModel extends Model
{
    protected $table = 'contract_payments';
    protected $primaryKey = 'payment_id';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['contract_id', 'payment_date', 'final_amount', 'payment_amount', 'balance_amount', 'payment_method','payment_method', 'cheque_date', 'cheque_number','bank_name', 'transaction_id', 'payment_status', 'receipt', 'notes'];

    // Get contract payment details with contract information
    public function getContractPayments($contractId = null, $searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        $builder = $this->db->table('contract_payments');

        $builder->join('contracts', 'contracts.contract_id = contract_payments.contract_id', 'left');

        if ($countOnly) {
            $builder->select("COUNT(contract_payments.payment_id) as total_count");
        } else {
            $builder->select('contract_payments.*, contracts.contract_code, contracts.total_amount as contract_total_amount, contracts.start_date, contracts.end_date, contracts.status as contract_status, contracts.contract_date, contracts.final_payable_amount');
        }

        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('contract_payments.payment_reference', $searchTerm)
                ->orLike('contract_payments.payment_status', $searchTerm)
                ->orLike('contracts.contract_code', $searchTerm)
                ->groupEnd();
        }

        if ($contractId) {
            $builder->where('contract_payments.contract_id', $contractId);
        }

        $builder->orderBy("contract_payments.payment_id", 'DESC');

        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        $query = $builder->get();

        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        return $query->getResult();
    }

    public function getContractLastPayments()
    {
        $subQuery = "(SELECT MAX(payment_id) as latest_payment_id, contract_id FROM contract_payments GROUP BY contract_id) as latest";

        $builder = $this->db->table('contract_payments cp');
        $builder->join($subQuery, 'cp.contract_id = latest.contract_id AND cp.payment_id = latest.latest_payment_id', 'inner');
        $builder->select('cp.*');

        return $builder->get()->getResultArray();
    }
}
