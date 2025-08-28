<?php
session_start();
require 'db_connection.php'; // PDO connection

// Only allow lecturers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    echo json_encode([]);
    exit;
}

// Path to the student uploads folder
$uploadDir = '../student/upload_assignment/'; 
$filesList = [];

if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $filesList[] = $file;
    }
}

echo json_encode($filesList);
?>
