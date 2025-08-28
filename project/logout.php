<?php

require 'db_connection.php'; // PDO connection
session_start();
session_unset();
session_destroy();
header("Location: project/login.php");
exit();
?>
