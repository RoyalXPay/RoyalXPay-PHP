<?php

namespace App\Models;

use CodeIgniter\Model;

class WalletModel extends Model
{
    protected $table = 'wallet';  // your wallet table name
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'amount', 'Payment_type','transaction_type', 'created_at','customer_id','wallet_by', 'status' ];
    protected $useTimestamps = false;  // change to true if you are using timestamps in CI4

    // You can add your custom functions here if needed
}
