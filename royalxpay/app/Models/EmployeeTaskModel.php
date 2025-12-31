<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeTaskModel extends Model
{
    protected $table = 'employee_tasks';
    protected $primaryKey = 'task_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'employee_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'assigned_by',
        'completed_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Method to get employee tasks with optional search, pagination, and count
     *
     * @param array $searchArray The search filters
     * @param string $offset The offset for pagination
     * @param string $limit The limit for pagination
     * @param string $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of tasks or count of records
     */
    public function getTasks($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        $builder = $this->db->table($this->table);

        if ($countOnly) {
            $builder->select("COUNT({$this->table}.{$this->primaryKey}) as total_count");
        } else {
            $builder->select("
            {$this->table}.*,
            CONCAT(employees.first_name, ' ', employees.last_name) AS employee_name,
            CONCAT(assigners.first_name, ' ', assigners.last_name) AS assigner_name
        ");
            $builder->join('employees', 'employees.employee_id = employee_tasks.employee_id', 'left');
            $builder->join('employees as assigners', 'assigners.employee_id = employee_tasks.assigned_by', 'left');
        }

        // Search filter
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like("{$this->table}.title", $searchTerm)
                ->orLike("{$this->table}.description", $searchTerm)
                ->orLike("employees.first_name", $searchTerm)
                ->orLike("employees.last_name", $searchTerm)
                ->groupEnd();
        }

        // Filter by employee ID
        if (!empty($searchArray['employee_id'])) {
            $builder->where("{$this->table}.employee_id", $searchArray['employee_id']);
        }

        // Filter by status
        if (!empty($searchArray['status'])) {
            $builder->where("{$this->table}.status", $searchArray['status']);
        }

        // Filter by priority
        if (!empty($searchArray['priority'])) {
            $builder->where("{$this->table}.priority", $searchArray['priority']);
        }

        // Order by priority and due date
        $builder->orderBy("{$this->table}.priority", 'ASC');
        $builder->orderBy("{$this->table}.due_date", 'ASC');

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

    /**
     * Get task statistics for dashboard
     */
    public function getTaskStatistics($employee_id = null)
    {
        $builder = $this->db->table($this->table);

        $builder->select("
            COUNT(*) as total_tasks,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_tasks,
            SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tasks,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_tasks,
            SUM(CASE WHEN due_date < CURDATE() AND status != 'completed' THEN 1 ELSE 0 END) as overdue_tasks
        ");

        if ($employee_id) {
            $builder->where('employee_id', $employee_id);
        }

        return $builder->get()->getRow();
    }

    /**
     * Update task status
     */
    public function updateTaskStatus($task_id, $status)
    {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($status == 'completed') {
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        return $this->update($task_id, $data);
    }
}
