<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttendanceLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'device_id'  => ['type' => 'INT', 'unsigned' => true],
            'student_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'pin'        => ['type' => 'VARCHAR', 'constraint' => 30],
            'att_time'   => ['type' => 'DATETIME'],
            'status'     => ['type' => 'INT', 'null' => true],
            'work_code'  => ['type' => 'INT', 'null' => true],
            'raw'        => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['device_id']);
        $this->forge->addKey(['student_id']);
        $this->forge->addKey(['att_time']);
        $this->forge->addUniqueKey(['device_id', 'pin', 'att_time', 'status']);
        $this->forge->createTable('attendance_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('attendance_logs', true);
    }
}
