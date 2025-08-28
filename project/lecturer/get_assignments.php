<?php
session_start();
require __DIR__ . '/../db_connection.php';

// Only lecturers can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Fetch all assignments
$stmt = $conn->query("
    SELECT id, student_id, filename, assignment_title, file_path, uploaded_at
    FROM assignments
    ORDER BY uploaded_at DESC
");
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($assignments);
