<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'username',
        'email',
        'password_hash',
        'role',
        'full_name',
        'phone',
        'nip',
        'student_id',
        'is_active',
        'last_login_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            unset($data['data']['password']);
        }

        return $data;
    }

    /**
     * Verify password
     */
    public function verifyPassword($username, $password)
    {
        // Try normal username/email login first
        $user = $this->where('username', $username)
            ->orWhere('email', $username)
            ->first();

        // If not found, try NISN/NIS for students
        if (!$user) {
            $db = \Config\Database::connect();
            $builder = $db->table('users');
            $builder->select('users.*');
            $builder->join('students', 'students.id = users.student_id', 'left');
            $builder->groupStart()
                ->where('students.nisn', $username)
                ->orWhere('students.nis', $username)
                ->groupEnd();
            $user = $builder->get()->getRowArray();
        }

        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }

        return false;
    }

    /**
     * Update last login
     */
    public function updateLastLogin($userId)
    {
        return $this->update($userId, ['last_login_at' => date('Y-m-d H:i:s')]);
    }
}
