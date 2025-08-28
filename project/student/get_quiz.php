<?php
session_start();
require '../db_connection.php';
require 'quiz.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    http_response_code(401);
    echo json_encode(['error'=>'Unauthorized']);
    exit;
}

$quiz_id = $_GET['quiz_id'] ?? null;
if (!$quiz_id) {
    echo json_encode([]);
    exit;
}

$quiz = new Quiz($conn, $_SESSION['user_id']);
$questions = $quiz->getQuestions($quiz_id);

echo json_encode($questions);
