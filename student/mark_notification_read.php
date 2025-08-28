<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$notification_id = $_POST['notification_id'] ?? null;

if (!$notification_id) {
    echo json_encode(['error' => 'Notification ID required']);
    exit;
}

try {
    $stmt = $conn->prepare("
        UPDATE notifications 
        SET is_read = TRUE 
        WHERE id = :notification_id AND user_id = :user_id
    ");
    
    $stmt->execute([
        ':notification_id' => $notification_id,
        ':user_id' => $_SESSION['user_id']
    ]);
    
    echo json_encode(['success' => 'Notification marked as read']);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>