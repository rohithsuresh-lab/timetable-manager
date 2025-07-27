<?php
include 'db.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password']; // Use hashing if you later add sessions
$role = 'student';

// Check if email already exists
$check = $conn->prepare("SELECT * FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('Email already registered'); window.location='register.php';</script>";
    exit;
}

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $password, $role);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;
    $null = null;
    $insertStudent = $conn->prepare("INSERT INTO students (student_id, class_id, is_approved) VALUES (?, ?, 0)");
    $insertStudent->bind_param("ii", $user_id, $null);
    $insertStudent->execute();

    echo "<script>alert('Signup successful! Please login.'); window.location='index.php';</script>";
} else {
    echo "<script>alert('Signup failed.'); window.location='register.php';</script>";
}
?>
