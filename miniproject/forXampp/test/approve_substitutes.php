<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applicant_id'])) {
    $applicant_id = intval($_POST['applicant_id']);

    // Step 1: Fetch relevant info
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
        $conn->begin_transaction();

        // Step 2: Approve selected applicant
        $stmt1 = $conn->prepare("UPDATE substitute_request_applicants SET status = 'approved' WHERE id = ?");
        $stmt1->bind_param("i", $applicant_id);
        $stmt1->execute();

        // Step 3: Reject other applicants
        $stmt2 = $conn->prepare("UPDATE substitute_request_applicants SET status = 'rejected' WHERE request_id = ? AND id != ?");
        $stmt2->bind_param("ii", $row['request_id'], $applicant_id);
        $stmt2->execute();

        // Step 4: Update main substitute_requests
        $stmt3 = $conn->prepare("UPDATE substitute_requests SET status = 'approved', substitute_teacher_id = ?, substitute_subject_id = ? WHERE request_id = ?");
        $stmt3->bind_param("iii", $row['substitute_teacher_id'], $row['substitute_subject_id'], $row['request_id']);
        $stmt3->execute();

        // Step 5: Add to timetable_overrides
        $stmt4 = $conn->prepare("INSERT INTO timetable_overrides (class_id, date, hour, original_teacher_id, substitute_teacher_id, subject_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt4->bind_param("isiiii", $row['class_id'], $row['date'], $row['hour'], $row['original_teacher_id'], $row['substitute_teacher_id'], $row['substitute_subject_id']);
        $stmt4->execute();

        $conn->commit();

        echo "<script>alert('Substitute approved and timetable override updated.'); window.location.href='approve_substitutes.php';</script>";
    } else {
        echo "<script>alert('Invalid or already approved.'); window.location.href='approve_substitutes.php';</script>";
    }
}

// Step 6: Fetch all pending substitute_requests with applicants
$query = "
    SELECT r.request_id, r.date, r.hour, r.reason, c.class_name, u1.name AS original_teacher,
           s1.name AS original_subject
    FROM substitute_requests r
    JOIN classes c ON r.class_id = c.class_id
    JOIN users u1 ON r.original_teacher_id = u1.user_id
    JOIN subjects s1 ON r.original_subject_id = s1.subject_id
    WHERE r.status = 'pending'
    ORDER BY r.date, r.hour
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Substitutes</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .request-box { border: 1px solid #aaa; padding: 15px; margin-bottom: 20px; border-radius: 8px; }
        .applicant { padding: 10px; margin-top: 10px; background: #f9f9f9; border: 1px solid #ddd; }
        .approve-btn { background-color: #28a745; color: white; border: none; padding: 6px 12px; cursor: pointer; }
    </style>
</head>
<body>

<h2>Pending Substitute Requests</h2>

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="request-box">
        <strong>Date:</strong> <?= htmlspecialchars($row['date']) ?><br>
        <strong>Hour:</strong> <?= htmlspecialchars($row['hour']) ?><br>
        <strong>Class:</strong> <?= htmlspecialchars($row['class_name']) ?><br>
        <strong>Teacher on Leave:</strong> <?= htmlspecialchars($row['original_teacher']) ?><br>
        <strong>Subject:</strong> <?= htmlspecialchars($row['original_subject']) ?><br>
        <strong>Reason:</strong> <?= nl2br(htmlspecialchars($row['reason'])) ?><br>

        <h4>Applicants:</h4>
        <?php
        $request_id = $row['request_id'];
        $app_sql = "
            SELECT a.id AS applicant_id, u.name AS applicant_name, s.name AS subject_name
            FROM substitute_request_applicants a
            JOIN users u ON a.substitute_teacher_id = u.user_id
            JOIN subjects s ON a.substitute_subject_id = s.subject_id
            WHERE a.request_id = ? AND a.status = 'pending'
        ";
        $stmt = $conn->prepare($app_sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $apps = $stmt->get_result();

        if ($apps->num_rows == 0): ?>
            <p>No applicants yet.</p>
        <?php else: ?>
            <?php while ($app = $apps->fetch_assoc()): ?>
                <div class="applicant">
                    <strong><?= htmlspecialchars($app['applicant_name']) ?></strong><br>
                    Subject: <?= htmlspecialchars($app['subject_name']) ?><br>
                    <form method="POST" style="margin-top: 5px;">
                        <input type="hidden" name="applicant_id" value="<?= $app['applicant_id'] ?>">
                        <button type="submit" class="approve-btn" onclick="return confirm('Approve this substitute?');">Approve</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
<?php endwhile; ?>

</body>
</html>
