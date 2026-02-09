<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceLogModel extends Model
{
    protected $table            = 'attendance_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'device_id',
        'student_id',
        'pin',
        'att_time',
        'status',
        'work_code',
        'raw',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // No updated_at field in this table
}
