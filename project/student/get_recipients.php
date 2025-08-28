<?php
session_start();
require '../db_connection.php';

$type = $_GET['type'] ?? 'peer';
$user_id = $_SESSION['user_id'];

$recipients = [];

if ($type === 'peer') {
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE role='student' AND id != :id");
    $stmt->execute([':id' => $user_id]);
    $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($type === 'lecturer') {
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE role='lecturer'");
    $stmt->execute();
    $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($type === 'community') {
    // Assuming a table student_communities(user_id, community_id, community_name)
    $stmt = $conn->prepare("SELECT community_id as id, community_name as name FROM student_communities WHERE user_id = :id");
    $stmt->execute([':id' => $user_id]);
    $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($recipients);
?>
