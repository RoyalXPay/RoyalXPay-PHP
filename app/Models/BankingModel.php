<?php

namespace App\Models;

use CodeIgniter\Model;

class BankingModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'amount', 'transaction_type', 'status', 'created_at'];
    protected $useTimestamps = false;

    /**
     * Get all transactions for a specific user
     */
    public function getTransactions($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get filtered transactions with optional search filters
     */
public function getFilteredTransactions($filters = [], $userId = null)
{
    $builder = $this->db->table($this->table . ' t');
    $builder->select('t.*, u.username AS user_name, u.email, u.phone'); // Include what you need
    $builder->join('users u', 'u.user_id = t.user_id', 'left');

    if (!empty($userId)) {
        $builder->where('t.user_id', $userId);
    }

    // Filter by date range
    if (!empty($filters['startDate'])) {
        $builder->where('t.created_at >=', $filters['startDate'] . ' 00:00:00');
    }

    if (!empty($filters['endDate'])) {
        $builder->where('t.created_at <=', $filters['endDate'] . ' 23:59:59');
    }

    // Optional search
    if (!empty($filters['txtsearch'])) {
        $builder->groupStart()
            ->like('t.transaction_type', $filters['txtsearch'])
            ->orLike('t.status', $filters['txtsearch'])
            ->orLike('u.username', $filters['txtsearch'])
            ->orLike('u.email', $filters['txtsearch'])
            ->orLike('u.phone', $filters['txtsearch'])
            ->groupEnd();
    }

    $builder->orderBy('t.created_at', 'DESC');

    return $builder->get()->getResult();
}


}
