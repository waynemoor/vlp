<?php
session_start();
require __DIR__ . '/../db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $newTitle = $_POST['title'] ?? null;

    if ($id && $newTitle) {
        $stmt = $conn->prepare("UPDATE assignment SET assignment_title = :title WHERE id = :id");
        $stmt->execute([':title' => $newTitle, ':id' => $id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Invalid request']);
    }
}
