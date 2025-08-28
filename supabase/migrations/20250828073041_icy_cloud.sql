/*
# Virtual Learning Platform Database Schema

This file contains all the necessary database tables and relationships for the VLP system.

## Tables Created:
1. Users - Main user authentication table
2. Students - Student-specific information
3. Lecturers - Lecturer-specific information
4. Modules - Course modules
5. Student_modules - Student module registrations (max 3)
6. Messages - Peer-to-peer and student-lecturer messaging
7. Lecturer_notes - PDF notes uploaded by lecturers
8. Quizzes - Quiz information
9. Quiz_questions - Individual quiz questions
10. Quiz_attempts - Student quiz attempts
11. Notifications - System notifications
12. Assignments - Student assignment submissions

## Security:
- All tables use proper foreign key constraints
- Password hashing implemented in application layer
- Input validation through PDO prepared statements
*/

-- Users table (main authentication)
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL CHECK (role IN ('admin', 'lecturer', 'student')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students table
CREATE TABLE IF NOT EXISTS students (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    stud_name VARCHAR(100) NOT NULL,
    stud_email VARCHAR(100) UNIQUE NOT NULL,
    stud_id VARCHAR(20) UNIQUE NOT NULL,
    program VARCHAR(100) NOT NULL,
    no_carries INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Lecturers table
CREATE TABLE IF NOT EXISTS lecturers (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    employee_id VARCHAR(20) UNIQUE NOT NULL,
    department VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Modules/Courses table
CREATE TABLE IF NOT EXISTS modules (
    id SERIAL PRIMARY KEY,
    module_code VARCHAR(20) UNIQUE NOT NULL,
    module_name VARCHAR(100) NOT NULL,
    description TEXT,
    lecturer_id INTEGER REFERENCES lecturers(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Student module registrations (max 3 per student)
CREATE TABLE IF NOT EXISTS student_modules (
    id SERIAL PRIMARY KEY,
    student_id INTEGER REFERENCES students(id) ON DELETE CASCADE,
    module_id INTEGER REFERENCES modules(id) ON DELETE CASCADE,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(student_id, module_id)
);

-- Messages table for peer-to-peer and student-lecturer communication
CREATE TABLE IF NOT EXISTS messages (
    id SERIAL PRIMARY KEY,
    sender_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    receiver_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    message_text TEXT NOT NULL,
    message_type VARCHAR(20) DEFAULT 'peer' CHECK (message_type IN ('peer', 'lecturer', 'admin')),
    is_read BOOLEAN DEFAULT FALSE,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Lecturer notes/PDFs
CREATE TABLE IF NOT EXISTS lecturer_notes (
    id SERIAL PRIMARY KEY,
    lecturer_id INTEGER REFERENCES lecturers(id) ON DELETE CASCADE,
    module_id INTEGER REFERENCES modules(id) ON DELETE CASCADE,
    title VARCHAR(200) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INTEGER,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Quizzes
CREATE TABLE IF NOT EXISTS quizzes (
    id SERIAL PRIMARY KEY,
    lecturer_id INTEGER REFERENCES lecturers(id) ON DELETE CASCADE,
    module_id INTEGER REFERENCES modules(id) ON DELETE CASCADE,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    time_limit INTEGER DEFAULT 30, -- minutes
    max_attempts INTEGER DEFAULT 1,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Quiz questions
CREATE TABLE IF NOT EXISTS quiz_questions (
    id SERIAL PRIMARY KEY,
    quiz_id INTEGER REFERENCES quizzes(id) ON DELETE CASCADE,
    question_text TEXT NOT NULL,
    option_a VARCHAR(500) NOT NULL,
    option_b VARCHAR(500) NOT NULL,
    option_c VARCHAR(500) NOT NULL,
    option_d VARCHAR(500) NOT NULL,
    correct_option CHAR(1) NOT NULL CHECK (correct_option IN ('A', 'B', 'C', 'D')),
    points INTEGER DEFAULT 1,
    question_order INTEGER DEFAULT 1
);

-- Quiz attempts
CREATE TABLE IF NOT EXISTS quiz_attempts (
    id SERIAL PRIMARY KEY,
    quiz_id INTEGER REFERENCES quizzes(id) ON DELETE CASCADE,
    student_id INTEGER REFERENCES students(id) ON DELETE CASCADE,
    score INTEGER DEFAULT 0,
    total_questions INTEGER DEFAULT 0,
    percentage DECIMAL(5,2) DEFAULT 0.00,
    time_taken INTEGER, -- minutes
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Quiz answers
CREATE TABLE IF NOT EXISTS quiz_answers (
    id SERIAL PRIMARY KEY,
    attempt_id INTEGER REFERENCES quiz_attempts(id) ON DELETE CASCADE,
    question_id INTEGER REFERENCES quiz_questions(id) ON DELETE CASCADE,
    selected_option CHAR(1) CHECK (selected_option IN ('A', 'B', 'C', 'D')),
    is_correct BOOLEAN DEFAULT FALSE
);

-- Notifications
CREATE TABLE IF NOT EXISTS notifications (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) DEFAULT 'info' CHECK (type IN ('info', 'warning', 'success', 'error')),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Assignments (existing table structure maintained)
CREATE TABLE IF NOT EXISTS assignments (
    id SERIAL PRIMARY KEY,
    student_id INTEGER REFERENCES students(id) ON DELETE CASCADE,
    module_id INTEGER REFERENCES modules(id),
    filename VARCHAR(255) NOT NULL,
    assignment_title VARCHAR(200) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_messages_sender ON messages(sender_id);
CREATE INDEX IF NOT EXISTS idx_messages_receiver ON messages(receiver_id);
CREATE INDEX IF NOT EXISTS idx_notifications_user ON notifications(user_id);
CREATE INDEX IF NOT EXISTS idx_student_modules_student ON student_modules(student_id);
CREATE INDEX IF NOT EXISTS idx_lecturer_notes_module ON lecturer_notes(module_id);
CREATE INDEX IF NOT EXISTS idx_quiz_attempts_student ON quiz_attempts(student_id);

-- Function to check student module registration limit
CREATE OR REPLACE FUNCTION check_student_module_limit()
RETURNS TRIGGER AS $$
BEGIN
    IF (SELECT COUNT(*) FROM student_modules WHERE student_id = NEW.student_id) >= 3 THEN
        RAISE EXCEPTION 'Student cannot register for more than 3 modules';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger to enforce module registration limit
DROP TRIGGER IF EXISTS student_module_limit_trigger ON student_modules;
CREATE TRIGGER student_module_limit_trigger
    BEFORE INSERT ON student_modules
    FOR EACH ROW
    EXECUTE FUNCTION check_student_module_limit();