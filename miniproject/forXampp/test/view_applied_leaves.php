<?php
include 'db.php';

$teacher_id = $_GET['teacher_id'] ?? null;
if (!$teacher_id) {
    echo "Teacher ID not provided.";
    exit;
}

// Get teacher name (optional)
$teacherName = '';
$nameStmt = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
$nameStmt->bind_param("i", $teacher_id);
$nameStmt->execute();
$nameStmt->bind_result($teacherName);
$nameStmt->fetch();
$nameStmt->close();

echo "<h2>Leave Applications for $teacherName</h2>";

$sql = "SELECT * FROM teacher_leaves WHERE teacher_id = ? ORDER BY date DESC, hour";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>No leave records found.</p>";
    exit;
}

echo "<table border='1' cellpadding='8'>";
echo "<tr><th>Date</th><th>Hour</th><th>Reason</th><th>Substitute Requests</th></tr>";

while ($row = $result->fetch_assoc()) {
    $leave_date = $row['date'];
    $hour = $row['hour'];
    $reason = $row['reason'];
    
    // Fetch substitute requests related to this leave
    $subQuery = "SELECT sr.*, c.class_name, s.name, u.name AS substitute_name
                FROM substitute_requests sr
                JOIN classes c ON sr.class_id = c.class_id
                JOIN subjects s ON sr.original_subject_id = s.subject_id
                LEFT JOIN users u ON sr.substitute_teacher_id = u.user_id
                WHERE sr.original_teacher_id = ? AND sr.date = ? AND sr.hour " . 
                ($hour === null ? "IS NOT NULL" : "= ?");
    
    $subStmt = $conn->prepare($subQuery);
    if ($hour === null) {
        $subStmt->bind_param("is", $teacher_id, $leave_date);
    } else {
        $subStmt->bind_param("isi", $teacher_id, $leave_date, $hour);
    }

    $subStmt->execute();
    $subResult = $subStmt->get_result();

    // Display leave info
    echo "<tr>";
    echo "<td>$leave_date</td>";
    echo "<td>" . ($hour === null ? "Full Day" : "Hour $hour") . "</td>";
    echo "<td>" . htmlspecialchars($reason) . "</td>";

    echo "<td>";
    if ($subResult->num_rows > 0) {
        while ($sub = $subResult->fetch_assoc()) {
            echo "Class: {$sub['class_name']}, Subject: {$sub['name']}, Hour: {$sub['hour']}, ";
            echo "Substitute: " . ($sub['substitute_name'] ?? "Not Assigned") . ", Status: {$sub['status']}<br>";
        }
    } else {
        echo "No substitute requests.";
    }
    echo "</td>";

    echo "</tr>";
    $subStmt->close();
}

echo "</table>";
$stmt->close();
?>
