<?php
// Connection
$conn = new mysqli("localhost", "root", "", "user_auth");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['fullname'];
    $email    = $_POST['email'];
    $pass     = $_POST['password'];
    $confPass = $_POST['confirm_password'];

    if ($pass === $confPass) {
        $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);
        
        if ($stmt->execute()) {
            echo "<script>alert('Signup successful!'); window.location='login.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "<script>alert('Passwords do not match');</script>";
    }
}
?>