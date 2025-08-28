<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$table_name = $_GET['table_name'] ?? '';

if (!$table_name) {
    echo json_encode(['error' => 'Table name is required']);
    exit;
}

// Validate table name
if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table_name)) {
    echo json_encode(['error' => 'Invalid table name']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT column_name, data_type, is_nullable, column_default,
               character_maximum_length, numeric_precision, numeric_scale
        FROM information_schema.columns
        WHERE table_schema = 'public' AND table_name = :table_name
        ORDER BY ordinal_position
    ");
    
    $stmt->execute([':table_name' => $table_name]);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($columns)) {
        echo json_encode(['error' => 'Table not found']);
        exit;
    }
    
    echo json_encode([
        'table_name' => $table_name,
        'columns' => $columns
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>