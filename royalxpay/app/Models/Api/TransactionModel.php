<?php

namespace App\Models\Api;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transactions'; // Make sure this matches your actual table name
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'transaction_id',
        'account_number',
        'amount',
        'customer_name',
        'status',
        'Payment_type',
        'customer_mobile',
        'response'
    ];

    protected $useTimestamps = true;
}
