<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStudentImportProfileFields extends Migration
{
    public function up()
    {
        $existingFields = $this->getStudentFieldNames();

        $fields = [
            'nipd' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
                'after' => 'nis',
            ],
            'nisn' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
                'after' => 'nipd',
            ],
            'birth_place' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'null' => true,
                'after' => 'gender',
            ],
            'nik' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
                'after' => 'birth_date',
            ],
            'religion' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
                'after' => 'nik',
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'parent_phone',
            ],
            'rt' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true,
                'after' => 'address',
            ],
            'rw' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true,
                'after' => 'rt',
            ],
            'kelurahan' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'null' => true,
                'after' => 'rw',
            ],
            'kecamatan' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'null' => true,
                'after' => 'kelurahan',
            ],
            'father_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'after' => 'kecamatan',
            ],
            'mother_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'after' => 'father_name',
            ],
        ];

        $safeFields = [];
        foreach ($fields as $name => $definition) {
            if (!in_array($name, $existingFields, true)) {
                $safeFields[$name] = $definition;
            }
        }

        if (!empty($safeFields)) {
            $this->forge->addColumn('students', $safeFields);
        }
    }

    public function down()
    {
        $existingFields = $this->getStudentFieldNames();

        $columns = [
            'nipd',
            'nisn',
            'birth_place',
            'nik',
            'religion',
            'phone',
            'rt',
            'rw',
            'kelurahan',
            'kecamatan',
            'father_name',
            'mother_name',
        ];

        $dropColumns = [];
        foreach ($columns as $column) {
            if (in_array($column, $existingFields, true)) {
                $dropColumns[] = $column;
            }
        }

        if (!empty($dropColumns)) {
            $this->forge->dropColumn('students', $dropColumns);
        }
    }

    private function getStudentFieldNames(): array
    {
        $result = $this->db->query('SHOW COLUMNS FROM students')->getResultArray();

        $fields = [];
        foreach ($result as $column) {
            if (isset($column['Field'])) {
                $fields[] = (string) $column['Field'];
            }
        }

        return $fields;
    }
}
