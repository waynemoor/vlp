<?php
// admin_overview.php
header('Content-Type: application/json');
include 'db_connect.php';

$totals = [
    'total_students' => $conn->query("SELECT COUNT(*) FROM students")->fetchColumn(),
    'total_lecturers' => $conn->query("SELECT COUNT(*) FROM lecturers")->fetchColumn(),
    'total_assignments' => $conn->query("SELECT COUNT(*) FROM assignments")->fetchColumn(),
    'total_announcements' => $conn->query("SELECT COUNT(*) FROM announcements")->fetchColumn()
];

echo json_encode($totals);
