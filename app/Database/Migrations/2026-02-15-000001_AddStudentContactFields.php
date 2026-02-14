<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStudentContactFields extends Migration
{
    public function up()
    {
        $fields = [
            'parent_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'birth_date'
            ],
            'parent_email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'parent_phone'
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'parent_email'
            ],
        ];
        
        $this->forge->addColumn('students', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('students', ['parent_phone', 'parent_email', 'address']);
    }
}
