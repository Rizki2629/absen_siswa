<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttendanceSummariesTable extends Migration
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
            'check_in_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'check_out_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['hadir', 'terlambat', 'sakit', 'izin', 'alpha', 'lupa_scan'],
                'default'    => 'alpha',
            ],
            'is_late' => [
                'type'    => 'TINYINT',
                'default' => 0,
            ],
            'late_minutes' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addUniqueKey(['student_id', 'date'], 'unique_summary_student_date');
        $this->forge->createTable('attendance_summaries', true);
    }

    public function down()
    {
        $this->forge->dropTable('attendance_summaries', true);
    }
}
