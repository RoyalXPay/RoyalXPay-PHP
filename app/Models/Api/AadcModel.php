<?php

namespace App\Models\Api;

use CodeIgniter\Model;

class AadcModel extends Model
{
    protected $table = 'transactions';
    protected $allowedFields = ['account_no', 'amount', 'user_id', 'response', 'created_at'];
}
