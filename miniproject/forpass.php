<?php
// Database config
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "login_demo"; // your database name


// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get email from form
$email = $_POST['email'];

// Check if user exists
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  // ✅ Email exists — normally generate a token & send email
  echo "A password reset link has been sent to your email.";
  
  // Example (not sending real mail here)
  // $token = bin2hex(random_bytes(50));
  // Save $token in DB, send link with token as GET param
  
} else {
  echo "No user found with that email.";
}

$stmt->close();
$conn->close();
?>
