<?php
session_start();
require '../db_connection.php';
require 'Messages.php';
require 'CommunityMessages.php';

$user_id = $_SESSION['user_id'];
$type = $_GET['type'] ?? 'peer';
$receiver_id = $_GET['receiver_id'] ?? 0;

if ($type === 'community') {
    $cm = new CommunityMessages($conn, $user_id);
    $msgs = $cm->fetchCommunityMessages($receiver_id);
} else {
    $m = new Messages($conn, $user_id);
    $msgs = $m->fetchMessages($receiver_id, $type);
    // Add sender name
    foreach ($msgs as &$msg) {
        $stmt = $conn->prepare("SELECT name FROM users WHERE id = :id");
        $stmt->execute([':id'=>$msg['sender_id']]);
        $msg['sender_name'] = $stmt->fetchColumn();
    }
}

echo json_encode($msgs);
?>
