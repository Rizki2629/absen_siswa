<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHomeroomTeacherToClasses extends Migration
{
    public function up()
    {
        $fields = [
            'homeroom_teacher' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'after' => 'year'
            ],
        ];
        
        $this->forge->addColumn('classes', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('classes', ['homeroom_teacher']);
    }
}
