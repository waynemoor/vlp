<?php
require '../db_connection.php';
session_start(); // lecturer_id should be in session

$lecturer_id = $_SESSION['username'] ?? 1; // adjust as needed
$title = $_POST['title'] ?? '';

if(!$title){
    echo json_encode(['success'=>false,'message'=>'Quiz title required']);
    exit;
}

// Insert quiz
$stmt = $conn->prepare("INSERT INTO quizzes (lecturer_id, title) VALUES (:lecturer_id, :title)");
$stmt->execute([':lecturer_id'=>$lecturer_id, ':title'=>$title]);
$quiz_id = $conn->lastInsertId();

// Insert questions dynamically
foreach($_POST as $key => $value){
    if(strpos($key,'question_text_') === 0){
        $qnum = explode('_',$key)[2];
        $stmt = $conn->prepare("
            INSERT INTO questions 
            (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option)
            VALUES (:quiz_id, :question_text, :option_a, :option_b, :option_c, :option_d, :correct_option)
        ");
        $stmt->execute([
            ':quiz_id'=>$quiz_id,
            ':question_text'=>$_POST["question_text_$qnum"],
            ':option_a'=>$_POST["option_a_$qnum"],
            ':option_b'=>$_POST["option_b_$qnum"],
            ':option_c'=>$_POST["option_c_$qnum"],
            ':option_d'=>$_POST["option_d_$qnum"],
            ':correct_option'=>$_POST["correct_option_$qnum"]
        ]);
    }
}

echo json_encode(['success'=>true,'message'=>'Quiz saved successfully']);
