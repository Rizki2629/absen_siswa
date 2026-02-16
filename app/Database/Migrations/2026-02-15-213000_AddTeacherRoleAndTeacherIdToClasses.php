<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTeacherRoleAndTeacherIdToClasses extends Migration
{
    public function up()
    {
        // 1. Add 'teacher' to ENUM role in users table
        $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('admin','guru_piket','siswa','orang_tua','teacher') NOT NULL DEFAULT 'siswa'");

        // 2. Fix existing teachers with empty role
        $this->db->query("UPDATE users SET role='teacher' WHERE role='' AND full_name != '' AND id NOT IN (SELECT id FROM (SELECT id FROM users WHERE role IN ('admin','guru_piket','siswa','orang_tua')) AS t)");

        // 3. Add teacher_id column to classes table
        $this->forge->addColumn('classes', [
            'teacher_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'homeroom_teacher',
            ],
        ]);
    }

    public function down()
    {
        // Remove teacher_id from classes
        $this->forge->dropColumn('classes', 'teacher_id');

        // Revert ENUM (remove teacher)
        $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('admin','guru_piket','siswa','orang_tua') NOT NULL DEFAULT 'siswa'");
    }
}
