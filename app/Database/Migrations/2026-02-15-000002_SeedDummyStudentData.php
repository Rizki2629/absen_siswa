<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedDummyStudentData extends Migration
{
    public function up()
    {
        // Insert a default class
        $this->db->table('classes')->insert([
            'name' => 'X IPA 1',
            'grade' => 'X',
            'year' => '2025/2026',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $classId = $this->db->insertID();

        // Insert dummy student
        $this->db->table('students')->insert([
            'nis' => '123456',
            'name' => 'RIZKI',
            'class_id' => $classId,
            'gender' => 'L',
            'parent_phone' => '085710002943',
            'parent_email' => 'rizki031010@gmail.com',
            'address' => 'Jl. Babakan',
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->db->table('students')->where('nis', '123456')->delete();
        $this->db->table('classes')->where('name', 'X IPA 1')->delete();
    }
}
