<?php
// QuizResult class (inside student/quiz_result.php)
class QuizResult {
    private $conn;
    private $student_id;

    public function __construct($conn, $student_id){
        $this->conn = $conn;
        $this->student_id = $student_id;
    }

    public function getAttempts(){
        $stmt = $this->conn->prepare("
            SELECT qa.id as attempt_id, qa.score, q.title, COUNT(qs.id) as total_questions
            FROM quiz_attempts qa
            JOIN quizzes q ON qa.quiz_id = q.id
            JOIN questions qs ON qs.quiz_id = q.id
            WHERE qa.student_id=:student_id
            GROUP BY qa.id
            ORDER BY qa.id DESC
        ");
        $stmt->execute([':student_id'=>$this->student_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAttemptDetails($attempt_id){
        $stmt = $this->conn->prepare("
            SELECT qa.question_id, qa.selected_option, q.question_text, q.correct_option
            FROM quiz_answers qa
            JOIN questions q ON qa.question_id = q.id
            WHERE qa.attempt_id=:attempt_id
        ");
        $stmt->execute([':attempt_id'=>$attempt_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
