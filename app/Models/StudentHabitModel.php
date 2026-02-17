<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentHabitModel extends Model
{
    protected $table            = 'student_habits';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'student_id',
        'date',
        'bangun_pagi',
        'beribadah',
        'berolahraga',
        'makan_sehat',
        'gemar_belajar',
        'bermasyarakat',
        'tidur_cepat',
        'habit_answers',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Habit column names and labels
     */
    public static function getHabitColumns(): array
    {
        return [
            'bangun_pagi'   => 'Bangun Pagi',
            'beribadah'     => 'Beribadah',
            'berolahraga'   => 'Berolahraga',
            'makan_sehat'   => 'Makan Sehat & Bergizi',
            'gemar_belajar' => 'Gemar Belajar',
            'bermasyarakat' => 'Bermasyarakat',
            'tidur_cepat'   => 'Tidur Cepat',
        ];
    }
}
