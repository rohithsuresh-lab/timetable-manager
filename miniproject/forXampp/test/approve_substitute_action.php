<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applicant_id'])) {
    $applicant_id = intval($_POST['applicant_id']);

    // Step 1: Get applicant & related request info
    $sql = "SELECT a.request_id, a.substitute_teacher_id, a.substitute_subject_id, 
                   r.class_id, r.date, r.hour, r.original_teacher_id
            FROM substitute_request_applicants a
            JOIN substitute_requests r ON a.request_id = r.request_id
            WHERE a.id = ? AND a.status = 'pending' AND r.status = 'pending'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $applicant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Step 2: Approve the selected applicant
        $conn->begin_transaction();

        // Mark this applicant approved
        $stmt1 = $conn->prepare("UPDATE substitute_request_applicants SET status = 'approved' WHERE id = ?");
        $stmt1->bind_param("i", $applicant_id);
        $stmt1->execute();

        // Reject other applicants for same request
        $stmt2 = $conn->prepare("UPDATE substitute_request_applicants SET status = 'rejected' WHERE request_id = ? AND id != ?");
        $stmt2->bind_param("ii", $row['request_id'], $applicant_id);
        $stmt2->execute();

        // Update main substitute_requests table
        $stmt3 = $conn->prepare("UPDATE substitute_requests SET status = 'approved', substitute_teacher_id = ?, substitute_subject_id = ? WHERE request_id = ?");
        $stmt3->bind_param("iii", $row['substitute_teacher_id'], $row['substitute_subject_id'], $row['request_id']);
        $stmt3->execute();

        // Insert into timetable_overrides
        $stmt4 = $conn->prepare("INSERT INTO timetable_overrides (class_id, date, hour, original_teacher_id, substitute_teacher_id, subject_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt4->bind_param("isiiii", $row['class_id'], $row['date'], $row['hour'], $row['original_teacher_id'], $row['substitute_teacher_id'], $row['substitute_subject_id']);
        $stmt4->execute();

        $conn->commit();

        echo "<script>alert('Substitution approved and timetable override added.'); window.location.href='approve_substitutes.php';</script>";
    } else {
        echo "<script>alert('Invalid request or already approved.'); window.location.href='approve_substitutes.php';</script>";
    }
}
?>
