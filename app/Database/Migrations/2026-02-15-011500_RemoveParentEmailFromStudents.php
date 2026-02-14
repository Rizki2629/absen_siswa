<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveParentEmailFromStudents extends Migration
{
    public function up()
    {
        // Check if column exists first
        if ($this->db->fieldExists('parent_email', 'students')) {
            $this->forge->dropColumn('students', 'parent_email');
        }
    }

    public function down()
    {
        // Add column back if rolling back
        $this->forge->addColumn('students', [
            'parent_email' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'parent_phone',
            ],
        ]);
    }
}
