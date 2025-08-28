<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Get assignments for modules taught by this lecturer
    $stmt = $conn->prepare("
        SELECT a.*, s.stud_name, s.stud_id, m.module_name, m.module_code
        FROM assignments a
        JOIN students s ON a.student_id = s.id
        LEFT JOIN modules m ON a.module_id = m.id
        LEFT JOIN lecturers l ON m.lecturer_id = l.id
        WHERE l.user_id = :user_id OR a.module_id IS NULL
        ORDER BY a.uploaded_at DESC
    ");
    
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($assignments);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>