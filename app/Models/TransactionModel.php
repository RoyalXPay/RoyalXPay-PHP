<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table      = 'transactions';   // check your DB table name
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'transaction_id', 'account_number','OID', 'Payment_type', 'created_at',
        'commision', 'amount', 'name','status', 'phone','wallet', 'email', 'city_name', 'state_name','customer_mobile'
    ];

public function getFilteredTransactions($filters = [])
{
    $builder = $this->db->table($this->table);

    $builder->select('
        transactions.*,
        users.name ,
        users.phone ,
        users.email ,
        users.wallet ,
        users.city_name ,
        users.state_name 
    ');
    $builder->join('users', 'users.user_id = transactions.customer_name', 'left');

    if (!empty($filters['merchant_id'])) {
        $builder->where('transactions.customer_name', $filters['merchant_id']);
    }
    // filters ...
    if (!empty($filters['start_date'])) {
        $builder->where('DATE(transactions.created_at) >=', $filters['start_date']);
    }
    if (!empty($filters['end_date'])) {
        $builder->where('DATE(transactions.created_at) <=', $filters['end_date']);
    }
    if (!empty($filters['username'])) {
        $builder->like('users.name', $filters['username']);
    }
    if (!empty($filters['mobile'])) {
        $builder->like('transactions.customer_mobile', $filters['mobile']);
    }
    if (!empty($filters['email'])) {
        $builder->like('users.email', $filters['email']);
    }
    if (!empty($filters['merchant'])) {
        $builder->like('users.name', $filters['merchant']);
    }
    if (!empty($filters['txn_id'])) {
        $builder->like('transactions.transaction_id', $filters['txn_id']);
    }
    if (!empty($filters['subscription'])) {
        $builder->like('transactions.subscription_type', $filters['subscription']);
    }
    if (!empty($filters['city'])) {
        $builder->like('users.city_name', $filters['city']);
    }
    if (!empty($filters['location'])) {
        $builder->like('users.state_name', $filters['location']);
    }
    if (!empty($filters['Payment_type'])) {
        $builder->where('transactions.Payment_type', $filters['Payment_type']);
    }
    

    $builder->orderBy('transactions.id', 'DESC');
    return $builder->get()->getResultArray();
}


}
