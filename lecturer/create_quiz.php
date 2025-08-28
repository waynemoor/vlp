<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$module_id = $_POST['module_id'] ?? null;
$time_limit = $_POST['time_limit'] ?? 30;
$max_attempts = $_POST['max_attempts'] ?? 1;

if (!$title || !$module_id) {
    echo json_encode(['error' => 'Title and module are required']);
    exit;
}

try {
    $conn->beginTransaction();
    
    // Get lecturer ID
    $lecturerStmt = $conn->prepare("SELECT id FROM lecturers WHERE user_id = :user_id");
    $lecturerStmt->execute([':user_id' => $_SESSION['user_id']]);
    $lecturer = $lecturerStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$lecturer) {
        throw new Exception('Lecturer not found');
    }
    
    // Verify lecturer owns this module
    $moduleStmt = $conn->prepare("SELECT id FROM modules WHERE id = :module_id AND lecturer_id = :lecturer_id");
    $moduleStmt->execute([
        ':module_id' => $module_id,
        ':lecturer_id' => $lecturer['id']
    ]);
    
    if (!$moduleStmt->fetch()) {
        throw new Exception('Module not found or access denied');
    }
    
    // Create quiz
    $quizStmt = $conn->prepare("
        INSERT INTO quizzes (lecturer_id, module_id, title, description, time_limit, max_attempts)
        VALUES (:lecturer_id, :module_id, :title, :description, :time_limit, :max_attempts)
        RETURNING id
    ");
    
    $quizStmt->execute([
        ':lecturer_id' => $lecturer['id'],
        ':module_id' => $module_id,
        ':title' => $title,
        ':description' => $description,
        ':time_limit' => $time_limit,
        ':max_attempts' => $max_attempts
    ]);
    
    $quiz = $quizStmt->fetch(PDO::FETCH_ASSOC);
    $quiz_id = $quiz['id'];
    
    // Add questions
    $question_count = 0;
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'question_text_') === 0) {
            $question_num = str_replace('question_text_', '', $key);
            
            $question_text = trim($value);
            $option_a = trim($_POST["option_a_$question_num"] ?? '');
            $option_b = trim($_POST["option_b_$question_num"] ?? '');
            $option_c = trim($_POST["option_c_$question_num"] ?? '');
            $option_d = trim($_POST["option_d_$question_num"] ?? '');
            $correct_option = $_POST["correct_option_$question_num"] ?? '';
            $points = $_POST["points_$question_num"] ?? 1;
            
            if ($question_text && $option_a && $option_b && $option_c && $option_d && $correct_option) {
                $questionStmt = $conn->prepare("
                    INSERT INTO quiz_questions 
                    (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option, points, question_order)
                    VALUES (:quiz_id, :question_text, :option_a, :option_b, :option_c, :option_d, :correct_option, :points, :question_order)
                ");
                
                $questionStmt->execute([
                    ':quiz_id' => $quiz_id,
                    ':question_text' => $question_text,
                    ':option_a' => $option_a,
                    ':option_b' => $option_b,
                    ':option_c' => $option_c,
                    ':option_d' => $option_d,
                    ':correct_option' => $correct_option,
                    ':points' => $points,
                    ':question_order' => $question_count + 1
                ]);
                
                $question_count++;
            }
        }
    }
    
    if ($question_count === 0) {
        throw new Exception('At least one question is required');
    }
    
    // Notify students registered for this module
    $notifyStmt = $conn->prepare("
        INSERT INTO notifications (user_id, title, message, type)
        SELECT s.user_id, 'New Quiz Available', 
               CONCAT('A new quiz \"', :title, '\" has been created for ', m.module_name), 'info'
        FROM student_modules sm
        JOIN students s ON sm.student_id = s.id
        JOIN modules m ON sm.module_id = m.id
        WHERE sm.module_id = :module_id
    ");
    
    $notifyStmt->execute([
        ':title' => $title,
        ':module_id' => $module_id
    ]);
    
    $conn->commit();
    
    echo json_encode([
        'success' => 'Quiz created successfully',
        'quiz_id' => $quiz_id,
        'question_count' => $question_count
    ]);
    
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['error' => 'Error creating quiz: ' . $e->getMessage()]);
}
?>