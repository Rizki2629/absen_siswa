<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\DeviceModel;
use App\Models\DeviceUserMapModel;
use App\Models\StudentModel;
use App\Models\ShiftModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseController
{
    protected $deviceModel;
    protected $deviceUserMapModel;
    protected $studentModel;
    protected $shiftModel;

    public function __construct()
    {
        $this->deviceModel = new DeviceModel();
        $this->deviceUserMapModel = new DeviceUserMapModel();
        $this->studentModel = new StudentModel();
        $this->shiftModel = new ShiftModel();
    }

    /**
     * Get all devices
     */
    public function getDevices()
    {
        $devices = $this->deviceModel->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data'    => $devices,
        ]);
    }

    /**
     * Create new device
     */
    public function createDevice()
    {
        $rules = [
            'sn'         => 'required|is_unique[devices.sn]',
            'name'       => 'required',
            'ip_address' => 'required|valid_ip',
            'port'       => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $this->validator->getErrors(),
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $data = [
            'sn'         => $this->request->getPost('sn'),
            'name'       => $this->request->getPost('name'),
            'ip_address' => $this->request->getPost('ip_address'),
            'port'       => $this->request->getPost('port'),
            'comm_key'   => $this->request->getPost('comm_key'),
            'location'   => $this->request->getPost('location'),
            'push_url'   => $this->request->getPost('push_url'),
            'status'     => 'offline',
        ];

        $deviceId = $this->deviceModel->insert($data);

        if (!$deviceId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create device',
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Device created successfully',
            'data'    => $this->deviceModel->find($deviceId),
        ])->setStatusCode(ResponseInterface::HTTP_CREATED);
    }

    /**
     * Update device
     */
    public function updateDevice($id)
    {
        $device = $this->deviceModel->find($id);
        
        if (!$device) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Device not found',
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        $data = $this->request->getJSON(true);

        if ($this->deviceModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Device updated successfully',
                'data'    => $this->deviceModel->find($id),
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update device',
        ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Delete device
     */
    public function deleteDevice($id)
    {
        $device = $this->deviceModel->find($id);
        
        if (!$device) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Device not found',
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        if ($this->deviceModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Device deleted successfully',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete device',
        ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test device connection
     */
    public function testDeviceConnection($id)
    {
        $device = $this->deviceModel->find($id);
        
        if (!$device) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Device not found',
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        // Simple ping test
        $connection = @fsockopen($device['ip_address'], $device['port'], $errno, $errstr, 5);
        
        $isOnline = $connection !== false;
        
        if ($connection) {
            fclose($connection);
        }

        // Update device status
        $this->deviceModel->update($id, [
            'status'       => $isOnline ? 'online' : 'offline',
            'last_seen_at' => $isOnline ? date('Y-m-d H:i:s') : null,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'data'    => [
                'online'  => $isOnline,
                'message' => $isOnline ? 'Device is online' : 'Device is offline',
            ],
        ]);
    }

    /**
     * Get device user mappings
     */
    public function getDeviceUserMaps($deviceId = null)
    {
        $query = $this->deviceUserMapModel
            ->select('device_user_maps.*, students.nis, students.name as student_name, devices.name as device_name')
            ->join('students', 'students.id = device_user_maps.student_id', 'left')
            ->join('devices', 'devices.id = device_user_maps.device_id');

        if ($deviceId) {
            $query->where('device_user_maps.device_id', $deviceId);
        }

        $mappings = $query->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data'    => $mappings,
        ]);
    }

    /**
     * Create device user mapping
     */
    public function createDeviceUserMap()
    {
        $rules = [
            'device_id'  => 'required|integer',
            'pin'        => 'required',
            'student_id' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $this->validator->getErrors(),
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $data = [
            'device_id'  => $this->request->getPost('device_id'),
            'pin'        => $this->request->getPost('pin'),
            'student_id' => $this->request->getPost('student_id'),
        ];

        // Check if mapping already exists
        $existing = $this->deviceUserMapModel
            ->where('device_id', $data['device_id'])
            ->where('pin', $data['pin'])
            ->first();

        if ($existing) {
            // Update existing mapping
            $this->deviceUserMapModel->update($existing['id'], $data);
            $mappingId = $existing['id'];
        } else {
            // Create new mapping
            $mappingId = $this->deviceUserMapModel->insert($data);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Mapping created successfully',
            'data'    => $this->deviceUserMapModel->find($mappingId),
        ]);
    }

    /**
     * Delete device user mapping
     */
    public function deleteDeviceUserMap($id)
    {
        if ($this->deviceUserMapModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Mapping deleted successfully',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete mapping',
        ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Get all shifts
     */
    public function getShifts()
    {
        $shifts = $this->shiftModel->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data'    => $shifts,
        ]);
    }

    /**
     * Create shift
     */
    public function createShift()
    {
        $rules = [
            'name'            => 'required',
            'check_in_start'  => 'required',
            'check_in_end'    => 'required',
            'check_out_start' => 'required',
            'check_out_end'   => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $this->validator->getErrors(),
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $data = [
            'name'            => $this->request->getPost('name'),
            'check_in_start'  => $this->request->getPost('check_in_start'),
            'check_in_end'    => $this->request->getPost('check_in_end'),
            'check_out_start' => $this->request->getPost('check_out_start'),
            'check_out_end'   => $this->request->getPost('check_out_end'),
            'late_tolerance'  => $this->request->getPost('late_tolerance') ?? 0,
            'is_active'       => $this->request->getPost('is_active') ?? 1,
        ];

        $shiftId = $this->shiftModel->insert($data);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Shift created successfully',
            'data'    => $this->shiftModel->find($shiftId),
        ])->setStatusCode(ResponseInterface::HTTP_CREATED);
    }

    /**
     * Update shift
     */
    public function updateShift($id)
    {
        $shift = $this->shiftModel->find($id);
        
        if (!$shift) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Shift not found',
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        $data = $this->request->getJSON(true);

        if ($this->shiftModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Shift updated successfully',
                'data'    => $this->shiftModel->find($id),
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update shift',
        ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Delete shift
     */
    public function deleteShift($id)
    {
        if ($this->shiftModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Shift deleted successfully',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete shift',
        ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }
}
