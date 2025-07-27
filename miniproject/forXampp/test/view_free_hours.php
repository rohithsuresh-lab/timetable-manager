<?php
include 'db.php';

$teacher_id = $_GET['teacher_id'] ?? 0;
$teacher_id = intval($teacher_id);

// Get all subjects taught by this teacher
$subject_stmt = $conn->prepare("SELECT subject_id, name FROM subjects WHERE teacher_id = ?");
$subject_stmt->bind_param("i", $teacher_id);
$subject_stmt->execute();
$subject_result = $subject_stmt->get_result();

$subjects_taught = [];
while ($sub = $subject_result->fetch_assoc()) {
    $subjects_taught[] = $sub;
}

// Fetch substitution requests
$sql = "
SELECT sr.*, 
       c.class_name AS class_name, 
       s.name AS subject_name, 
       u.name AS original_teacher_name
FROM substitute_requests sr
JOIN classes c ON sr.class_id = c.class_id
JOIN subjects s ON sr.original_subject_id = s.subject_id
JOIN users u ON sr.original_teacher_id = u.user_id
WHERE sr.status = 'pending'
  AND sr.original_teacher_id != ?
  AND sr.request_id NOT IN (
      SELECT request_id 
      FROM substitute_request_applicants 
      WHERE substitute_teacher_id = ?
  )
ORDER BY sr.date, sr.hour
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $teacher_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Substitute Requests</title>
</head>
<body>
    <h2>Available Substitution Requests</h2>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Date</th>
                <th>Hour</th>
                <th>Class</th>
                <th>Subject</th>
                <th>Original Teacher</th>
                <th>Reason</th>
                <th>Apply</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['date'] ?></td>
                    <td><?= $row['hour'] ?></td>
                    <td><?= $row['class_name'] ?></td>
                    <td><?= $row['subject_name'] ?></td>
                    <td><?= $row['original_teacher_name'] ?></td>
                    <td><?= $row['reason'] ?></td>
                    <td>
                        <form method="POST" action="apply_substitute.php">
                            <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">
                            <input type="hidden" name="request_id" value="<?= $row['request_id'] ?>">
                            <label for="subject_id">Select Subject:</label>
                            <select name="subject_id" required>
                                <option value="">--Select--</option>
                                <?php foreach ($subjects_taught as $sub): ?>
                                    <option value="<?= $sub['subject_id'] ?>"><?= $sub['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Apply</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No available substitute requests to apply for.</p>
    <?php endif; ?>

    <br><a href="dashboard_teacher.php?teacher_id=<?= $teacher_id ?>">🔙 Back to Dashboard</a>
</body>
</html>
