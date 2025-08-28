<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$table_name = trim($_POST['table_name'] ?? '');

if (!$table_name) {
    echo json_encode(['error' => 'Table name is required']);
    exit;
}

// Validate table name (alphanumeric and underscores only)
if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table_name)) {
    echo json_encode(['error' => 'Invalid table name']);
    exit;
}

// Protect system tables
$protected_tables = [
    'users', 'students', 'lecturers', 'modules', 'student_modules',
    'messages', 'lecturer_notes', 'quizzes', 'quiz_questions',
    'quiz_attempts', 'quiz_answers', 'notifications', 'assignments'
];

if (in_array(strtolower($table_name), $protected_tables)) {
    echo json_encode(['error' => 'Cannot delete system table']);
    exit;
}

try {
    // Check if table exists
    $checkStmt = $conn->prepare("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' AND table_name = :table_name
    ");
    $checkStmt->execute([':table_name' => $table_name]);
    
    if (!$checkStmt->fetch()) {
        echo json_encode(['error' => 'Table does not exist']);
        exit;
    }
    
    // Drop the table
    $sql = "DROP TABLE IF EXISTS $table_name CASCADE";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    echo json_encode(['success' => "Table '$table_name' deleted successfully"]);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>