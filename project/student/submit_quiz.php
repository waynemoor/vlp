<?php
session_start();
require '../db_connection.php';
require 'quiz.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    http_response_code(401);
    echo json_encode(['error'=>'Unauthorized']);
    exit;
}

$quiz_id = $_POST['quiz_id'] ?? null;
if (!$quiz_id) {
    echo json_encode(['error'=>'No quiz selected']);
    exit;
}

// Collect answers
$answers = [];
foreach ($_POST as $key => $value) {
    if (strpos($key, 'q') === 0) { // e.g., q1, q2
        $answers[$key] = $value;
    }
}

$quiz = new Quiz($conn, $_SESSION['user_id']);
$result = $quiz->submit($quiz_id, $answers);

echo json_encode($result);
