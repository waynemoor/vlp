<?php

session_start();
require 'db_connection.php'; // PDO connection

header('Content-Type: application/json');
$sql="select * from students";
$result=mysqli_query($data,$sql);

?>