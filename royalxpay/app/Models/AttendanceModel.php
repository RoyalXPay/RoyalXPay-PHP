<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table = 'attendance';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['employee_id', 'attendance_date', 'in_time', 'out_time', 'total_work_hours', 'status', 'remarks', 'created_at', 'updated_at'];

    /**
     * Save or update attendance records for an employee.
     *
     * @param int $employeeId
     * @param array $attendanceData Array of ['YYYY-MM-DD' => 'status'] pairs
     * @return void
     */
    public function saveAttendance(int $employeeId, array $attendanceData): void
    {
        foreach ($attendanceData as $date => $status) {
            // Check if attendance record exists for employee and date
            $existing = $this->where('employee_id', $employeeId)
                ->where('attendance_date', $date)
                ->first();

            $data = [
                'employee_id' => $employeeId,
                'attendance_date' => $date,
                'status' => $status,
            ];

            if ($existing) {
                // Update existing record
                $this->update($existing['id'], $data);
            } else {
                // Insert new record
                $this->insert($data);
            }
        }
    }
}
