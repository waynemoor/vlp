<?php
session_start();
include '../db_connection.php'; // $conn is available
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stud_name  = trim($_POST['name'] ?? '');
    $stud_email = trim($_POST['email'] ?? '');
    $stud_id    = trim($_POST['student_id'] ?? '');
    $program    = trim($_POST['program'] ?? '');
    $no_carries = (int) ($_POST['no_carries'] ?? 0);

    if (!$stud_name || !$stud_email || !$stud_id || !$program || !$no_carries) {
        echo json_encode(['status'=>'error', 'message'=>'Please fill in all required fields.']);
        exit;
    }

    try {
        // MySQL upsert using ON DUPLICATE KEY
        $sql = "INSERT INTO students (stud_name, stud_email, stud_id, program, no_carries)
                VALUES (:stud_name, :stud_email, :stud_id, :program, :no_carries)
                ON DUPLICATE KEY UPDATE 
                    stud_name = VALUES(stud_name),
                    stud_email = VALUES(stud_email),
                    program = VALUES(program),
                    no_carries = VALUES(no_carries)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':stud_name'  => $stud_name,
            ':stud_email' => $stud_email,
            ':stud_id'    => $stud_id,
            ':program'    => $program,
            ':no_carries' => $no_carries
        ]);

        echo json_encode(['status'=>'success', 'message'=>"Student '{$stud_name}' added/updated successfully."]);

    } catch (PDOException $e) {
        echo json_encode(['status'=>'error', 'message'=>"Error: ".$e->getMessage()]);
    }
} else {
    echo json_encode(['status'=>'error', 'message'=>'POST request required']);
}
?>
