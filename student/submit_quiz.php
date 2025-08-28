<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$quiz_id = $_POST['quiz_id'] ?? null;
$time_taken = $_POST['time_taken'] ?? 0;

if (!$quiz_id) {
    echo json_encode(['error' => 'Quiz ID required']);
    exit;
}

try {
    $conn->beginTransaction();
    
    // Get student ID
    $studentStmt = $conn->prepare("SELECT id FROM students WHERE user_id = :user_id");
    $studentStmt->execute([':user_id' => $_SESSION['user_id']]);
    $student = $studentStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        throw new Exception('Student not found');
    }
    
    $student_id = $student['id'];
    
    // Get quiz questions with correct answers
    $questionsStmt = $conn->prepare("
        SELECT id, correct_option, points
        FROM quiz_questions
        WHERE quiz_id = :quiz_id
    ");
    $questionsStmt->execute([':quiz_id' => $quiz_id]);
    $questions = $questionsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total_questions = count($questions);
    $score = 0;
    $total_points = 0;
    
    // Create quiz attempt
    $attemptStmt = $conn->prepare("
        INSERT INTO quiz_attempts (quiz_id, student_id, total_questions, time_taken)
        VALUES (:quiz_id, :student_id, :total_questions, :time_taken)
        RETURNING id
    ");
    
    $attemptStmt->execute([
        ':quiz_id' => $quiz_id,
        ':student_id' => $student_id,
        ':total_questions' => $total_questions,
        ':time_taken' => $time_taken
    ]);
    
    $attempt = $attemptStmt->fetch(PDO::FETCH_ASSOC);
    $attempt_id = $attempt['id'];
    
    // Process answers
    foreach ($questions as $question) {
        $question_id = $question['id'];
        $correct_option = $question['correct_option'];
        $points = $question['points'];
        $total_points += $points;
        
        $selected_option = $_POST["question_$question_id"] ?? null;
        $is_correct = ($selected_option === $correct_option);
        
        if ($is_correct) {
            $score += $points;
        }
        
        // Save answer
        $answerStmt = $conn->prepare("
            INSERT INTO quiz_answers (attempt_id, question_id, selected_option, is_correct)
            VALUES (:attempt_id, :question_id, :selected_option, :is_correct)
        ");
        
        $answerStmt->execute([
            ':attempt_id' => $attempt_id,
            ':question_id' => $question_id,
            ':selected_option' => $selected_option,
            ':is_correct' => $is_correct
        ]);
    }
    
    // Calculate percentage
    $percentage = $total_points > 0 ? ($score / $total_points) * 100 : 0;
    
    // Update attempt with score
    $updateStmt = $conn->prepare("
        UPDATE quiz_attempts 
        SET score = :score, percentage = :percentage
        WHERE id = :attempt_id
    ");
    
    $updateStmt->execute([
        ':score' => $score,
        ':percentage' => $percentage,
        ':attempt_id' => $attempt_id
    ]);
    
    $conn->commit();
    
    echo json_encode([
        'success' => 'Quiz submitted successfully',
        'score' => $score,
        'total_points' => $total_points,
        'percentage' => round($percentage, 2),
        'attempt_id' => $attempt_id
    ]);
    
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['error' => 'Error submitting quiz: ' . $e->getMessage()]);
}
?>