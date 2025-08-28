<?php
// lecturer/get_quiz.php
require '../db_connection.php';

$quiz_id = $_GET['quiz_id'] ?? 0;
if(!$quiz_id){
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = :quiz_id");
$stmt->execute([':quiz_id'=>$quiz_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($questions);
