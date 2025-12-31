<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyInfoModel extends Model
{
    protected $table = 'company_info';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'citylight_logo', 'citylight_address', 'contact_details', 'citylight_email', 'bank_details', 
        'terms_and_conditions', 'return_policies', 'signature', 'stamp', 'created_at', 'updated_at'
    ];
}
