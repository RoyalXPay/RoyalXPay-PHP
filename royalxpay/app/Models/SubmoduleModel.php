<?php
namespace App\Models;

use CodeIgniter\Model;

class SubmoduleModel extends Model
{
    protected $table = 'submodules';
    protected $primaryKey = 'id';
    protected $allowedFields = ['module_id','name','slug'];
}
