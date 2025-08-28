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

$user_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'] ?? null;
$message_text = trim($_POST['message'] ?? '');
$message_type = $_POST['type'] ?? 'peer';

if (!$receiver_id || !$message_text) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    // Verify receiver exists and has appropriate role
    $stmt = $conn->prepare("SELECT id, role FROM users WHERE id = :receiver_id");
    $stmt->execute([':receiver_id' => $receiver_id]);
    $receiver = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$receiver) {
        echo json_encode(['error' => 'Receiver not found']);
        exit;
    }
    
    // Validate message type based on receiver role
    if ($message_type === 'lecturer' && $receiver['role'] !== 'lecturer') {
        echo json_encode(['error' => 'Invalid message type for receiver']);
        exit;
    }
    
    // Insert message
    $stmt = $conn->prepare("
        INSERT INTO messages (sender_id, receiver_id, message_text, message_type)
        VALUES (:sender_id, :receiver_id, :message_text, :message_type)
    ");
    
    $stmt->execute([
        ':sender_id' => $user_id,
        ':receiver_id' => $receiver_id,
        ':message_text' => $message_text,
        ':message_type' => $message_type
    ]);
    
    // Create notification for receiver
    $notificationStmt = $conn->prepare("
        INSERT INTO notifications (user_id, title, message, type)
        VALUES (:user_id, :title, :message, 'info')
    ");
    
    $notificationStmt->execute([
        ':user_id' => $receiver_id,
        ':title' => 'New Message',
        ':message' => 'You have received a new message from ' . $_SESSION['username']
    ]);
    
    echo json_encode(['success' => 'Message sent successfully']);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>