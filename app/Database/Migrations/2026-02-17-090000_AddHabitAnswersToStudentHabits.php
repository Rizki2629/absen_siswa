<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHabitAnswersToStudentHabits extends Migration
{
    public function up()
    {
        $this->forge->addColumn('student_habits', [
            'habit_answers' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'tidur_cepat',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('student_habits', 'habit_answers');
    }
}
