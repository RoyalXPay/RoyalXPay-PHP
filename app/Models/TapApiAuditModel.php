<?php

namespace App\Models;

use CodeIgniter\Model;

class TapApiAuditModel extends Model
{
    protected $table = 'tap_api_audit';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'endpoint',
        'request_method',
        'request_ip',
        'request_payload',
        'transaction_id',
        'external_reference',
        'from_date',
        'to_date',
        'status_filter',
        'response_status',
        'response_message',
        'records_returned',
        'total_records',
        'execution_time',
        'error_message',
        'user_agent',
        'created_at'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Log an API request/response for audit
     * 
     * @param array $data Audit data
     * @return bool
     */
    public function logApiCall(array $data)
    {
        // Ensure created_at is set
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        // Truncate large payloads
        if (isset($data['request_payload']) && strlen($data['request_payload']) > 5000) {
            $data['request_payload'] = substr($data['request_payload'], 0, 4997) . '...';
        }

        return $this->insert($data) !== false;
    }

    /**
     * Get audit logs by endpoint
     * 
     * @param string $endpoint
     * @param int $limit
     * @return array
     */
    public function getByEndpoint(string $endpoint, int $limit = 100)
    {
        return $this->where('endpoint', $endpoint)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get failed API calls
     * 
     * @param int $limit
     * @return array
     */
    public function getFailedCalls(int $limit = 100)
    {
        return $this->whereIn('response_status', [400, 401, 404, 500])
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get audit logs by transaction ID
     * 
     * @param string $transactionId
     * @return array
     */
    public function getByTransactionId(string $transactionId)
    {
        return $this->where('transaction_id', $transactionId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get API call statistics
     * 
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
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
            endpoint,
            response_status,
            COUNT(*) as call_count,
            AVG(execution_time) as avg_execution_time,
            MAX(execution_time) as max_execution_time,
            SUM(records_returned) as total_records_returned
        ')
        ->groupBy(['endpoint', 'response_status'])
        ->get()
        ->getResultArray();

        return $result;
    }

    /**
     * Clean old audit logs (older than specified days)
     * 
     * @param int $daysToKeep
     * @return int Number of deleted records
     */
    public function cleanOldLogs(int $daysToKeep = 90)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysToKeep} days"));
        
        return $this->where('created_at <', $cutoffDate)->delete();
    }
}
