<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT qa.*, q.title as quiz_title, m.module_name, m.module_code
        FROM quiz_attempts qa
        JOIN quizzes q ON qa.quiz_id = q.id
        JOIN modules m ON q.module_id = m.id
        JOIN students s ON qa.student_id = s.id
        WHERE s.user_id = :user_id
        ORDER BY qa.submitted_at DESC
    ");
    
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($results);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>