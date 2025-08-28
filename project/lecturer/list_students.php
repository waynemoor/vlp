<?php
session_start();

// Only lecturers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    echo json_encode([]);
    exit;
}

// DB connection
$host = 'localhost';
$db = 'your_database';
$user = 'your_username';
$pass = 'your_password';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id, name, email, student_id, program FROM students ORDER BY name");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($students as &$student) {
        $stmtPerf = $pdo->prepare("SELECT course, assignment, score, grade FROM student_performance WHERE student_id = ?");
        $stmtPerf->execute([$student['id']]);
        $student['performance'] = $stmtPerf->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($students);

} catch (PDOException $e) {
    echo json_encode([]);
}
?>
