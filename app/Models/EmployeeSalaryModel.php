<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeSalaryModel extends Model
{
    protected $table = 'employee_salaries';
    protected $primaryKey = 'salary_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = ['employee_id', 'month', 'year', 'basic_salary', 'allowances', 'deductions', 'net_salary', 'remarks', 'created_at', 'updated_at'];

    /**
     * Get all salaries with employee names
     *
     * @param array $searchParams Optional search filters
     * @param int $limit Pagination limit
     * @param int $offset Pagination offset
     * @param bool $countOnly If true, return total count instead of results
     * @return array|int
     */
    public function getSalariesWithEmployees(array $searchParams = [], int $limit = 0, int $offset = 0, bool $countOnly = false)
    {
        // 1) Build subquery to get the latest salary_id for each employee
        $sub = $this->db
            ->table($this->table)
            ->select('MAX(salary_id) AS salary_id')
            ->groupBy('employee_id');

        // 2) Main query: join the subquery back to employee_salaries (aliased as es)
        $builder = $this->db
            ->table("({$sub->getCompiledSelect()}) AS latest")
            ->select('es.*, e.first_name, e.last_name, e.email, e.phone')
            ->join("{$this->table} es", 'es.salary_id = latest.salary_id', 'inner')
            ->join('employees e', 'e.employee_id = es.employee_id', 'inner');

        // 3) Apply your optional search filters
        if (!empty($searchParams['employee_id'])) {
            $builder->where('es.employee_id', $searchParams['employee_id']);
        }
        if (!empty($searchParams['month'])) {
            $builder->where('es.month', $searchParams['month']);
        }
        if (!empty($searchParams['year'])) {
            $builder->where('es.year', $searchParams['year']);
        }

        // 4) Count-only case: return the total number of matching employees
        if ($countOnly) {
            // countAllResults() ignores any limit() you set below
            return $builder->countAllResults();
        }

        // 5) Ordering & Pagination
        $builder->orderBy('es.salary_id', 'DESC');
        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        // 6) Execute & return
        return $builder->get()->getResult();
    }
}
