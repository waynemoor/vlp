<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get available modules and student's current registrations
    try {
        // Get student ID
        $studentStmt = $conn->prepare("SELECT id FROM students WHERE user_id = :user_id");
        $studentStmt->execute([':user_id' => $_SESSION['user_id']]);
        $student = $studentStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$student) {
            echo json_encode(['error' => 'Student not found']);
            exit;
        }
        
        // Get all available modules
        $modulesStmt = $conn->prepare("
            SELECT m.*, l.name as lecturer_name,
                   CASE WHEN sm.id IS NOT NULL THEN true ELSE false END as is_registered
            FROM modules m
            JOIN lecturers l ON m.lecturer_id = l.id
            LEFT JOIN student_modules sm ON m.id = sm.module_id AND sm.student_id = :student_id
            ORDER BY m.module_name
        ");
        $modulesStmt->execute([':student_id' => $student['id']]);
        $modules = $modulesStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get current registration count
        $countStmt = $conn->prepare("SELECT COUNT(*) as count FROM student_modules WHERE student_id = :student_id");
        $countStmt->execute([':student_id' => $student['id']]);
        $registrationCount = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo json_encode([
            'modules' => $modules,
            'registration_count' => $registrationCount,
            'max_registrations' => 3
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Register for a module
    $module_id = $_POST['module_id'] ?? null;
    
    if (!$module_id) {
        echo json_encode(['error' => 'Module ID required']);
        exit;
    }
    
    try {
        // Get student ID
        $studentStmt = $conn->prepare("SELECT id FROM students WHERE user_id = :user_id");
        $studentStmt->execute([':user_id' => $_SESSION['user_id']]);
        $student = $studentStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$student) {
            echo json_encode(['error' => 'Student not found']);
            exit;
        }
        
        // Check current registration count
        $countStmt = $conn->prepare("SELECT COUNT(*) as count FROM student_modules WHERE student_id = :student_id");
        $countStmt->execute([':student_id' => $student['id']]);
        $currentCount = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($currentCount >= 3) {
            echo json_encode(['error' => 'Maximum of 3 module registrations allowed']);
            exit;
        }
        
        // Register for module
        $registerStmt = $conn->prepare("
            INSERT INTO student_modules (student_id, module_id)
            VALUES (:student_id, :module_id)
        ");
        
        $registerStmt->execute([
            ':student_id' => $student['id'],
            ':module_id' => $module_id
        ]);
        
        echo json_encode(['success' => 'Successfully registered for module']);
        
    } catch (PDOException $e) {
        if ($e->getCode() == '23505') { // Unique constraint violation
            echo json_encode(['error' => 'Already registered for this module']);
        } else {
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
}
?>