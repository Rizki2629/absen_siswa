<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixDeviceUserMapsTable extends Migration
{
    public function up()
    {
        // Rename pin to device_user_id
        $this->forge->modifyColumn('device_user_maps', [
            'pin' => [
                'name' => 'device_user_id',
                'type' => 'VARCHAR',
                'constraint' => 30,
            ],
        ]);

        // Add privilege_level field
        $this->forge->addColumn('device_user_maps', [
            'privilege_level' => [
                'type' => 'TINYINT',
                'unsigned' => true,
                'default' => 0,
                'after' => 'device_user_id',
            ],
        ]);

        // Drop old unique key and create new one
        $this->db->query('ALTER TABLE device_user_maps DROP INDEX device_id_pin');
        $this->db->query('ALTER TABLE device_user_maps ADD UNIQUE KEY device_id_device_user_id (device_id, device_user_id)');
    }

    public function down()
    {
        // Reverse changes
        $this->forge->modifyColumn('device_user_maps', [
            'device_user_id' => [
                'name' => 'pin',
                'type' => 'VARCHAR',
                'constraint' => 30,
            ],
        ]);

        $this->forge->dropColumn('device_user_maps', 'privilege_level');

        $this->db->query('ALTER TABLE device_user_maps DROP INDEX device_id_device_user_id');
        $this->db->query('ALTER TABLE device_user_maps ADD UNIQUE KEY device_id_pin (device_id, pin)');
    }
}
