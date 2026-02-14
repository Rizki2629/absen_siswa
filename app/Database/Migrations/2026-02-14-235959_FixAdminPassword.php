<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixAdminPassword extends Migration
{
    public function up()
    {
        // Delete existing admin user if it exists
        $this->db->table('users')->where('username', 'admin')->delete();

        // Insert admin user with properly hashed password
        $this->db->table('users')->insert([
            'username' => 'admin',
            'email' => 'admin@absensi.com',
            'password_hash' => '$2y$10$BKPj2J6rUaeg0Qjsdeb5hOvjvLrYQX6M5.QAwESN5pVjDRVL/SvaO', // hash of 'admin123'
            'role' => 'admin',
            'full_name' => 'Administrator',
            'phone' => '08123456789',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        // Delete admin user on rollback
        $this->db->table('users')->where('username', 'admin')->delete();
    }
}
