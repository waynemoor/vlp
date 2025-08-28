<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Get quizzes for modules the student is registered for
    $stmt = $conn->prepare("
        SELECT q.*, m.module_name, m.module_code, l.name as lecturer_name,
               (SELECT COUNT(*) FROM quiz_attempts qa WHERE qa.quiz_id = q.id AND qa.student_id = s.id) as attempts_count
        FROM quizzes q
        JOIN modules m ON q.module_id = m.id
        JOIN lecturers l ON q.lecturer_id = l.id
        JOIN student_modules sm ON m.id = sm.module_id
        JOIN students s ON sm.student_id = s.id
        WHERE s.user_id = :user_id AND q.is_active = TRUE
        ORDER BY q.created_at DESC
    ");
    
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($quizzes);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>