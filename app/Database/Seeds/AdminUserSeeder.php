<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $userModel = new \App\Models\UserModel();

        // Check if admin already exists
        $existingAdmin = $userModel->where('username', 'admin')->first();

        if ($existingAdmin) {
            echo "Admin user already exists.\n";
            return;
        }

        // Create admin user with hashed password
        $userData = [
            'username' => 'admin',
            'email' => 'admin@absensi.com',
            'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'full_name' => 'Administrator',
            'phone' => '08123456789',
            'is_active' => 1,
        ];

        $userModel->insert($userData);

        echo "Admin user created successfully.\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
    }
}
