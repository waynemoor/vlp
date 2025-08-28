<?php
require 'db_connection.php'; // Make sure $conn is your PDO connection

$users = [
    ['username' => 'T2274805V', 'password' => 'qwerty@123', 'role' => 'student'],
  //  ['username' => 'lecturer1', 'password' => 'lecturer@123', 'role' => 'lecturer'],
 //   ['username' => 'student1', 'password' => 'Student@123', 'role' => 'student']
];

foreach ($users as $user) {
    // Hash the password
    $passwordHash = password_hash($user['password'], PASSWORD_DEFAULT);

    // Prepare insert
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
    $stmt->bindParam(':username', $user['username']);
    $stmt->bindParam(':password', $passwordHash);
    $stmt->bindParam(':role', $user['role']);

    $stmt->execute();
}

echo "Users inserted successfully!";
//adminuser Admin@123
//lecturer1 Lecturer@123
//student1 student@123


?>
