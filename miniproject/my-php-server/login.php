<?php
$valid_email = "asd@gmail.com";
$valid_password = "123";

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($email === $valid_email && $password === $valid_password) {
    echo "<h2>Login successful!</h2>";
    echo "<p>Welcome, $email</p>";
} else {
    echo "<h2>Login failed!</h2>";
    echo "<p>Invalid email or password.</p>";
}
?>
