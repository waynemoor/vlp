<?php
header('Content-Type: application/json');
require 'db_connect.php';

$stmt = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC");
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($announcements);
