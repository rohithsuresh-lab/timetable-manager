
<?php
// Get form data from POST
$username = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Database credentials
$host = 'localhost';
$dbname = 'mini_project';
$dbuser = 'root';
$dbpass = '123'; // Change this if you set a root password

// Connect to MySQL
$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Escape input to avoid SQL injection
$username = $conn->real_escape_string($username);
$password = $conn->real_escape_string($password);

// Query the login table
$sql = "SELECT * FROM login WHERE username = '$username' AND password = '$password'";
$result = $conn->query($sql);

// Check if user exists
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $role = $user['role'];

    // Redirect based on role
    if ($role === 'admin') {
        header("Location: admin-dashboard.php");
    } elseif ($role === 'teacher') {
        header("Location: teacher-dashboard.php");
    } elseif ($role === 'student') {
        header("Location: student-dashboard.php");
    } else {
        echo "Unknown role!";
    }
    exit;
} else {
    echo "Invalid username or password.";
}

$conn->close();
?>
