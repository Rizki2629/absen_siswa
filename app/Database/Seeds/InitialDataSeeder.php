<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Classes
        $classes = [
            ['name' => 'X IPA 1'],
            ['name' => 'X IPA 2'],
            ['name' => 'XI IPA 1'],
            ['name' => 'XII IPA 1'],
        ];

        foreach ($classes as $class) {
            $this->db->table('classes')->insert($class);
        }

        // 2. Create Students
        $students = [
            [
                'nis'        => '12345',
                'name'       => 'Budi Santoso',
                'class_id'   => 1,
                'gender'     => 'L',
                'birth_date' => '2008-05-15',
                'active'     => 1,
            ],
            [
                'nis'        => '12346',
                'name'       => 'Ani Wijaya',
                'class_id'   => 1,
                'gender'     => 'P',
                'birth_date' => '2008-08-20',
                'active'     => 1,
            ],
            [
                'nis'        => '12347',
                'name'       => 'Eko Prasetyo',
                'class_id'   => 2,
                'gender'     => 'L',
                'birth_date' => '2008-03-10',
                'active'     => 1,
            ],
            [
                'nis'        => '12348',
                'name'       => 'Dewi Lestari',
                'class_id'   => 2,
                'gender'     => 'P',
                'birth_date' => '2008-11-25',
                'active'     => 1,
            ],
            [
                'nis'        => '12349',
                'name'       => 'Rudi Hermawan',
                'class_id'   => 3,
                'gender'     => 'L',
                'birth_date' => '2007-07-08',
                'active'     => 1,
            ],
        ];

        foreach ($students as $student) {
            $this->db->table('students')->insert($student);
        }

        // 3. Create Users
        $users = [
            [
                'username'      => 'admin',
                'email'         => 'admin@school.com',
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'          => 'admin',
                'full_name'     => 'Administrator',
                'phone'         => '081234567890',
                'is_active'     => 1,
            ],
            [
                'username'      => 'guru.piket',
                'email'         => 'guru.piket@school.com',
                'password_hash' => password_hash('guru123', PASSWORD_DEFAULT),
                'role'          => 'guru_piket',
                'full_name'     => 'Guru Piket',
                'phone'         => '081234567891',
                'is_active'     => 1,
            ],
            [
                'username'      => '12345',
                'email'         => 'budi@student.school.com',
                'password_hash' => password_hash('siswa123', PASSWORD_DEFAULT),
                'role'          => 'siswa',
                'full_name'     => 'Budi Santoso',
                'phone'         => '081234567892',
                'student_id'    => 1,
                'is_active'     => 1,
            ],
            [
                'username'      => 'orangtua.12345',
                'email'         => 'orangtua.budi@parent.school.com',
                'password_hash' => password_hash('ortu123', PASSWORD_DEFAULT),
                'role'          => 'orang_tua',
                'full_name'     => 'Orang Tua Budi Santoso',
                'phone'         => '081234567893',
                'student_id'    => 1,
                'is_active'     => 1,
            ],
        ];

        foreach ($users as $user) {
            $this->db->table('users')->insert($user);
        }

        // 4. Create Default Shift
        $shift = [
            'name'            => 'Shift Pagi',
            'check_in_start'  => '06:30:00',
            'check_in_end'    => '07:15:00',
            'check_out_start' => '15:00:00',
            'check_out_end'   => '17:00:00',
            'late_tolerance'  => 5,
            'is_active'       => 1,
        ];

        $this->db->table('shifts')->insert($shift);

        // 5. Create Sample Device (untuk testing)
        $device = [
            'sn'         => 'DEV001',
            'name'       => 'Mesin Gerbang Utama',
            'ip_address' => '192.168.1.100',
            'port'       => 4370,
            'location'   => 'Gerbang Utama',
            'status'     => 'offline',
        ];

        $this->db->table('devices')->insert($device);

        // 6. Create Sample Device User Maps
        $mappings = [
            ['device_id' => 1, 'pin' => '105', 'student_id' => 1],
            ['device_id' => 1, 'pin' => '106', 'student_id' => 2],
            ['device_id' => 1, 'pin' => '107', 'student_id' => 3],
            ['device_id' => 1, 'pin' => '108', 'student_id' => 4],
            ['device_id' => 1, 'pin' => '109', 'student_id' => 5],
        ];

        foreach ($mappings as $mapping) {
            $this->db->table('device_user_maps')->insert($mapping);
        }

        echo "✅ Initial data seeded successfully!\n";
        echo "📝 Login credentials:\n";
        echo "   Admin: admin / admin123\n";
        echo "   Guru Piket: guru.piket / guru123\n";
        echo "   Siswa: 12345 / siswa123\n";
        echo "   Orang Tua: orangtua.12345 / ortu123\n";
    }
}
