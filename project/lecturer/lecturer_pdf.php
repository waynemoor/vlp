<?php
session_start(); // REQUIRED to access session variables
require __DIR__ . '/../db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lecturer_id = $_POST['lecturer_id'];
    $title = $_POST['title'];

    // File handling
    $targetDir = "/lecturer_notes/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($_FILES["pdf"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName;

    // Check file type
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    if ($fileType !== "pdf") {
        die("Only PDF files are allowed.");
    }

    if (move_uploaded_file($_FILES["pdf"]["tmp_name"], $targetFilePath)) {
        try {
            $stmt = $conn->prepare("INSERT INTO lecturer_pdfs (lecturer_id, title, file_path) VALUES (:lecturer_id, :title, :file_path)");
            $stmt->execute([
                ':lecturer_id' => $_SESSION['username'],
                ':title' => $title,
                ':file_path' => $targetFilePath
            ]);

            echo json_encode(["success" => "File uploaded successfully"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Database insert failed", "details" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["error" => "File upload failed"]);
    }
}
?>
