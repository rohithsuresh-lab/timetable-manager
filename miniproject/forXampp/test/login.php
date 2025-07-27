<?php
include 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT u.*, s.is_approved 
        FROM users u 
        LEFT JOIN students s ON u.user_id = s.student_id 
        WHERE u.email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    if ($password === $user['password']) {
        if ($user['role'] == 'student' && $user['is_approved'] != 1) {
            echo "<script>
                alert('Your account is not yet approved by the admin.');
                window.location.href = 'unapproved_student.php';
            </script>";
            exit;
        }

        // Redirect to role-specific dashboard
        if ($user['role'] == 'admin') {
            header("Location: dashboard_admin.php");
        } else if ($user['role'] == 'teacher') {
			$user_id = $user['user_id'];
            header("Location: dashboard_teacher.php?teacher_id=$user_id");
        } else if ($user['role'] == 'student') {
            header("Location: dashboard_student.php");
        }
        exit;
    } else {
        echo "<script>alert('Incorrect password'); window.location.href='index.php';</script>";
    }
} else {
    echo "<script>alert('User not found'); window.location.href='index.php';</script>";
}
?>
