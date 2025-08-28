<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$type = $_GET['type'] ?? 'peer';
$user_id = $_SESSION['user_id'];

try {
    $recipients = [];
    
    if ($type === 'peer') {
        // Get other students
        $stmt = $conn->prepare("
            SELECT u.id, u.username, s.stud_name as name
            FROM users u
            JOIN students s ON u.id = s.user_id
            WHERE u.role = 'student' AND u.id != :user_id
            ORDER BY s.stud_name
        ");
        $stmt->execute([':user_id' => $user_id]);
        $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } elseif ($type === 'lecturer') {
        // Get lecturers for modules the student is registered for
        $stmt = $conn->prepare("
            SELECT DISTINCT u.id, u.username, l.name
            FROM users u
            JOIN lecturers l ON u.id = l.user_id
            JOIN modules m ON l.id = m.lecturer_id
            JOIN student_modules sm ON m.id = sm.module_id
            JOIN students s ON sm.student_id = s.id
            WHERE s.user_id = :user_id
            ORDER BY l.name
        ");
        $stmt->execute([':user_id' => $user_id]);
        $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode($recipients);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>