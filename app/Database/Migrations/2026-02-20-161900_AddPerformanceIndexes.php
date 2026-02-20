<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // Students table indexes
        if (!$this->db->indexExists('students', 'idx_students_nis')) {
            $this->db->query('CREATE INDEX idx_students_nis ON students(nis)');
        }
        
        if (!$this->db->indexExists('students', 'idx_students_nisn')) {
            $this->db->query('CREATE INDEX idx_students_nisn ON students(nisn)');
        }
        
        if (!$this->db->indexExists('students', 'idx_students_class_id')) {
            $this->db->query('CREATE INDEX idx_students_class_id ON students(class_id)');
        }
        
        if (!$this->db->indexExists('students', 'idx_students_active')) {
            $this->db->query('CREATE INDEX idx_students_active ON students(active)');
        }

        // Attendance logs indexes
        if (!$this->db->indexExists('attendance_logs', 'idx_attendance_date')) {
            $this->db->query('CREATE INDEX idx_attendance_date ON attendance_logs(date)');
        }
        
        if (!$this->db->indexExists('attendance_logs', 'idx_attendance_student_id')) {
            $this->db->query('CREATE INDEX idx_attendance_student_id ON attendance_logs(student_id)');
        }
        
        if (!$this->db->indexExists('attendance_logs', 'idx_attendance_status')) {
            $this->db->query('CREATE INDEX idx_attendance_status ON attendance_logs(status)');
        }
        
        // Composite index for common queries (date + student_id)
        if (!$this->db->indexExists('attendance_logs', 'idx_attendance_date_student')) {
            $this->db->query('CREATE INDEX idx_attendance_date_student ON attendance_logs(date, student_id)');
        }

        // Classes table indexes
        if (!$this->db->indexExists('classes', 'idx_classes_name')) {
            $this->db->query('CREATE INDEX idx_classes_name ON classes(name)');
        }

        // Habits table indexes
        if ($this->db->tableExists('student_habits')) {
            if (!$this->db->indexExists('student_habits', 'idx_habits_student_date')) {
                $this->db->query('CREATE INDEX idx_habits_student_date ON student_habits(student_id, date)');
            }
            
            if (!$this->db->indexExists('student_habits', 'idx_habits_date')) {
                $this->db->query('CREATE INDEX idx_habits_date ON student_habits(date)');
            }
        }

        // Users table indexes (Shield)
        if (!$this->db->indexExists('users', 'idx_users_username')) {
            $this->db->query('CREATE INDEX idx_users_username ON users(username)');
        }

        echo "✓ Performance indexes added successfully\n";
    }

    public function down()
    {
        // Students
        $this->db->query('DROP INDEX IF EXISTS idx_students_nis ON students');
        $this->db->query('DROP INDEX IF EXISTS idx_students_nisn ON students');
        $this->db->query('DROP INDEX IF EXISTS idx_students_class_id ON students');
        $this->db->query('DROP INDEX IF EXISTS idx_students_active ON students');

        // Attendance logs
        $this->db->query('DROP INDEX IF EXISTS idx_attendance_date ON attendance_logs');
        $this->db->query('DROP INDEX IF EXISTS idx_attendance_student_id ON attendance_logs');
        $this->db->query('DROP INDEX IF EXISTS idx_attendance_status ON attendance_logs');
        $this->db->query('DROP INDEX IF EXISTS idx_attendance_date_student ON attendance_logs');

        // Classes
        $this->db->query('DROP INDEX IF EXISTS idx_classes_name ON classes');

        // Habits
        if ($this->db->tableExists('student_habits')) {
            $this->db->query('DROP INDEX IF EXISTS idx_habits_student_date ON student_habits');
            $this->db->query('DROP INDEX IF EXISTS idx_habits_date ON student_habits');
        }

        // Users
        $this->db->query('DROP INDEX IF EXISTS idx_users_username ON users');

        echo "✓ Performance indexes removed\n";
    }
}
