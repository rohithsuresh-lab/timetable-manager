<?php
include 'db.php';
require_once 'send_mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacher_id = intval($_POST['teacher_id']);
    $request_id = intval($_POST['request_id']);
    $subject_id = intval($_POST['subject_id']);

    // Prevent duplicate application
    $check = $conn->prepare("SELECT id FROM substitute_request_applicants WHERE request_id = ? AND substitute_teacher_id = ?");
    $check->bind_param("ii", $request_id, $teacher_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('You have already applied for this substitution.'); window.location.href='view_free_hours.php?teacher_id=$teacher_id';</script>";
        exit;
    }

    // Insert application
    $stmt = $conn->prepare("INSERT INTO substitute_request_applicants (request_id, substitute_teacher_id, substitute_subject_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $request_id, $teacher_id, $subject_id);

    if ($stmt->execute()) {
        // Fetch email details
        $query = "
            SELECT 
                sr.date, sr.hour, sr.reason,
                c.class_name AS class_name,
                s.name AS original_subject,
                u1.name AS original_teacher,
                u2.name AS substitute_teacher,
                s2.name AS substitute_subject
            FROM substitute_requests sr
            JOIN classes c ON sr.class_id = c.class_id
            JOIN subjects s ON sr.original_subject_id = s.subject_id
            JOIN users u1 ON sr.original_teacher_id = u1.user_id
            JOIN users u2 ON u2.user_id = ?
            JOIN subjects s2 ON s2.subject_id = ?
            WHERE sr.request_id = ?
        ";

        $info_stmt = $conn->prepare($query);
        $info_stmt->bind_param("iii", $teacher_id, $subject_id, $request_id);
        $info_stmt->execute();
        $info_result = $info_stmt->get_result();

        if ($info_result->num_rows > 0) {
            $info = $info_result->fetch_assoc();

            // Email body
            $body = "
                <h3>New Substitute Application</h3>
                <p><b>Applicant:</b> {$info['substitute_teacher']}</p>
                <p><b>Substitute Subject:</b> {$info['substitute_subject']}</p>
                <p><b>Class:</b> {$info['class_name']}</p>
                <p><b>Original Teacher:</b> {$info['original_teacher']}</p>
                <p><b>Original Subject:</b> {$info['original_subject']}</p>
                <p><b>Date:</b> {$info['date']}</p>
                <p><b>Hour:</b> {$info['hour']}</p>
            ";
        } else {
            $body = "<p>A teacher has applied for a substitution, but details could not be fetched.</p>";
        }

        $recipient = "rohithsureshts2005@gmail.com";
        $subject = "Applied for Substitution";
        sendEmail($recipient, $subject, $body);

        echo "<script>alert('Applied successfully!'); window.location.href='view_free_hours.php?teacher_id=$teacher_id';</script>";
    } else {
        echo "<script>alert('Failed to apply.'); window.location.href='view_free_hours.php?teacher_id=$teacher_id';</script>";
    }
}
?>
