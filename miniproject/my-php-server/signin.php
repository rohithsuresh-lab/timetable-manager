<?php
// Database config
$servername = "localhost";
$dbusername = "root";  // default XAMPP user
$dbpassword = "";      // default XAMPP password
$dbname = "login_demo";

// Connect to MySQL
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get data from form
$username = $_POST['username'];
$password = $_POST['password'];

// Prepare SQL
$sql = "SELECT * FROM users WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows === 1) {
  $row = $result->fetch_assoc();

  // Verify password
  if (password_verify($password, $row['password'])) {
    echo "Login successful! Welcome, " . htmlspecialchars($username);
  } else {
    echo "Invalid password.";
  }
} else {
  echo "No user found.";
}

$stmt->close();
$conn->close();
?>
