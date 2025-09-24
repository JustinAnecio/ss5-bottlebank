<?php
$host = "localhost";
$user = "root";      // default XAMPP user
$pass = "";          // default XAMPP has no password
$db   = "bottlebankdb"; // your database name

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
