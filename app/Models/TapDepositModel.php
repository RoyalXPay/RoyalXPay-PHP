<?php

namespace App\Models;

use CodeIgniter\Model;

class TapDepositModel extends Model
{
    protected $table = 'tap_deposits';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'event_id',
        'event_type',
        'transaction_id',
        'external_reference',
        'user_id',
        'wallet_id',
        'wallet_number',
        'amount',
        'currency',
        'status',
        'error_message',
        'processed_by',
        'raw_payload',
        'processed_at',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'event_id' => 'required|max_length[255]',
        'event_type' => 'required|max_length[100]',
        'transaction_id' => 'required|max_length[255]',
        'amount' => 'required|decimal',
        'currency' => 'required|max_length[10]',
        'status' => 'required|in_list[completed,failed,pending]',
        'processed_by' => 'required|max_length[50]'
    ];

    protected $validationMessages = [
        'event_id' => [
            'required' => 'Event ID is required'
        ],
        'status' => [
            'in_list' => 'Status must be one of: completed, failed, pending'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Check if an event already exists (idempotency check)
     */
    public function eventExists($eventId)
    {
        if (empty($eventId)) {
            return false;
        }
        
        return $this->where('external_reference', $eventId)
            ->orWhere('event_id', $eventId)
            ->countAllResults() > 0;
    }

    /**
     * Get deposits by status
     */
    public function getByStatus($status, $limit = 50)
    {
        return $this->where('status', $status)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get deposits by wallet
     */
    public function getByWallet($walletId, $limit = 50)
    {
        return $this->where('wallet_id', $walletId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get deposit statistics
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $builder = $this->builder();

        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }

        if ($endDate) {
            $builder->where('created_at <=', $endDate);
        }

        $result = $builder->select('
            status,
            COUNT(*) as total_count,
            SUM(amount) as total_amount,
            currency
        ')
        ->groupBy(['status', 'currency'])
        ->get()
        ->getResultArray();

        return $result;
    }

    /**
     * Get failed deposits for retry
     */
    public function getFailedDeposits($limit = 10)
    {
        return $this->where('status', 'failed')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
