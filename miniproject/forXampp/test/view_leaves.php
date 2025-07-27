<?php
include 'db.php';

$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Fetch leave data with optional search filter
$sql = "SELECT tl.*, u.name AS teacher_name
        FROM teacher_leaves tl
        JOIN users u ON tl.teacher_id = u.user_id";

if (!empty($search)) {
    $sql .= " WHERE u.name LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$search%";
    $stmt->bind_param("s", $searchTerm);
} else {
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Teacher Leaves</title>
    <style>
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 20px auto;
        }
        th, td {
            padding: 10px;
            border: 1px solid #888;
            text-align: center;
        }
        input[type="text"] {
            padding: 8px;
            width: 300px;
            margin: 20px auto;
            display: block;
        }
        input[type="submit"] {
            padding: 8px 16px;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>

<h2>Teacher Leave Records</h2>

<form method="GET" style="text-align: center;">
    <input type="text" name="search" placeholder="Search by teacher name..." value="<?php echo htmlspecialchars($search); ?>">
    <input type="submit" value="Search">
</form>

<table>
    <tr>
        <th>Teacher Name</th>
        <th>Date</th>
        <th>Leave Type</th>
        <th>Hour</th>
        <th>Reason</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) : ?>
        <tr>
            <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
            <td><?php echo htmlspecialchars($row['date']); ?></td>
            <td><?php echo is_null($row['hour']) ? 'Full Day' : 'Hourly'; ?></td>
            <td><?php echo is_null($row['hour']) ? '-' : $row['hour']; ?></td>
            <td><?php echo htmlspecialchars($row['reason']); ?></td>
        </tr>
    <?php endwhile; ?>

    <?php if ($result->num_rows === 0): ?>
        <tr>
            <td colspan="5">No leave records found.</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>
