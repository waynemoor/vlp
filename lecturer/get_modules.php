<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Get lecturer's modules
    $stmt = $conn->prepare("
        SELECT m.*
        FROM modules m
        JOIN lecturers l ON m.lecturer_id = l.id
        WHERE l.user_id = :user_id
        ORDER BY m.module_name
    ");
    
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($modules);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>