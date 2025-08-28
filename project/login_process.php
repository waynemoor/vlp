<?php
session_start();
require 'db_connection.php'; // PDO connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        header("Location: login.php?error=empty");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Save session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect to dashboard by role
        if ($user['role'] === 'admin') {
            header("Location: admin/admin_dashboard.php");
        } elseif ($user['role'] === 'lecturer') {
            header("Location: lecturer/lecturer_dashboard.php");
        } else {
            header("Location: student/student_dashboard.php");
        }
        exit();
    } else {
        // Invalid credentials
        header("Location: login.php?error=invalid");
        exit();
    }
} else {
    // If accessed directly, go back to login
    header("Location: login.php");
    exit();
}
?>
