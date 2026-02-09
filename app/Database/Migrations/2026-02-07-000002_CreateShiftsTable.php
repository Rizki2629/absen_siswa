<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateShiftsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'check_in_start' => [
                'type' => 'TIME',
            ],
            'check_in_end' => [
                'type' => 'TIME',
            ],
            'check_out_start' => [
                'type' => 'TIME',
            ],
            'check_out_end' => [
                'type' => 'TIME',
            ],
            'late_tolerance' => [
                'type'    => 'INT',
                'comment' => 'Minutes of tolerance before marked as late',
                'default' => 0,
            ],
            'is_active' => [
                'type'    => 'TINYINT',
                'default' => 1,
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
        $this->forge->createTable('shifts', true);
    }

    public function down()
    {
        $this->forge->dropTable('shifts', true);
    }
}
