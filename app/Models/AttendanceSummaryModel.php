<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceSummaryModel extends Model
{
    protected $table            = 'attendance_summaries';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'student_id',
        'date',
        'check_in_time',
        'check_out_time',
        'status',
        'is_late',
        'late_minutes',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get daily summary
     */
    public function getDailySummary($date)
    {
        return $this->select('attendance_summaries.*, students.name, students.nis, classes.name as class_name')
            ->join('students', 'students.id = attendance_summaries.student_id')
            ->join('classes', 'classes.id = students.class_id')
            ->where('attendance_summaries.date', $date)
            ->findAll();
    }

    /**
     * Get students who haven't checked in
     */
    public function getNotCheckedIn($date)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('students');
        
        return $builder->select('students.id, students.nis, students.name, classes.name as class_name')
            ->join('classes', 'classes.id = students.class_id')
            ->where('students.active', 1)
            ->whereNotIn('students.id', function($builder) use ($date) {
                return $builder->select('student_id')
                    ->from('attendance_summaries')
                    ->where('date', $date)
                    ->where('check_in_time IS NOT NULL');
            })
            ->get()
            ->getResultArray();
    }

    /**
     * Get student attendance summary for date range
     */
    public function getStudentSummary($studentId, $startDate, $endDate)
    {
        return $this->where('student_id', $studentId)
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->orderBy('date', 'DESC')
            ->findAll();
    }

    /**
     * Get attendance statistics
     */
    public function getStatistics($studentId, $startDate, $endDate)
    {
        $data = $this->where('student_id', $studentId)
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->findAll();

        $stats = [
            'total_days' => count($data),
            'hadir' => 0,
            'terlambat' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpha' => 0,
        ];

        foreach ($data as $row) {
            if (isset($stats[$row['status']])) {
                $stats[$row['status']]++;
            }
        }

        return $stats;
    }
}
