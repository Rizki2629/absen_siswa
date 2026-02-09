<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttendanceExceptionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
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
            'exception_type' => [
                'type'       => 'ENUM',
                'constraint' => ['sakit', 'izin', 'lupa_scan', 'alpha'],
                'default'    => 'alpha',
            ],
            'check_in_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'check_out_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'proof_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path to uploaded proof (e.g., sick letter)',
            ],
            'created_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
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
        $this->forge->addUniqueKey(['student_id', 'date'], 'unique_student_date');
        $this->forge->createTable('attendance_exceptions', true);
    }

    public function down()
    {
        $this->forge->dropTable('attendance_exceptions', true);
    }
}
