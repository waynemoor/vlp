<?php
// Quiz class (inside student/quiz.php)
class Quiz {
    private $conn;
    private $student_id;

    public function __construct($conn, $student_id){
        $this->conn = $conn;
        $this->student_id = $student_id;
    }

    public function getQuestions($quiz_id){
        $stmt = $this->conn->prepare("SELECT * FROM questions WHERE quiz_id=:quiz_id");
        $stmt->execute([':quiz_id'=>$quiz_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function submit($quiz_id, $answers){
        $stmt = $this->conn->prepare("INSERT INTO quiz_attempts (quiz_id, student_id) VALUES (:quiz_id,:student_id)");
        $stmt->execute([':quiz_id'=>$quiz_id, ':student_id'=>$this->student_id]);
        $attempt_id = $this->conn->lastInsertId();

        $score = 0;
        $stmt = $this->conn->prepare("SELECT * FROM questions WHERE quiz_id=:quiz_id");
        $stmt->execute([':quiz_id'=>$quiz_id]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = count($questions);

        foreach($questions as $q){
            $qid = $q['id'];
            $selected = $answers["q$qid"] ?? '';
            if($selected === $q['correct_option']) $score++;

            $stmt2 = $this->conn->prepare("INSERT INTO quiz_answers (attempt_id, question_id, selected_option) VALUES (:attempt_id,:question_id,:selected_option)");
            $stmt2->execute([':attempt_id'=>$attempt_id, ':question_id'=>$qid, ':selected_option'=>$selected]);
        }

        $stmt = $this->conn->prepare("UPDATE quiz_attempts SET score=:score WHERE id=:attempt_id");
        $stmt->execute([':score'=>$score, ':attempt_id'=>$attempt_id]);

        return ['score'=>$score,'total'=>$total,'attempt_id'=>$attempt_id];
    }
}
