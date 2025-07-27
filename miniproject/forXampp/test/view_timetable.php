<?php
include 'db.php';

// Get all classes for dropdown
$classes = $conn->query("SELECT class_id, class_name FROM classes");

// Get selected class ID
$class_id = $_GET['class_id'] ?? null;

// Get subjects for the selected class
$timetable = [];
if ($class_id) {
    $sql = "SELECT day_of_week, hour, name 
            FROM timetable t 
            JOIN subjects s ON t.subject_id = s.subject_id 
            WHERE class_id = $class_id";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $timetable[$row['day_of_week']][$row['hour']] = $row['name'];
    }
}

$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
$hours = [1, 2, 3, 4, 5];
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Timetable</title>
    <style>
        table { border-collapse: collapse; width: 90%; margin: auto; margin-top: 20px; }
        th, td { border: 1px solid #aaa; padding: 10px; text-align: center; }
        th { background-color: #f0f0f0; }
        select, input[type="submit"] { padding: 5px; font-size: 16px; }
        form { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>

    <h2 style="text-align: center;">View Weekly Timetable</h2>

    <form method="GET">
        <label for="class_id">Select Class:</label>
        <select name="class_id" id="class_id" required onchange="this.form.submit()">
            <option value="">-- Choose Class --</option>
            <?php while ($row = $classes->fetch_assoc()): ?>
                <option value="<?= $row['class_id'] ?>" <?= ($row['class_id'] == $class_id) ? 'selected' : '' ?>>
                    <?= $row['class_name'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($class_id): ?>
        <table>
            <tr>
                <th>Day / Hour</th>
                <?php foreach ($hours as $h) echo "<th>Hour $h</th>"; ?>
            </tr>
            <?php foreach ($days as $day): ?>
                <tr>
                    <th><?= $day ?></th>
                    <?php foreach ($hours as $hour): ?>
                        <td>
                            <?= $timetable[$day][$hour] ?? '-' ?>
                        </td>
                    <?php endforeach ?>
                </tr>
            <?php endforeach ?>
        </table>
    <?php endif; ?>

</body>
</html>
