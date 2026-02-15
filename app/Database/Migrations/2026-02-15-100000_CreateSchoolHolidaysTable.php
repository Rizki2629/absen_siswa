<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchoolHolidaysTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'date' => [
                'type' => 'DATE',
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['national', 'school'],
                'default' => 'school',
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
        $this->forge->addKey('date');
        $this->forge->createTable('school_holidays', true);
    }

    public function down()
    {
        $this->forge->dropTable('school_holidays', true);
    }
}
