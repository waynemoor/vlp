<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$quiz_id = $_GET['quiz_id'] ?? null;

try {
    if ($quiz_id) {
        // Get results for specific quiz
        $stmt = $conn->prepare("
            SELECT qa.*, q.title as quiz_title, s.stud_name, s.stud_id, m.module_name
            FROM quiz_attempts qa
            JOIN quizzes q ON qa.quiz_id = q.id
            JOIN students s ON qa.student_id = s.id
            JOIN modules m ON q.module_id = m.id
            JOIN lecturers l ON q.lecturer_id = l.id
            WHERE q.id = :quiz_id AND l.user_id = :user_id
            ORDER BY qa.submitted_at DESC
        ");
        
        $stmt->execute([
            ':quiz_id' => $quiz_id,
            ':user_id' => $_SESSION['user_id']
        ]);
    } else {
        // Get all quiz results for lecturer's quizzes
        $stmt = $conn->prepare("
            SELECT qa.*, q.title as quiz_title, s.stud_name, s.stud_id, m.module_name
            FROM quiz_attempts qa
            JOIN quizzes q ON qa.quiz_id = q.id
            JOIN students s ON qa.student_id = s.id
            JOIN modules m ON q.module_id = m.id
            JOIN lecturers l ON q.lecturer_id = l.id
            WHERE l.user_id = :user_id
            ORDER BY qa.submitted_at DESC
        ");
        
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
    }
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>