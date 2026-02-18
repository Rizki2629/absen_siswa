<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CleanupLegacyDummyClasses extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        $this->softDeleteClassByExactName('XI IPA 1', $now);
        $this->cleanupDuplicateKelas5C($now);
    }

    public function down()
    {
        // Cleanup migration is irreversible by design.
    }

    private function cleanupDuplicateKelas5C(string $now): void
    {
        $rows = $this->db->query(
            "SELECT id, name FROM classes WHERE deleted_at IS NULL AND REPLACE(LOWER(name), ' ', '') = 'kelas5c' ORDER BY id DESC"
        )->getResultArray();

        if (count($rows) <= 1) {
            return;
        }

        $keepId = (int) $rows[0]['id'];

        for ($index = 1; $index < count($rows); $index++) {
            $removeId = (int) $rows[$index]['id'];

            $this->db->table('students')
                ->where('class_id', $removeId)
                ->where('deleted_at', null)
                ->update(['class_id' => $keepId]);

            $updateData = [
                'deleted_at' => $now,
            ];

            if ($this->columnExists('classes', 'updated_at')) {
                $updateData['updated_at'] = $now;
            }

            if ($this->columnExists('classes', 'teacher_id')) {
                $updateData['teacher_id'] = null;
            }

            if ($this->columnExists('classes', 'homeroom_teacher')) {
                $updateData['homeroom_teacher'] = '';
            }

            $this->db->table('classes')
                ->where('id', $removeId)
                ->update($updateData);
        }
    }

    private function softDeleteClassByExactName(string $className, string $now): void
    {
        $rows = $this->db->table('classes')
            ->select('id')
            ->where('name', $className)
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $classId = (int) $row['id'];

            $updateData = [
                'deleted_at' => $now,
            ];

            if ($this->columnExists('classes', 'updated_at')) {
                $updateData['updated_at'] = $now;
            }

            if ($this->columnExists('classes', 'teacher_id')) {
                $updateData['teacher_id'] = null;
            }

            if ($this->columnExists('classes', 'homeroom_teacher')) {
                $updateData['homeroom_teacher'] = '';
            }

            $this->db->table('classes')
                ->where('id', $classId)
                ->update($updateData);
        }
    }

    private function columnExists(string $table, string $column): bool
    {
        $result = $this->db->query("SHOW COLUMNS FROM {$table} LIKE ?", [$column])->getResultArray();
        return !empty($result);
    }
}
