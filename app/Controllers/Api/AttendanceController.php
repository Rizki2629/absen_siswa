<?php

namespace App\Controllers\Api;

use App\Models\AttendanceLogModel;

class AttendanceController extends BaseApiController
{
    public function index()
    {
        $from = $this->request->getGet('from');
        $to   = $this->request->getGet('to');
        $date = $this->request->getGet('date');

        if (($from === null || $from === '') && ($to === null || $to === '') && ($date !== null && $date !== '')) {
            $from = $date . ' 00:00:00';
            $to   = $date . ' 23:59:59';
        }

        $builder = model(AttendanceLogModel::class)->builder();
        $builder
            ->select('attendance_logs.*, students.name as student_name, students.nis as student_nis, classes.name as class_name, devices.sn as device_sn, devices.name as device_name')
            ->join('students', 'students.id = attendance_logs.student_id', 'left')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->join('devices', 'devices.id = attendance_logs.device_id', 'left')
            ->orderBy('attendance_logs.att_time', 'DESC');

        if ($from !== null && $from !== '') {
            $builder->where('attendance_logs.att_time >=', $from);
        }
        if ($to !== null && $to !== '') {
            $builder->where('attendance_logs.att_time <=', $to);
        }

        $logs = $builder->get(500)->getResultArray();

        return $this->respond(['data' => $logs]);
    }

    public function summary()
    {
        $date = $this->request->getGet('date');
        if ($date === null || $date === '') {
            return $this->failValidationErrors('date is required (YYYY-MM-DD)');
        }

        $from = $date . ' 00:00:00';
        $to   = $date . ' 23:59:59';

        $db = db_connect();

        $rows = $db->table('attendance_logs')
            ->select('students.id as student_id, students.name as student_name, students.nis as student_nis, classes.name as class_name, MIN(attendance_logs.att_time) as first_in, MAX(attendance_logs.att_time) as last_out')
            ->join('students', 'students.id = attendance_logs.student_id', 'left')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->where('attendance_logs.att_time >=', $from)
            ->where('attendance_logs.att_time <=', $to)
            ->groupBy('students.id')
            ->orderBy('class_name', 'ASC')
            ->orderBy('student_name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->respond(['date' => $date, 'data' => $rows]);
    }
}
