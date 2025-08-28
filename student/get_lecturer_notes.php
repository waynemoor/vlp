<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Get notes only for modules the student is registered for
    $stmt = $conn->prepare("
        SELECT ln.*, m.module_name, m.module_code, l.name as lecturer_name
        FROM lecturer_notes ln
        JOIN modules m ON ln.module_id = m.id
        JOIN lecturers l ON ln.lecturer_id = l.id
        JOIN student_modules sm ON m.id = sm.module_id
        JOIN students s ON sm.student_id = s.id
        WHERE s.user_id = :user_id
        ORDER BY ln.uploaded_at DESC
    ");
    
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($notes);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>