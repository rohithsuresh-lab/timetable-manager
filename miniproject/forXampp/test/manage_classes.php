<?php
include 'db.php';

$msg = "";

// Handle student deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_student'])) {
    $student_id = $_POST['student_id'];

    // Delete from students table
    $conn->query("DELETE FROM students WHERE student_id = $student_id");

    // Delete from users table
    $conn->query("DELETE FROM users WHERE user_id = $student_id");

    $msg = "Student account deleted successfully.";
}

// Get list of all classes
$classes = $conn->query("SELECT * FROM classes");
$selected_class_id = $_GET['class_id'] ?? null;
$students = [];

if ($selected_class_id) {
    $stmt = $conn->prepare("
        SELECT u.user_id, u.name, u.email 
        FROM students s
        JOIN users u ON s.student_id = u.user_id
        WHERE s.class_id = ?
    ");
    $stmt->bind_param("i", $selected_class_id);
    $stmt->execute();
    $students = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Classes - Student View</title>
</head>
<body>
    <h2>Manage Classes</h2>

    <?php if ($msg): ?>
        <p style="color: green;"><?= $msg ?></p>
    <?php endif; ?>

    <form method="GET">
        <label>Select Class:</label>
        <select name="class_id" onchange="this.form.submit()" required>
            <option value="">-- Select Class --</option>
            <?php while ($row = $classes->fetch_assoc()): ?>
                <option value="<?= $row['class_id'] ?>" <?= ($selected_class_id == $row['class_id']) ? 'selected' : '' ?>>
                    <?= $row['class_name'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($selected_class_id): ?>
        <hr>
        <h3>Total Students: <?= $students->num_rows ?></h3>
        <ul>
            <?php while ($s = $students->fetch_assoc()): ?>
                <li>
                    <?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['email']) ?>)
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this student?');">
                        <input type="hidden" name="student_id" value="<?= $s['user_id'] ?>">
                        <button type="submit" name="delete_student">Delete</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
