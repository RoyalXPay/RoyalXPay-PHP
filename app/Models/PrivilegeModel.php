<?php

namespace App\Models;

use CodeIgniter\Model;

class PrivilegeModel extends Model
{
    protected $table      = 'privileges';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'merchant_id','module_id','submodule_id','sub_submodule_id',
        'can_add','can_edit','can_delete','can_view','can_download',
        'created_at','updated_at'
    ];

    public function getAllPrivileges()
    {
        return $this->select('
                privileges.*,
                u.name as merchant_name,
                m.name as module_name,
                sm.name as submodule_name,
                ssm.name as sub_submodule_name
            ')
            ->join('users u', 'u.user_id = privileges.merchant_id', 'left')
            ->join('modules m', 'm.id = privileges.module_id', 'left')
            ->join('submodules sm', 'sm.id = privileges.submodule_id', 'left')
            ->join('subsubmodules ssm', 'ssm.id = privileges.sub_submodule_id', 'left')
            ->findAll();
    }
}
