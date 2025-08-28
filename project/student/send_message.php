<?php
session_start();
require '../db_connection.php';
require 'Messages.php';
require 'CommunityMessages.php';

$user_id = $_SESSION['user_id'];
$type = $_POST['type'] ?? 'peer';
$receiver_id = $_POST['receiver_id'] ?? 0;
$message = $_POST['message'] ?? '';

if ($type === 'community') {
    $cm = new CommunityMessages($conn, $user_id);
    $success = $cm->sendCommunityMessage($receiver_id, $message);
} else {
    $m = new Messages($conn, $user_id);
    $success = $m->sendMessage($receiver_id, $message, $type);
}

echo json_encode(['success'=>$success]);
?>
