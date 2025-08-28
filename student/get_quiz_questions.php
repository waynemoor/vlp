<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$quiz_id = $_GET['quiz_id'] ?? null;

if (!$quiz_id) {
    echo json_encode(['error' => 'Quiz ID required']);
    exit;
}

try {
    // Verify student has access to this quiz
    $accessStmt = $conn->prepare("
        SELECT q.id, q.title, q.time_limit, q.max_attempts
        FROM quizzes q
        JOIN modules m ON q.module_id = m.id
        JOIN student_modules sm ON m.id = sm.module_id
        JOIN students s ON sm.student_id = s.id
        WHERE q.id = :quiz_id AND s.user_id = :user_id AND q.is_active = TRUE
    ");
    
    $accessStmt->execute([
        ':quiz_id' => $quiz_id,
        ':user_id' => $_SESSION['user_id']
    ]);
    
    $quiz = $accessStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$quiz) {
        echo json_encode(['error' => 'Quiz not found or access denied']);
        exit;
    }
    
    // Check attempt limit
    $attemptStmt = $conn->prepare("
        SELECT COUNT(*) as attempt_count
        FROM quiz_attempts qa
        JOIN students s ON qa.student_id = s.id
        WHERE qa.quiz_id = :quiz_id AND s.user_id = :user_id
    ");
    
    $attemptStmt->execute([
        ':quiz_id' => $quiz_id,
        ':user_id' => $_SESSION['user_id']
    ]);
    
    $attemptData = $attemptStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($attemptData['attempt_count'] >= $quiz['max_attempts']) {
        echo json_encode(['error' => 'Maximum attempts reached']);
        exit;
    }
    
    // Get quiz questions
    $stmt = $conn->prepare("
        SELECT id, question_text, option_a, option_b, option_c, option_d, points
        FROM quiz_questions
        WHERE quiz_id = :quiz_id
        ORDER BY question_order, id
    ");
    
    $stmt->execute([':quiz_id' => $quiz_id]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'quiz' => $quiz,
        'questions' => $questions
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>