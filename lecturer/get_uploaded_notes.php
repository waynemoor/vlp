<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT ln.*, m.module_name, m.module_code
        FROM lecturer_notes ln
        JOIN modules m ON ln.module_id = m.id
        JOIN lecturers l ON ln.lecturer_id = l.id
        WHERE l.user_id = :user_id
        ORDER BY ln.uploaded_at DESC
    ");
    
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($notes);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>