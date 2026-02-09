<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDevicesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'sn'            => ['type' => 'VARCHAR', 'constraint' => 64],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'ip_address'    => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'location'      => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'last_seen_at'  => ['type' => 'DATETIME', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('sn');
        $this->forge->createTable('devices', true);
    }

    public function down()
    {
        $this->forge->dropTable('devices', true);
    }
}
