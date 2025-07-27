<?php
include 'db.php';
require_once 'send_mail.php';

$teacher_id = $_POST['teacher_id'];
$leave_type = $_POST['leave_type']; // 'full' or 'hour'
$leave_date = $_POST['leave_date'];
$hour = isset($_POST['hour']) ? $_POST['hour'] : null;
$reason = $_POST['reason'] ?? 'No reason provided';

// Prevent duplicate leave
if ($leave_type === 'full') {
    $check = $conn->prepare("SELECT * FROM teacher_leaves WHERE teacher_id = ? AND date = ?");
    $check->bind_param("is", $teacher_id, $leave_date);
} else {
    $check = $conn->prepare("SELECT * FROM teacher_leaves WHERE teacher_id = ? AND date = ? AND hour = ?");
    $check->bind_param("isi", $teacher_id, $leave_date, $hour);
}
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('Leave already applied for this date/hour.'); window.location.href = 'dashboard_teacher.php?teacher_id=$teacher_id';</script>";
    exit;
}
$check->close();

// Insert into teacher_leaves
$nullHour = ($leave_type === 'full') ? null : $hour;
$stmt = $conn->prepare("INSERT INTO teacher_leaves (teacher_id, date, hour, reason) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isis", $teacher_id, $leave_date, $nullHour, $reason);
$stmt->execute();
$stmt->close();

// Determine day of week
$dayOfWeek = date('D', strtotime($leave_date)); // e.g. 'Mon'

// Handle substitute_requests
if ($leave_type === 'full') {
    $sql = "SELECT t.class_id, t.subject_id, t.hour FROM timetable t
            JOIN subjects s ON t.subject_id = s.subject_id
            WHERE s.teacher_id = ? AND t.day_of_week = ?";
    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param("is", $teacher_id, $dayOfWeek);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    while ($row = $result2->fetch_assoc()) {
        $class_id = $row['class_id'];
        $subject_id = $row['subject_id'];
        $hour = $row['hour'];

        // Prevent duplicate substitute entry
        $checkSub = $conn->prepare("SELECT * FROM substitute_requests WHERE original_teacher_id = ? AND class_id = ? AND date = ? AND hour = ? AND original_subject_id = ?");
        $checkSub->bind_param("iisii", $teacher_id, $class_id, $leave_date, $hour, $subject_id);
        $checkSub->execute();
        $exists = $checkSub->get_result();
        if ($exists->num_rows == 0) {
            $insertSub = $conn->prepare("INSERT INTO substitute_requests (original_teacher_id, class_id, date, hour, original_subject_id, reason) VALUES (?, ?, ?, ?, ?, ?)");
            $insertSub->bind_param("iisiss", $teacher_id, $class_id, $leave_date, $hour, $subject_id, $reason);
            $insertSub->execute();
			$teacherName = "Unknown Teacher";
			$tq = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
			$tq->bind_param("i", $teacher_id);
			$tq->execute();
			$tq->bind_result($teacherName);
			$tq->fetch();
			$tq->close();

			// Get subject name
			$subjectName = "Unknown Subject";
			$sq = $conn->prepare("SELECT name FROM subjects WHERE subject_id = ?");
			$sq->bind_param("i", $subject_id);
			$sq->execute();
			$sq->bind_result($subjectName);
			$sq->fetch();
			$sq->close();
			$recipient = "rohithsureshts2005@gmail.com";
			$subject = "New Substitution Request Raised";

			$hourText = is_null($hour) ? "Full Day" : "Hour $hour";
			$body = "<b>Substitution required for <u>$subjectName</u> on $leave_date ($hourText) for Sem $class_id.</b><br>Requested due to leave by <u>$teacherName</u>.";
			sendEmail($recipient, $subject, $body);

            $insertSub->close();
        }
        $checkSub->close();
    }

    $stmt2->close();
} else {
    // Hour-based leave
    $sql = "SELECT t.class_id, t.subject_id FROM timetable t
            JOIN subjects s ON t.subject_id = s.subject_id
            WHERE s.teacher_id = ? AND t.day_of_week = ? AND t.hour = ?";
    $stmt3 = $conn->prepare($sql);
    $stmt3->bind_param("isi", $teacher_id, $dayOfWeek, $hour);
    $stmt3->execute();
    $result3 = $stmt3->get_result();

    while ($row = $result3->fetch_assoc()) {
        $class_id = $row['class_id'];
        $subject_id = $row['subject_id'];

        // Prevent duplicate substitute entry
        $checkSub = $conn->prepare("SELECT * FROM substitute_requests WHERE original_teacher_id = ? AND class_id = ? AND date = ? AND hour = ? AND original_subject_id = ?");
        $checkSub->bind_param("iisii", $teacher_id, $class_id, $leave_date, $hour, $subject_id);
        $checkSub->execute();
        $exists = $checkSub->get_result();
        if ($exists->num_rows == 0) {
            $insertSub = $conn->prepare("INSERT INTO substitute_requests (original_teacher_id, class_id, date, hour, original_subject_id, reason) VALUES (?, ?, ?, ?, ?, ?)");
            $insertSub->bind_param("iisiss", $teacher_id, $class_id, $leave_date, $hour, $subject_id, $reason);
            $insertSub->execute();
			$teacherName = "Unknown Teacher";
			$tq = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
			$tq->bind_param("i", $teacher_id);
			$tq->execute();
			$tq->bind_result($teacherName);
			$tq->fetch();
			$tq->close();

			// Get subject name
			$subjectName = "Unknown Subject";
			$sq = $conn->prepare("SELECT name FROM subjects WHERE subject_id = ?");
			$sq->bind_param("i", $subject_id);
			$sq->execute();
			$sq->bind_result($subjectName);
			$sq->fetch();
			$sq->close();
			$recipient = "rohithsureshts2005@gmail.com";
			$subject = "New Substitution Request Raised";

			$hourText = is_null($hour) ? "Full Day" : "Hour $hour";
			$body = "<b>Substitution required for <u>$subjectName</u> on $leave_date ($hourText) for Sem ID $class_id.</b><br>Requested due to leave by <u>$teacherName</u>.";
			sendEmail($recipient, $subject, $body);

            $insertSub->close();
        }
        $checkSub->close();
    }

    $stmt3->close();
}

echo "<script>alert('Leave applied and substitution requested.'); window.location.href = 'dashboard_teacher.php?teacher_id=$teacher_id';</script>";
?>