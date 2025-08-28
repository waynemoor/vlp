<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isset($_POST['message']) || empty(trim($_POST['message']))) {
    echo json_encode(['error' => 'Message cannot be empty']);
    exit;
}

require 'db_connect.php'; // your DB connection

$stmt = $pdo->prepare("INSERT INTO announcements (lecturer_id, message) VALUES (?, ?)");
if ($stmt->execute([$_SESSION['user_id'], trim($_POST['message'])])) {
    echo json_encode(['success' => 'Announcement sent successfully']);
} else {
    echo json_encode(['error' => 'Failed to send announcement']);
}
