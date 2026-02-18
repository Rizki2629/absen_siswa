<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MigrateStudentUsernamesToNisn extends Migration
{
    public function up()
    {
        if (!$this->tableExists('users') || !$this->tableExists('students')) {
            return;
        }

        if (!$this->columnExists('students', 'nisn') || !$this->columnExists('users', 'username')) {
            return;
        }

        $rows = $this->db->query(
            "SELECT u.id, u.username, s.nisn
             FROM users u
             INNER JOIN students s ON s.id = u.student_id
             WHERE u.student_id IS NOT NULL
               AND u.deleted_at IS NULL
               AND (u.role = 'siswa' OR u.role = 'student')
               AND s.nisn IS NOT NULL
               AND TRIM(s.nisn) <> ''
             ORDER BY u.id ASC"
        )->getResultArray();

        if (empty($rows)) {
            return;
        }

        $hasUpdatedAt = $this->columnExists('users', 'updated_at');
        $now = date('Y-m-d H:i:s');

        foreach ($rows as $row) {
            $userId = (int) ($row['id'] ?? 0);
            $currentUsername = trim((string) ($row['username'] ?? ''));
            $nisn = trim((string) ($row['nisn'] ?? ''));

            if ($userId <= 0 || $nisn === '') {
                continue;
            }

            $base = preg_replace('/[^a-zA-Z0-9._-]/', '', $nisn);
            $base = trim((string) $base);
            if ($base === '') {
                continue;
            }

            $candidate = $this->buildUniqueUsername($base, $userId);
            if ($candidate === '' || $candidate === $currentUsername) {
                continue;
            }

            $updateData = ['username' => $candidate];
            if ($hasUpdatedAt) {
                $updateData['updated_at'] = $now;
            }

            $this->db->table('users')
                ->where('id', $userId)
                ->update($updateData);
        }
    }

    public function down()
    {
        // Irreversible one-time data migration.
    }

    private function buildUniqueUsername(string $base, int $excludeUserId): string
    {
        $candidate = $base;
        $counter = 1;

        while ($this->usernameExists($candidate, $excludeUserId)) {
            $counter++;
            $candidate = $base . $counter;
        }

        return $candidate;
    }

    private function usernameExists(string $username, int $excludeUserId): bool
    {
        $builder = $this->db->table('users')
            ->select('id')
            ->where('username', $username)
            ->where('id !=', $excludeUserId)
            ->limit(1);

        $row = $builder->get()->getRowArray();
        return !empty($row);
    }

    private function tableExists(string $table): bool
    {
        return in_array($table, $this->db->listTables(), true);
    }

    private function columnExists(string $table, string $column): bool
    {
        $result = $this->db->query("SHOW COLUMNS FROM {$table} LIKE ?", [$column])->getResultArray();
        return !empty($result);
    }
}
