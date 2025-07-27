<?php
include 'db.php';

$msg = "";

// Handle approval
if (isset($_GET['approve'])) {
    $student_id = intval($_GET['approve']);
    $sql = "UPDATE students SET is_approved = 1 WHERE student_id = $student_id";
    if ($conn->query($sql)) {
        $msg = "Student approved successfully.";
		echo "<script>window.location='dashboard_admin.php';</script>";
    } else {
        $msg = "Error: " . $conn->error;
		echo "<script>window.location='dashboard_admin.php';</script>";
    }
}

// Fetch unapproved students
$sql = "SELECT u.user_id, u.email, s.class_id 
        FROM users u 
        JOIN students s ON u.user_id = s.student_id 
        WHERE s.is_approved = 0";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Students</title>
</head>
<body>
    <h2>Unapproved Students</h2>
    <?php if ($msg): ?>
        <p><?= $msg ?></p>
    <?php endif; ?>

    <table border="1" cellpadding="5">
        <tr>
            <th>Student ID</th>
            <th>Email</th>
            <th>Class ID</th>
            <th>Action</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['user_id'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['class_id'] ?: 'Not assigned' ?></td>
                    <td><a href="?approve=<?= $row['user_id'] ?>" onclick="return confirm('Approve this student?')">Approve</a></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No unapproved students found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
