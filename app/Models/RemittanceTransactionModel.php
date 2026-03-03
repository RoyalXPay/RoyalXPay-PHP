<?php

namespace App\Models;

use CodeIgniter\Model;

class RemittanceTransactionModel extends Model
{
    protected $table = 'remittance_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'org_id', 'process_id', 'biller_code', 'channel', 'signature', 'notification_no',
        'reference_id', 'type', 'account', 'amount', 'remarks', 'principal_ref_id',
        'trx_ref_no', 'status', 'transaction_date', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function createTransaction($data)
    {
        $existing = $this->where('reference_id', $data['data']['referenceId'])->first();
        if ($existing) {
            return [
                'success' => false,
                'message' => 'Duplicate reference ID'
            ];
        }
        
        $trxRefNo = $this->generateTrxRefNo();
        
        $transactionData = [
            'org_id' => $data['orgId'],
            'process_id' => $data['processId'],
            'biller_code' => $data['billerCode'],
            'channel' => $data['channel'],
            'signature' => $data['signature'] ?? null,
            'notification_no' => $data['notificationNo'] ?? null,
            'reference_id' => $data['data']['referenceId'],
            'type' => $data['data']['type'],
            'account' => $data['data']['account'],
            'amount' => $data['data']['amount'],
            'remarks' => $data['data']['remarks'],
            'principal_ref_id' => $data['data']['principalRefId'] ?? '',
            'trx_ref_no' => $trxRefNo,
            'status' => 'COMPLETED',
            'transaction_date' => date('Y-m-d H:i:s')
        ];
        
        $this->insert($transactionData);
        $transactionId = $this->getInsertID();
        
        return [
            'success' => true,
            'trx_ref_no' => $trxRefNo,
            'transaction_id' => $transactionId
        ];
    }
    
    public function getTransactionByRefId($refId)
    {
        return $this->where('reference_id', $refId)->first();
    }
    
    /**
     * Get transaction with sender info
     */
    public function getTransactionWithSenderInfo($refId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('remittance_transactions.*, remittance_sender_info.*');
        $builder->join('remittance_sender_info', 'remittance_sender_info.remittance_transaction_id = remittance_transactions.id', 'left');
        $builder->where('remittance_transactions.reference_id', $refId);
        
        return $builder->get()->getRowArray();
    }
    
    private function generateTrxRefNo()
    {
        do {
            $trxRefNo = 'FCDP' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 11));
            $exists = $this->where('trx_ref_no', $trxRefNo)->first();
        } while ($exists);
        
        return $trxRefNo;
    }
    
    public function getFilteredRemittanceTransactions($filters = [])
    {
        $builder = $this->db->table($this->table);
        
        $builder->select('remittance_transactions.*, remittance_wallets.name as wallet_name');
        $builder->join('remittance_wallets', 'remittance_wallets.wallet_number = remittance_transactions.account', 'left');
        
        if (!empty($filters['start_date'])) {
            $builder->where('DATE(remittance_transactions.created_at) >=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $builder->where('DATE(remittance_transactions.created_at) <=', $filters['end_date']);
        }
        if (!empty($filters['wallet_number'])) {
            $builder->like('remittance_transactions.account', $filters['wallet_number']);
        }
        if (!empty($filters['trx_ref_no'])) {
            $builder->like('remittance_transactions.trx_ref_no', $filters['trx_ref_no']);
        }
        if (!empty($filters['reference_id'])) {
            $builder->like('remittance_transactions.reference_id', $filters['reference_id']);
        }
        if (!empty($filters['type'])) {
            $builder->where('remittance_transactions.type', $filters['type']);
        }
        
        $builder->orderBy('remittance_transactions.id', 'DESC');
        return $builder->get()->getResultArray();
    }
}
