<?php

namespace App\Models;

use CodeIgniter\Model;

class ShiftModel extends Model
{
    protected $table            = 'shifts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'name',
        'check_in_start',
        'check_in_end',
        'check_out_start',
        'check_out_end',
        'late_tolerance',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get active shift
     */
    public function getActiveShift()
    {
        return $this->where('is_active', 1)->first();
    }
}
