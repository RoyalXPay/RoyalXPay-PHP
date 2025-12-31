<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'employee_id';
protected $useAutoIncrement = true;
    protected $allowedFields = [
        'company_id',
        'user_id',
        'employee_code',
        'first_name',
        'last_name',
        'phone',
        'email',
        'dob',
        'gender',
        'designation',
        'department',
        'address',
         'geo_tracking',
        'joining_date',
        'profile_image',
        'status',
        'is_online',
        'last_active',
        'created_by',
        'created_at',
        'updated_at',
        'manager_reporting',
        'login_role',
         'access_role',
         'otp_expire_time',
         'otp',
         'created_by_type',
         'approved_at',
         'approved_by',
    ];

    /**
     * Method to get employee records with optional search, pagination, and count
     *
     * @param array $searchArray The search filters
     * @param int|string $offset The offset for pagination
     * @param int|string $limit The limit for pagination
     * @param bool $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of employees or count of records
     */
    public function getEmployees($searchArray = [], $offset = '', $limit = '', $countOnly = false)
    {
        $builder = $this->db->table($this->table . ' e');

        if ($countOnly) {
            $builder->select("COUNT(e.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('e.*, CONCAT(m.first_name, " ", m.last_name) as manager_name, c.name');
        }

        // Join with companies to get company name
        $builder->join('users c', 'c.user_id = e.user_id', 'left');

        // Join with reporting_managers to get manager_id
        $builder->join('reporting_managers rm', 'rm.employee_id = e.employee_id', 'left');

        // Join with employees again to get manager details
        $builder->join('employees m', 'rm.manager_id = m.employee_id', 'left');

        // Search by text (name, email, phone)
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like("CONCAT(e.first_name, ' ', e.last_name)", $searchTerm)
                ->orLike("e.email", $searchTerm)
                ->orLike("e.phone", $searchTerm)
                ->groupEnd();
        }

        // companies Filter
        if (!empty($searchArray['user_id'])) {
            $builder->where("e.user_id", (int)$searchArray['user_id']);
        }

        // Manager Filter
        if (!empty($searchArray['manager'])) {
            $builder->where("rm.manager_id", (int)$searchArray['manager']);
        }

        // Status Filter
        if (isset($searchArray['status']) && $searchArray['status'] !== '') {
            $builder->where("e.status", $searchArray['status']);
            
        }
        // ✅ Only approved employees
// Role-based approved filter
if (!empty($searchArray['role'])) {
    if ($searchArray['role'] === 'maker') {
        $builder->where('e.status', 'Active');
        $builder->where('e.approved_by IS NOT NULL');
    }
    // Checker can see all employees; no filter needed
}
        // Order by employee_id
        $builder->orderBy("e.{$this->primaryKey}", 'DESC');

        // Pagination
        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        // Execute the query and get the results
        $query = $builder->get();

        // Return the count if requested, otherwise return the results
        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        return $query->getResult();
    }

    public function getManagerCandidates(array $searchArray = [])
    {
        $builder = $this->select('employee_id, first_name, last_name, email, designation, department')
            ->orderBy('first_name', 'ASC');

        if (!empty($searchArray['employeeId'])) {
            $builder->where('employee_id !=', $searchArray['employeeId']);
        }

        if (!empty($searchArray['companyId'])) {
            $builder->where('user_id', $searchArray['companyId']);
        }

        return $builder->findAll();
    }
}
