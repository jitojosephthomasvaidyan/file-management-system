<?php
$host = 'localhost';
$user = 'root';
$password = ''; // Set your MySQL password
$dbname = 'file_management';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
