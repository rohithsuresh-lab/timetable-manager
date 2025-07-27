<?php
include 'db.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_email = $_POST['student_email'];
    $class_name = $_POST['class_name'];

    // Get student ID from email
    $student_query = $conn->prepare("SELECT u.user_id FROM users u JOIN students s ON u.user_id = s.student_id WHERE u.email = ?");
    $student_query->bind_param("s", $student_email);
    $student_query->execute();
    $student_result = $student_query->get_result();
    $student_row = $student_result->fetch_assoc();
    $student_id = $student_row['user_id'] ?? null;

    // Get class ID from class name
    $class_query = $conn->prepare("SELECT class_id FROM classes WHERE class_name = ?");
    $class_query->bind_param("s", $class_name);
    $class_query->execute();
    $class_result = $class_query->get_result();
    $class_row = $class_result->fetch_assoc();
    $class_id = $class_row['class_id'] ?? null;

    if ($student_id && $class_id) {
        $sql = "UPDATE students SET class_id = $class_id WHERE student_id = $student_id";
        $msg = $conn->query($sql) ? "Student assigned successfully." : "Error: " . $conn->error;
    } else {
        $msg = "Invalid student email or class name.";
    }
}

// For populating datalists
$students = $conn->query("SELECT u.email FROM users u JOIN students s ON u.user_id = s.student_id");
$classes = $conn->query("SELECT class_name FROM classes");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Assign Student to Class</title>
</head>
<body>
  <h2>Assign Student to Class</h2>
  <form method="POST">
    <label>Search Student (Email):</label>
    <input list="students" name="student_email" required>
    <datalist id="students">
      <?php while ($row = $students->fetch_assoc()) echo "<option value='{$row['email']}'>"; ?>
    </datalist>
    <br>

    <label>Search Class (Name):</label>
    <input list="classes" name="class_name" required>
    <datalist id="classes">
      <?php while ($row = $classes->fetch_assoc()) echo "<option value='{$row['class_name']}'>"; ?>
    </datalist>
    <br>

    <input type="submit" value="Assign">
  </form>
  <p><?= $msg ?></p>
</body>
</html>
