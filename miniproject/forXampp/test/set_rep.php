<?php
include 'db.php';
$msg = "";

// SET new class rep
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['set_rep']) && isset($_POST['class_id']) && isset($_POST['student_id'])) {
        $class_id = $_POST['class_id'];
        $student_id = $_POST['student_id'];

        // Unset existing rep
        $conn->query("UPDATE students SET is_rep = 0 WHERE class_id = $class_id");

        // Set new rep
        $sql = "UPDATE students SET is_rep = 1 WHERE student_id = $student_id";
        $msg = $conn->query($sql) ? "Student set as class rep." : "Error: " . $conn->error;
    }

    // REMOVE class rep
    if (isset($_POST['remove_rep']) && isset($_POST['student_id'])) {
        $student_id = $_POST['student_id'];
        $sql = "UPDATE students SET is_rep = 0 WHERE student_id = $student_id";
        $msg = $conn->query($sql) ? "Class rep removed." : "Error: " . $conn->error;
    }
}

// Fetch all classes
$classes = $conn->query("SELECT * FROM classes");

// If a class is selected, fetch students in that class
$class_students = [];
if (isset($_GET['class_id'])) {
    $selected_class_id = $_GET['class_id'];
    $stmt = $conn->prepare("SELECT u.user_id, u.email FROM users u JOIN students s ON u.user_id = s.student_id WHERE s.class_id = ?");
    $stmt->bind_param("i", $selected_class_id);
    $stmt->execute();
    $class_students = $stmt->get_result();
}

// Fetch current reps
$current_reps = $conn->query("
    SELECT c.class_name, u.user_id, u.email, s.class_id
    FROM students s
    JOIN users u ON s.student_id = u.user_id
    JOIN classes c ON s.class_id = c.class_id
    WHERE s.is_rep = 1
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Class Representatives</title>
</head>
<body>
    <h2>Set Class Representative</h2>

    <!-- Select Class -->
    <form method="GET">
        <label>Select Class:</label>
        <select name="class_id" onchange="this.form.submit()" required>
            <option value="">--Select Class--</option>
            <?php while ($row = $classes->fetch_assoc()): ?>
                <option value="<?= $row['class_id'] ?>" <?= (isset($selected_class_id) && $row['class_id'] == $selected_class_id) ? "selected" : "" ?>>
                    <?= $row['class_name'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <!-- Set Rep Form -->
    <?php if (!empty($class_students) && $class_students->num_rows > 0): ?>
        <form method="POST">
            <input type="hidden" name="class_id" value="<?= $selected_class_id ?>">
            <label>Select Student:</label>
            <select name="student_id" required>
                <?php while ($row = $class_students->fetch_assoc()): ?>
                    <option value="<?= $row['user_id'] ?>"><?= $row['email'] ?></option>
                <?php endwhile; ?>
            </select><br>
            <button type="submit" name="set_rep">Set as Rep</button>
        </form>
    <?php elseif (isset($selected_class_id)): ?>
        <p>No students in selected class.</p>
    <?php endif; ?>

    <p><?= $msg ?></p>

    <hr>

    <h2>Current Class Representatives</h2>
    <?php if ($current_reps->num_rows > 0): ?>
        <table border="1" cellpadding="5">
            <tr>
                <th>Class</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
            <?php while ($rep = $current_reps->fetch_assoc()): ?>
                <tr>
                    <td><?= $rep['class_name'] ?></td>
                    <td><?= $rep['email'] ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="student_id" value="<?= $rep['user_id'] ?>">
                            <button type="submit" name="remove_rep" onclick="return confirm('Remove this class rep?')">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No reps set yet.</p>
    <?php endif; ?>
	<script>
  // Redirect if referrer is not dashboard.php and user tries to leave this page
  if (document.referrer && !document.referrer.includes("dashboard_admin.php")) {
    window.addEventListener("pageshow", function (event) {
      if (event.persisted || performance.getEntriesByType("navigation")[0].type === "back_forward") {
        window.location.href = "dashboard_admin.php";
      }
    });
  }
</script>
</body>
</html>
