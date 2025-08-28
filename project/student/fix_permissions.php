<?php
$dir = __DIR__ . "/upload_assignments";

if (!is_dir($dir)) {
    mkdir($dir, 0777, true);  // creates with full write permissions
    echo "Folder created with permissions 0777";
} else {
    chmod($dir, 0777);
    echo "Folder already exists, permissions changed to 0777";
}
?>
