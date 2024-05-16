<?php

// Establish connection to MySQL database
$hostname = "localhost"; // Change to your MySQL server hostname
$username = "root"; // Change to your MySQL username
$password = "88vdmC6yawFPHf1"; // Change to your MySQL password
$database_name = "final_project"; // Change to your database name

$conn = new mysqli($hostname, $username, $password, $database_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}  