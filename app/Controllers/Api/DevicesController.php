<?php

namespace App\Controllers\Api;

use App\Models\DeviceModel;
use App\Models\DeviceUserMapModel;
use App\Models\StudentModel;
use CodeIgniter\Database\BaseConnection;

class DevicesController extends BaseApiController
{
    public function index()
    {
        $devices = model(DeviceModel::class)->orderBy('last_seen_at', 'DESC')->findAll();

        return $this->respond(['data' => $devices]);
    }

    public function linkStudent()
    {
        $data = $this->getJsonBody();

        $rules = [
            'device_sn'  => 'required|string',
            'pin'        => 'required|string',
            'student_id' => 'required|is_natural_no_zero',
        ];

        if (! $this->validateData($data, $rules)) {
            return $this->respondValidationErrors($this->validator->getErrors());
        }

        $deviceModel = model(DeviceModel::class);
        $device      = $deviceModel->where('sn', $data['device_sn'])->first();

        if ($device === null) {
            return $this->failNotFound('Device not found. Pastikan device sudah pernah connect ke endpoint iClock.');
        }

        $student = model(StudentModel::class)->find((int) $data['student_id']);
        if ($student === null) {
            return $this->failNotFound('Student not found');
        }

        $mapModel = model(DeviceUserMapModel::class);

        $existing = $mapModel->where('device_id', $device['id'])->where('pin', $data['pin'])->first();

        if ($existing === null) {
            $mapModel->insert([
                'device_id'  => $device['id'],
                'pin'        => (string) $data['pin'],
                'student_id' => (int) $data['student_id'],
            ]);
        } else {
            $mapModel->update($existing['id'], [
                'student_id' => (int) $data['student_id'],
            ]);
        }

        /** @var BaseConnection $db */
        $db = db_connect();
        $db->table('attendance_logs')
            ->where('device_id', (int) $device['id'])
            ->where('pin', (string) $data['pin'])
            ->where('student_id IS NULL', null, false)
            ->update(['student_id' => (int) $data['student_id']]);

        return $this->respond(['ok' => true]);
    }
}
