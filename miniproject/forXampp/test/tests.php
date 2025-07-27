<?php
$host = "localhost";
$dbname = "user_auth";
$username = "root"; // default XAMPP user
$password = "";     // default XAMPP has no password

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
} else {
    echo "✅ Connected successfully to database.";
}
?>
