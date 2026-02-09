<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeviceConnectionFields extends Migration
{
    public function up()
    {
        $fields = [
            'port' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 4370,
                'after'      => 'ip_address',
            ],
            'comm_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'port',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['online', 'offline'],
                'default'    => 'offline',
                'after'      => 'comm_key',
            ],
            'push_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'status',
            ],
        ];

        $this->forge->addColumn('devices', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('devices', ['port', 'comm_key', 'status', 'push_url']);
    }
}
