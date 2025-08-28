<?php
session_start();
include '../db_connection.php'; // $conn is available
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form data safely
    $name        = trim($_POST['name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $employee_id = trim($_POST['employee_id'] ?? '');
    $department  = trim($_POST['department'] ?? '');

    // Validate required fields
    if (!$name || !$email || !$employee_id || !$department) {
        echo json_encode(['status'=>'error', 'message'=>'Please fill in all required fields.']);
        exit;
    }

    try {
        // MySQL upsert (add or update if employee_id exists)
        $sql = "INSERT INTO lecturers (name, email, employee_id, department)
                VALUES (:name, :email, :employee_id, :department)
                ON DUPLICATE KEY UPDATE
                    name = VALUES(name),
                    email = VALUES(email),
                    department = VALUES(department)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name'        => $name,
            ':email'       => $email,
            ':employee_id' => $employee_id,
            ':department'  => $department
        ]);

        echo json_encode([
            'status' => 'success',
            'message' => "Lecturer '{$name}' added/updated successfully."
        ]);

    } catch (PDOException $e) {
        echo json_encode(['status'=>'error', 'message'=>"Database Error: " . $e->getMessage()]);
    }

} else {
    echo json_encode(['status'=>'error', 'message'=>'POST request required']);
}
?>
