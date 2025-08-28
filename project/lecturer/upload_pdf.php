<?php
session_start();

// Only allow lecturers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (isset($_FILES['pdfFile']) && $_FILES['pdfFile']['error'] === UPLOAD_ERR_OK) {

    $uploadDir = '../lecturer_notes/'; // adjust path relative to this file
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileTmpPath = $_FILES['pdfFile']['tmp_name'];
    $fileName = basename($_FILES['pdfFile']['name']);
    $targetFilePath = $uploadDir . $fileName;

    // Optional: rename file to prevent overwrite
    $counter = 1;
    $originalFileName = pathinfo($fileName, PATHINFO_FILENAME);
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
    while (file_exists($targetFilePath)) {
        $fileName = $originalFileName . "_$counter." . $extension;
        $targetFilePath = $uploadDir . $fileName;
        $counter++;
    }

    if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
        echo json_encode(['success' => 'File uploaded successfully: ' . $fileName]);
    } else {
        echo json_encode(['error' => 'Error moving the uploaded file']);
    }

} else {
    echo json_encode(['error' => 'No file uploaded or upload error']);
}
?>
