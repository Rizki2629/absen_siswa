<?php

namespace App\Models;

use CodeIgniter\Model;

class DeviceUserMapModel extends Model
{
    protected $table            = 'device_user_maps';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'device_id',
        'pin',
        'student_id',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
