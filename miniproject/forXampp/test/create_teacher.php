<?php
include 'db.php';

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Not hashed as per your note

    // Check if email already exists
    $check_sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        $msg = "Error: Email is already registered!";
    } else {
        // Insert into users
        $user_sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'teacher')";
        if ($conn->query($user_sql)) {
            $user_id = $conn->insert_id;
            // Insert into teachers
            $teacher_sql = "INSERT INTO teachers (teacher_id) VALUES ($user_id)";
            if ($conn->query($teacher_sql)) {
                $msg = "Teacher account created successfully!";
            } else {
                $msg = "Error inserting into teachers: " . $conn->error;
            }
        } else {
            $msg = "Error inserting into users: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Create Teacher Account</title>
</head>
<body>
  <h2>Create Teacher Account</h2>
  <form method="POST">
    <label>Name:</label><input type="text" name="name" required><br>
    <label>Email:</label><input type="email" name="email" required><br>
    <label>Password:</label><input type="text" name="password" required><br>
    <input type="submit" value="Create">
  </form>
  <p><?= $msg ?></p>
</body>
</html>
