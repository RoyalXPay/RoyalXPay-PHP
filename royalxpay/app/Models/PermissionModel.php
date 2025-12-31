<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['role_id', 'module_id', 'submodule_id', 'permission_type'];

    public function getPermissionsDetails(array $searchArray = [], string $offset = '', string $limit = '', string $countOnly = '')
{
    // Base SQL Query
    if ($countOnly) {
        $sql = "SELECT COUNT(permissions.{$this->primaryKey}) as total_count FROM {$this->table} AS permissions ";
    } else {
        $sql = "SELECT
            permissions.*, 
            roles.role_name,
            modules.module_name, 
            GROUP_CONCAT(submodules.submodule_name ORDER BY submodules.submodule_id) AS submodule_name
        FROM {$this->table} AS permissions ";
    }

    // Joins
    $sql .= "LEFT JOIN roles ON roles.role_id = permissions.role_id ";
    $sql .= "LEFT JOIN modules ON modules.module_id = permissions.module_id ";
    $sql .= "LEFT JOIN submodules ON FIND_IN_SET(submodules.submodule_id, permissions.submodule_id) ";

    $whereConditions = [];

    // Filters
    if (!empty($searchArray)) {
        if (!empty($searchArray['role_id'])) {
            $roleId = $this->db->escapeString($searchArray['role_id']);
            $whereConditions[] = "permissions.role_id = '$roleId'";
        }
        if (!empty($searchArray['module_id'])) {
            $moduleId = $this->db->escapeString($searchArray['module_id']);
            $whereConditions[] = "permissions.module_id = '$moduleId'";
        }
        if (!empty($searchArray['submodule_id'])) {
            $submoduleId = $this->db->escapeString($searchArray['submodule_id']);
            $whereConditions[] = "FIND_IN_SET('$submoduleId', permissions.submodule_id)";
        }
        if (!empty($searchArray['permission_type'])) {
            $permissionType = $this->db->escapeString($searchArray['permission_type']);
            $whereConditions[] = "permissions.permission_type = '$permissionType'";
        }
    }

    if (!empty($whereConditions)) {
        $sql .= "WHERE " . implode(' AND ', $whereConditions) . " ";
    }

    // Group by permission ID (needed because of GROUP_CONCAT)
    if (!$countOnly) {
        $sql .= "GROUP BY permissions.{$this->primaryKey} ";
    }

    $sql .= "ORDER BY permissions.{$this->primaryKey} DESC ";

    if ($limit && $offset !== '') {
        $sql .= "LIMIT $offset, $limit ";
    }

    $query = $this->db->query($sql);
    $result = $query->getResult();

    if ($countOnly) {
        return empty($result) ? 0 : $result[0]->total_count;
    }

    return $result;
}


    /**
     * Check if a role has a specific permission for a module and submodule.
     *
     * @param int $role_id
     * @param int $module_id
     * @param int $submodule_id
     * @param string $type ('view', 'add', 'edit', 'delete')
     * @return bool
     */
    public function hasPermission($role_id, $module_id, $submodule_id, $type)
    {
        return $this->where([
            'role_id'       => $role_id,
            'module_id'     => $module_id,
            'submodule_id'  => $submodule_id,
            'permission_type' => $type
        ])->countAllResults() > 0;
    }

    /**
     * Get all permissions for a specific role
     *
     * @param int $role_id
     * @return array
     */
    public function getPermissionsByRole($role_id)
    {
        return $this->where('role_id', $role_id)->findAll();
    }

    /**
     * Assign a new permission to a role
     *
     * @param int $role_id
     * @param int $module_id
     * @param int $submodule_id
     * @param string $type
     * @return bool
     */
    public function addPermission($role_id, $module_id, $submodule_id, $type)
    {
        return $this->insert([
            'role_id'       => $role_id,
            'module_id'     => $module_id,
            'submodule_id'  => $submodule_id,
            'permission_type' => $type
        ]);
    }

    /**
     * Remove a permission from a role
     *
     * @param int $role_id
     * @param int $module_id
     * @param int $submodule_id
     * @param string $type
     * @return bool
     */
    public function removePermission($role_id, $module_id, $submodule_id, $type)
    {
        return $this->where([
            'role_id'       => $role_id,
            'module_id'     => $module_id,
            'submodule_id'  => $submodule_id,
            'permission_type' => $type
        ])->delete();
    }
}
