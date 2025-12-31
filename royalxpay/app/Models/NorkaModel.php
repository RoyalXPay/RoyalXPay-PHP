<?php 
namespace App\Models;

use CodeIgniter\Model;

class NorkaModel extends Model {
    protected $table = 'norka_customers';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'type','first_name','middle_name','last_name','role','dob','gender',
        'mobile','phone','email','password','image','created_at'
    ];
}
