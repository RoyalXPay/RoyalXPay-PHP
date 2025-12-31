<?php

namespace App\Models;

use CodeIgniter\Model;

class DeletedContract extends Model
{
    protected $table      = 'deleted_contracts';
    protected $primaryKey = 'id';
    protected $allowedFields = ['contract_code', 'created_at'];
}
