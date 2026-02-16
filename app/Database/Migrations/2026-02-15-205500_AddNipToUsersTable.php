<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNipToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'nip' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'full_name',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'nip');
    }
}
