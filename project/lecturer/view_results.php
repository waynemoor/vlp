<?php
// lecturer/view_results.php
require '../db_connection.php';
session_start();

$lecturer_id = $_SESSION['user_id'] ?? 1;

// Get quizzes created by this lecturer
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE lecturer_id=:lecturer_id");
$stmt->execute([':lecturer_id'=>$lecturer_id]);
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($quizzes as $quiz){
    echo "<h3>Quiz: {$quiz['title']}</h3>";

    // Get all attempts for this quiz
    $stmt2 = $conn->prepare("
        SELECT qa.*, s.name as student_name
        FROM quiz_attempts qa
        JOIN students s ON qa.student_id = s.id
        WHERE qa.quiz_id = :quiz_id
    ");
    $stmt2->execute([':quiz_id'=>$quiz['id']]);
    $attempts = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    if($attempts){
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Student</th><th>Score</th><th>Submitted At</th></tr>";
        foreach($attempts as $a){
            echo "<tr>
                    <td>{$a['student_name']}</td>
                    <td>{$a['score']}</td>
                    <td>{$a['submitted_at']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No attempts yet.</p>";
    }
}
