<?php
$host = "localhost";
$db = "vlp";
$user = "root";     // or your MySQL username
$pass = "";         // or your MySQL password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
    $conn = null; //handle error appropriately
}
?>
