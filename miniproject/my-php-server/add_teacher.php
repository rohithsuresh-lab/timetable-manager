<?php
// Get input from form
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Basic validation
if (empty($username) || empty($email) || empty($password)) {
    die("All fields are required.");
}

// Hash password (optional but recommended)
$hashedPassword = $password; // Change to: password_hash($password, PASSWORD_DEFAULT); for security

// DB config
$host = 'localhost';
$dbname = 'miniproject';
$dbuser = 'root';
$dbpass = ''; // Change as needed

// Connect to DB
$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check for duplicate email or username
$checkQuery = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
$checkResult = $conn->query($checkQuery);
if ($checkResult->num_rows > 0) {
    die("Username or email already exists.");
}

// Insert teacher
$sql = "INSERT INTO users (username, email, password, role, status)
        VALUES ('$username', '$email', '$hashedPassword', 'teacher', 'approved')";

if ($conn->query($sql) === TRUE) {
    echo "Teacher added successfully. <a href='../admin-dashboard.php'>Go back</a>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
