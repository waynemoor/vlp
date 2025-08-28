<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$table_name = trim($_POST['table_name'] ?? '');
$columns = $_POST['columns'] ?? [];

if (!$table_name || empty($columns)) {
    echo json_encode(['error' => 'Table name and columns are required']);
    exit;
}

// Validate table name (alphanumeric and underscores only)
if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table_name)) {
    echo json_encode(['error' => 'Invalid table name']);
    exit;
}

try {
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (";
    $column_definitions = [];
    
    foreach ($columns as $column) {
        $col_name = trim($column['name'] ?? '');
        $col_type = trim($column['type'] ?? '');
        $col_constraints = trim($column['constraints'] ?? '');
        
        if (!$col_name || !$col_type) {
            continue;
        }
        
        // Validate column name
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $col_name)) {
            echo json_encode(['error' => "Invalid column name: $col_name"]);
            exit;
        }
        
        // Validate column type
        $allowed_types = [
            'INTEGER', 'SERIAL', 'BIGINT', 'BIGSERIAL',
            'VARCHAR', 'TEXT', 'CHAR',
            'BOOLEAN', 'DATE', 'TIMESTAMP', 'TIME',
            'DECIMAL', 'NUMERIC', 'REAL', 'DOUBLE PRECISION'
        ];
        
        $type_valid = false;
        foreach ($allowed_types as $allowed_type) {
            if (stripos($col_type, $allowed_type) === 0) {
                $type_valid = true;
                break;
            }
        }
        
        if (!$type_valid) {
            echo json_encode(['error' => "Invalid column type: $col_type"]);
            exit;
        }
        
        $column_def = "$col_name $col_type";
        if ($col_constraints) {
            $column_def .= " $col_constraints";
        }
        
        $column_definitions[] = $column_def;
    }
    
    if (empty($column_definitions)) {
        echo json_encode(['error' => 'No valid columns provided']);
        exit;
    }
    
    $sql .= implode(', ', $column_definitions) . ')';
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    echo json_encode(['success' => "Table '$table_name' created successfully"]);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>