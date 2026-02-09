<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'user_id',
        'type',
        'title',
        'message',
        'student_id',
        'is_sent',
        'sent_at',
        'read_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';

    /**
     * Get unread notifications
     */
    public function getUnread($userId)
    {
        return $this->where('user_id', $userId)
            ->where('read_at IS NULL')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Mark as read
     */
    public function markAsRead($notificationId)
    {
        return $this->update($notificationId, ['read_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead($userId)
    {
        return $this->where('user_id', $userId)
            ->where('read_at IS NULL')
            ->set(['read_at' => date('Y-m-d H:i:s')])
            ->update();
    }

    /**
     * Create notification for check-in
     */
    public function createCheckInNotification($studentId, $checkInTime)
    {
        $studentModel = new StudentModel();
        $student = $studentModel->find($studentId);

        if (!$student) {
            return false;
        }

        // Get parent user ID
        $userModel = new UserModel();
        $parent = $userModel->where('student_id', $studentId)
            ->where('role', 'orang_tua')
            ->first();

        if (!$parent) {
            return false;
        }

        $message = sprintf(
            'Anak Anda, %s, telah tiba di sekolah pukul %s.',
            $student['name'],
            date('H:i', strtotime($checkInTime))
        );

        return $this->insert([
            'user_id'    => $parent['id'],
            'type'       => 'check_in',
            'title'      => 'Siswa Sudah Tiba di Sekolah',
            'message'    => $message,
            'student_id' => $studentId,
        ]);
    }
}
