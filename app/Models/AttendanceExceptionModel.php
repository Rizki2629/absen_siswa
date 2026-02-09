<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceExceptionModel extends Model
{
    protected $table            = 'attendance_exceptions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'student_id',
        'date',
        'exception_type',
        'check_in_time',
        'check_out_time',
        'notes',
        'proof_file',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get exceptions for a date
     */
    public function getExceptionsForDate($date)
    {
        return $this->select('attendance_exceptions.*, students.name as student_name, students.nis')
            ->join('students', 'students.id = attendance_exceptions.student_id')
            ->where('attendance_exceptions.date', $date)
            ->findAll();
    }

    /**
     * Get student exceptions for date range
     */
    public function getStudentExceptions($studentId, $startDate, $endDate)
    {
        return $this->where('student_id', $studentId)
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->findAll();
    }
}
