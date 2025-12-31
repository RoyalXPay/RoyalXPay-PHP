<?php namespace App\Models;

use CodeIgniter\Model;

class MerchantPrivilegesModel extends Model
{
    protected $table = 'merchant_privileges';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'merchant_id', 'module', 'submodule', 'action',
        'can_add', 'can_edit', 'can_delete', 'can_view', 'created_at'
    ];
}
