<?php
require '../db_connection.php';
session_start();
$student_id = $_SESSION['user_id'] ?? 1;

$quiz_id = $_POST['quiz_id'] ?? 0;
if(!$quiz_id){ echo json_encode(['score'=>0,'total'=>0]); exit; }

// Insert attempt
$stmt = $conn->prepare("INSERT INTO quiz_attempts (quiz_id, student_id) VALUES (:quiz_id,:student_id)");
$stmt->execute([':quiz_id'=>$quiz_id, ':student_id'=>$student_id]);
$attempt_id = $conn->lastInsertId();

// Fetch questions
$stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id=:quiz_id");
$stmt->execute([':quiz_id'=>$quiz_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$score = 0;
$total = count($questions);

foreach($questions as $q){
    $qid = $q['id'];
    $selected = $_POST["q$qid"] ?? '';
    if($selected === $q['correct_option']) $score++;

    $stmt = $conn->prepare("INSERT INTO quiz_answers (attempt_id, question_id, selected_option) VALUES (:attempt_id, :question_id, :selected_option)");
    $stmt->execute([':attempt_id'=>$attempt_id, ':question_id'=>$qid, ':selected_option'=>$selected]);
}

// Update score
$stmt = $conn->prepare("UPDATE quiz_attempts SET score=:score WHERE id=:attempt_id");
$stmt->execute([':score'=>$score, ':attempt_id'=>$attempt_id]);

echo json_encode(['score'=>$score,'total'=>$total]);
