<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddShiftIdToClasses extends Migration
{
    public function up()
    {
        $this->forge->addColumn('classes', [
            'shift_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'homeroom_teacher',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('classes', 'shift_id');
    }
}
