<?php

namespace App\Models;

use CodeIgniter\Model;

class DeletedQuotation extends Model
{
    protected $table      = 'deleted_quotations';
    protected $primaryKey = 'id';
    protected $allowedFields = ['quotation_code', 'created_at'];
}
