<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customer';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'id',
        'phone',
        'email',
        'emirates_id',
        'address',
        'items',
        'quantity',
        'paid_amount',
        'balance_amount',
        'mode_of_payment',
        'password',
        'token',
        'status',
        'created_by',
        'paid_status',
        'created_at',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
}
