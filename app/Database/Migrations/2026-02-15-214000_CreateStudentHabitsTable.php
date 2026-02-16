<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudentHabitsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'student_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'date' => [
                'type' => 'DATE',
            ],
            'bangun_pagi' => [
                'type'    => 'TINYINT',
                'default' => 0,
            ],
            'beribadah' => [
                'type'    => 'TINYINT',
                'default' => 0,
            ],
            'berolahraga' => [
                'type'    => 'TINYINT',
                'default' => 0,
            ],
            'makan_sehat' => [
                'type'    => 'TINYINT',
                'default' => 0,
            ],
            'gemar_belajar' => [
                'type'    => 'TINYINT',
                'default' => 0,
            ],
            'bermasyarakat' => [
                'type'    => 'TINYINT',
                'default' => 0,
            ],
            'tidur_cepat' => [
                'type'    => 'TINYINT',
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['student_id', 'date'], 'student_date_unique');
        $this->forge->createTable('student_habits');
    }

    public function down()
    {
        $this->forge->dropTable('student_habits');
    }
}
