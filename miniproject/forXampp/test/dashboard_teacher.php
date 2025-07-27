<?php
include 'db.php';

if (!isset($_GET['teacher_id'])) {
    die("Teacher ID not provided.");
}

$teacher_id = intval($_GET['teacher_id']);

// Get teacher email
$teacherRes = $conn->query("SELECT email FROM users WHERE user_id = $teacher_id AND role = 'teacher'");
if ($teacherRes->num_rows == 0) {
    die("Invalid teacher ID.");
}
$teacherEmail = $teacherRes->fetch_assoc()['email'];

// Get subjects assigned to teacher
$subjectRes = $conn->query("SELECT DISTINCT s.subject_id, s.name AS subject_name, c.class_name
FROM timetable t
JOIN subjects s ON t.subject_id = s.subject_id
JOIN classes c ON t.class_id = c.class_id
WHERE s.teacher_id = $teacher_id

");

$assignedSubjects = [];
$subjectDetails = [];

while ($row = $subjectRes->fetch_assoc()) {
    $assignedSubjects[$row['subject_id']] = $row['subject_name'];
    $subjectDetails[] = $row['subject_name'] . " (" . $row['class_name'] . ")";
}


// Fetch complete timetable
// Fetch base timetable
$baseTimetableRes = $conn->query("SELECT t.*, s.name AS subject_name, c.class_name 
                              FROM timetable t
                              JOIN subjects s ON t.subject_id = s.subject_id
                              JOIN classes c ON t.class_id = c.class_id");

$timetable = [];
while ($row = $baseTimetableRes->fetch_assoc()) {
    $timetable[$row['class_name']][$row['day_of_week']][$row['hour']] = [
        'subject_name' => $row['subject_name'],
        'subject_id' => $row['subject_id'],
        'class_id' => $row['class_id'],
        'is_own' => isset($assignedSubjects[$row['subject_id']])
    ];
}

// Fetch overrides
$overrideRes = $conn->query("SELECT tovr.*, sub.name AS substitute_subject_name, u.name AS substitute_teacher_name, c.class_name 
                             FROM timetable_overrides tovr
                             JOIN subjects sub ON tovr.subject_id = sub.subject_id
                             JOIN users u ON sub.teacher_id = u.user_id
                             JOIN classes c ON tovr.class_id = c.class_id");

while ($row = $overrideRes->fetch_assoc()) {
    $className = $row['class_name'];
	$dayOfWeek = date('D', strtotime($row['date'])); // returns "Mon", "Tue", etc.
    $day = $dayOfWeek;
    $hour = $row['hour'];
    $subjName = $row['substitute_subject_name'];

    $timetable[$className][$day][$hour] = [
        'subject_name' => $subjName . " (Sub by " . $row['substitute_teacher_name'] . ")",
        'subject_id' => $row['subject_id'],
        'is_own' => ($row['substitute_teacher_name'] === $teacherEmail) // OR use ID check
    ];
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 30px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .highlight { background-color: #d1f0d1; }
        .note { color: #555; font-size: 0.9em; }
    </style>

</head>
<body>
	<div style="text-align: right; padding: 10px;">
    <button onclick="window.location.href='index.php'" style="
        padding: 6px 12px;
        background-color: #e74c3c;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    ">
			Logout
		</button>
	</div>


    <h2>Welcome, <?= htmlspecialchars($teacherEmail) ?></h2>

    <h3>📚 Assigned Subjects</h3>
    <ul>
        <?php foreach ($subjectDetails as $sub): ?>
            <li><?= htmlspecialchars($sub) ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>📅 Timetable (Your subjects highlighted)</h3>
    <?php foreach ($timetable as $class => $days): ?>
        <h4>Class: <?= htmlspecialchars($class) ?></h4>
        <table>
            <tr>
                <th>Day</th>
                <?php for ($i = 1; $i <= 5; $i++) echo "<th>Hour $i</th>"; ?>
            </tr>
            <?php foreach (['Mon','Tue','Wed','Thu','Fri'] as $day): ?>
                <tr>
                    <td><?= $day ?></td>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php
                            $cell = $days[$day][$i] ?? ['subject_name' => '', 'is_own' => false];
                            $classHighlight = $cell['is_own'] ? "highlight" : "";
                        ?>
                        <td class="<?= $classHighlight ?>"><?= htmlspecialchars($cell['subject_name']) ?></td>
                    <?php endfor; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endforeach; ?>

    <h3>📝 Apply Leave</h3>
    <form method="POST" action="apply_leave.php">
        <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">
        
        <label for="leave_type">Leave Type:</label>
        <select name="leave_type" id="leave_type" required onchange="toggleHourSelect(this.value)">
            <option value="">Select</option>
            <option value="full">Full Day</option>
            <option value="hourly">Specific Hour</option>
        </select><br><br>

        <label for="leave_date">Date:</label>
        <input type="date" name="leave_date" required><br><br>

        <div id="hourSection" style="display:none;">
            <label for="hour">Hour (1-5):</label>
            <select name="hour">
                <option value="">Select Hour</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>">Hour <?= $i ?></option>
                <?php endfor; ?>
            </select><br><br>
        </div>

        <button type="submit">Submit Leave</button>
    </form>
	<hr>

<h3>🔍 View Options</h3>

<div style="display: flex; flex-direction: column; gap: 10px; max-width: 300px;">
    <button onclick="window.location.href='view_free_hours.php?teacher_id=<?= $teacher_id ?>'" style="padding: 8px; background-color: #3498db; color: white; border: none; border-radius: 5px;">View Free Hours</button>

    <button onclick="window.location.href='view_applied_leaves.php?teacher_id=<?= $teacher_id ?>'" style="padding: 8px; background-color: #9b59b6; color: white; border: none; border-radius: 5px;">View Applied Leaves</button>

    <button onclick="window.location.href='rep_notes.php?teacher_id=<?= $teacher_id ?>'" style="padding: 8px; background-color: #e67e22; color: white; border: none; border-radius: 5px;">View Rep Notes</button>
</div>

    <script>
        function toggleHourSelect(type) {
            document.getElementById('hourSection').style.display = (type === 'hourly') ? 'block' : 'none';
        }
    </script>
</body>
</html>
