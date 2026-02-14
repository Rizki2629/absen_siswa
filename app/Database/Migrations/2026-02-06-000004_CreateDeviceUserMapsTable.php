<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDeviceUserMapsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'device_id'        => ['type' => 'INT', 'unsigned' => true],
            'student_id'       => ['type' => 'INT', 'unsigned' => true],
            'device_user_id'   => ['type' => 'VARCHAR', 'constraint' => 30],
            'privilege_level'  => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 0],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['device_id', 'device_user_id']);
        $this->forge->addKey(['student_id']);
        $this->forge->createTable('device_user_maps', true);
    }

    public function down()
    {
        $this->forge->dropTable('device_user_maps', true);
    }
}
