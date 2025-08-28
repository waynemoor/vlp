<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'] ?? null;
$message_type = $_GET['type'] ?? 'peer';

if (!$receiver_id) {
    echo json_encode([]);
    exit;
}

try {
    // Get messages between current user and selected receiver
    $stmt = $conn->prepare("
        SELECT m.*, 
               sender.username as sender_name,
               receiver.username as receiver_name
        FROM messages m
        JOIN users sender ON m.sender_id = sender.id
        JOIN users receiver ON m.receiver_id = receiver.id
        WHERE ((m.sender_id = :user_id AND m.receiver_id = :receiver_id) 
               OR (m.sender_id = :receiver_id AND m.receiver_id = :user_id))
          AND m.message_type = :type
        ORDER BY m.sent_at ASC
    ");
    
    $stmt->execute([
        ':user_id' => $user_id,
        ':receiver_id' => $receiver_id,
        ':type' => $message_type
    ]);
    
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Mark messages as read
    $updateStmt = $conn->prepare("
        UPDATE messages 
        SET is_read = TRUE 
        WHERE receiver_id = :user_id AND sender_id = :sender_id AND is_read = FALSE
    ");
    $updateStmt->execute([
        ':user_id' => $user_id,
        ':sender_id' => $receiver_id
    ]);
    
    echo json_encode($messages);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>