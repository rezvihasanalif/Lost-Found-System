<?php
// Database connection without selecting a specific database first
$host = "localhost";
$user = "root";
$pass = "";

// Connect without specifying a database
$conn = mysqli_connect($host, $user, $pass);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create the database
$sql = "CREATE DATABASE IF NOT EXISTS project_db";
if (mysqli_query($conn, $sql)) {
    echo "Database 'project_db' created successfully or already exists.<br>";
} else {
    echo "Error creating database: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
