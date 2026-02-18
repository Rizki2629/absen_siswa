<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table            = 'students';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'nis',
        'nipd',
        'name',
        'class_id',
        'gender',
        'nisn',
        'birth_place',
        'birth_date',
        'nik',
        'religion',
        'parent_phone',
        'phone',
        'address',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'father_name',
        'mother_name',
        'active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
