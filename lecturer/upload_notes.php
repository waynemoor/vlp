<?php
session_start();
require '../db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$title = trim($_POST['title'] ?? '');
$module_id = $_POST['module_id'] ?? null;

if (!$title || !$module_id || !isset($_FILES['pdf_file'])) {
    echo json_encode(['error' => 'Title, module, and PDF file are required']);
    exit;
}

$file = $_FILES['pdf_file'];

// Validate file
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'File upload error']);
    exit;
}

$allowed_types = ['application/pdf'];
$file_info = finfo_open(FILEINFO_MIME_TYPE);
$file_type = finfo_file($file_info, $file['tmp_name']);
finfo_close($file_info);

if (!in_array($file_type, $allowed_types)) {
    echo json_encode(['error' => 'Only PDF files are allowed']);
    exit;
}

try {
    // Get lecturer ID and verify module ownership
    $lecturerStmt = $conn->prepare("
        SELECT l.id
        FROM lecturers l
        JOIN modules m ON l.id = m.lecturer_id
        WHERE l.user_id = :user_id AND m.id = :module_id
    ");
    
    $lecturerStmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':module_id' => $module_id
    ]);
    
    $lecturer = $lecturerStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$lecturer) {
        echo json_encode(['error' => 'Module not found or access denied']);
        exit;
    }
    
    // Create upload directory if it doesn't exist
    $upload_dir = '../uploads/lecturer_notes/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $file_path = $upload_dir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        echo json_encode(['error' => 'Failed to save file']);
        exit;
    }
    
    // Save to database
    $stmt = $conn->prepare("
        INSERT INTO lecturer_notes (lecturer_id, module_id, title, file_path, file_size)
        VALUES (:lecturer_id, :module_id, :title, :file_path, :file_size)
    ");
    
    $stmt->execute([
        ':lecturer_id' => $lecturer['id'],
        ':module_id' => $module_id,
        ':title' => $title,
        ':file_path' => 'uploads/lecturer_notes/' . $filename,
        ':file_size' => $file['size']
    ]);
    
    // Notify students registered for this module
    $notifyStmt = $conn->prepare("
        INSERT INTO notifications (user_id, title, message, type)
        SELECT s.user_id, 'New Notes Available', 
               CONCAT('New notes \"', :title, '\" have been uploaded for ', m.module_name), 'info'
        FROM student_modules sm
        JOIN students s ON sm.student_id = s.id
        JOIN modules m ON sm.module_id = m.id
        WHERE sm.module_id = :module_id
    ");
    
    $notifyStmt->execute([
        ':title' => $title,
        ':module_id' => $module_id
    ]);
    
    echo json_encode(['success' => 'Notes uploaded successfully']);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>