<?php

namespace App\Models;

use CodeIgniter\Model;

class SchoolHolidayModel extends Model
{
    protected $table = 'school_holidays';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['date', 'name', 'type', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get holidays for a specific month/year
     */
    public function getHolidaysByMonth(int $year, int $month): array
    {
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        return $this->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->orderBy('date', 'ASC')
            ->findAll();
    }

    /**
     * Toggle holiday for a date
     */
    public function toggleHoliday(string $date, string $name, string $type = 'school'): array
    {
        $existing = $this->where('date', $date)->where('type', 'school')->first();

        if ($existing) {
            $this->delete($existing['id']);
            return ['action' => 'deleted', 'id' => $existing['id']];
        } else {
            $id = $this->insert([
                'date' => $date,
                'name' => $name,
                'type' => $type,
            ]);
            return ['action' => 'created', 'id' => $id];
        }
    }
}
