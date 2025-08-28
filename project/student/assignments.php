<?php
session_start();
require __DIR__ . '/../db_connection.php';

//  Only allow logged-in students
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

//  Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

//  Check file upload
if (!isset($_FILES['assignmentFile']) || $_FILES['assignmentFile']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['assignmentFile'];
$allowedExt = ['pdf', 'doc', 'docx', 'txt'];
$fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($fileExt, $allowedExt)) {
    echo json_encode(['error' => 'Invalid file type']);
    exit;
}

//  Ensure upload directory exists
$targetDir = __DIR__ . "/upload_assignments/";
if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true)) {
    echo json_encode(['error' => 'Failed to create upload directory']);
    exit;
}

//  Generate safe file name & path
$newFileName = time() . "_" . basename($file['name']);
$targetFile  = $targetDir . $newFileName;

//  Move file to target folder
if (move_uploaded_file($file['tmp_name'], $targetFile)) {
    try {
        // Relative path stored in DB (so lecturers can open via browser)
        $relativePath = "upload_assignments/" . $newFileName;

    $stmt = $conn->prepare("
    INSERT INTO assignments (student_id, filename, assignment_title, file_path, uploaded_at)
    VALUES (:student_id, :filename, :title, :file_path, NOW())
");
      $stmt->execute([
    ':student_id' => $_SESSION['user_id'],
    ':filename'   => $newFileName,
    ':title'      => $_POST['assignment_title'] ?? '',
    ':file_path'  => "upload_assignments/" . $newFileName
]);

        echo json_encode(['success' => 'File uploaded successfully']);
    } catch (PDOException $e) {
        echo json_encode([
            'error'   => 'Database insert failed',
            'details' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['error' => 'Failed to move uploaded file']);
}
?>
