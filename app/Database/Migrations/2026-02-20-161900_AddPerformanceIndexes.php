<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // Students table indexes
        try {
            $this->db->query('CREATE INDEX idx_students_nis ON students(nis)');
        } catch (\Exception $e) {
            // Index might already exist
        }
        
        try {
            $this->db->query('CREATE INDEX idx_students_nisn ON students(nisn)');
        } catch (\Exception $e) {
            // Index might already exist
        }
        
        try {
            $this->db->query('CREATE INDEX idx_students_class_id ON students(class_id)');
        } catch (\Exception $e) {
            // Index might already exist
        }
        
        try {
            $this->db->query('CREATE INDEX idx_students_active ON students(active)');
        } catch (\Exception $e) {
            // Index might already exist
        }

        // Attendance logs indexes
        try {
            $this->db->query('CREATE INDEX idx_attendance_date ON attendance_logs(date)');
        } catch (\Exception $e) {
            // Index might already exist
        }
        
        try {
            $this->db->query('CREATE INDEX idx_attendance_student_id ON attendance_logs(student_id)');
        } catch (\Exception $e) {
            // Index might already exist
        }
        
        try {
            $this->db->query('CREATE INDEX idx_attendance_status ON attendance_logs(status)');
        } catch (\Exception $e) {
            // Index might already exist
        }
        
        try {
            $this->db->query('CREATE INDEX idx_attendance_date_student ON attendance_logs(date, student_id)');
        } catch (\Exception $e) {
            // Index might already exist
        }

        // Classes table indexes
        try {
            $this->db->query('CREATE INDEX idx_classes_name ON classes(name)');
        } catch (\Exception $e) {
            // Index might already exist
        }

        // Habits table indexes
        if ($this->db->tableExists('student_habits')) {
            try {
                $this->db->query('CREATE INDEX idx_habits_student_date ON student_habits(student_id, date)');
            } catch (\Exception $e) {
                // Index might already exist
            }
            
            try {
                $this->db->query('CREATE INDEX idx_habits_date ON student_habits(date)');
            } catch (\Exception $e) {
                // Index might already exist
            }
        }

        // Users table indexes (Shield)
        try {
            $this->db->query('CREATE INDEX idx_users_username ON users(username)');
        } catch (\Exception $e) {
            // Index might already exist
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
