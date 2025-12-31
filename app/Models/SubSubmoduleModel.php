<?php
namespace App\Models;

use CodeIgniter\Model;

class SubSubmoduleModel extends Model
{
    protected $table = 'subsubmodules';
    protected $primaryKey = 'id';
    protected $allowedFields = ['submodule_id','name','slug'];
}
