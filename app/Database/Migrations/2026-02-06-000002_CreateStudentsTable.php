<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nis'           => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 150],
            'class_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'gender'        => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'birth_date'    => ['type' => 'DATE', 'null' => true],
            'active'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nis');
        $this->forge->addKey(['class_id']);

        $this->forge->createTable('students', true);
    }

    public function down()
    {
        $this->forge->dropTable('students', true);
    }
}
