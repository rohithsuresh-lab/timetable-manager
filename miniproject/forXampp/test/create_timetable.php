<?php
include 'db.php';

$msg = "";
$class_id = isset($_POST['class_id']) ? $_POST['class_id'] : null;

// Handle save request
if (isset($_POST['save']) && $class_id) {
    foreach ($_POST['timetable'] as $day => $hours) {
        foreach ($hours as $hour => $subject_id) {
            if ($subject_id === "") continue;

            $check = $conn->query("SELECT * FROM timetable WHERE class_id = $class_id AND day_of_week = '$day' AND hour = $hour");
            if ($check->num_rows > 0) {
                $sql = "UPDATE timetable SET subject_id = $subject_id WHERE class_id = $class_id AND day_of_week = '$day' AND hour = $hour";
            } else {
                $sql = "INSERT INTO timetable (class_id, subject_id, day_of_week, hour) VALUES ($class_id, $subject_id, '$day', $hour)";
            }
            $conn->query($sql);
        }
    }
    $msg = "Timetable updated.";
}

// Fetch class list
$classes = $conn->query("SELECT * FROM classes");

// Fetch subject list
$subjects = $conn->query("SELECT * FROM subjects");
$subject_options = [];
while ($row = $subjects->fetch_assoc()) {
    $subject_options[$row['subject_id']] = $row['name'];
}

// Fetch existing timetable if class selected
$timetable_data = [];
if ($class_id) {
    $result = $conn->query("SELECT * FROM timetable WHERE class_id = $class_id");
    while ($row = $result->fetch_assoc()) {
        $timetable_data[$row['day_of_week']][$row['hour']] = $row['subject_id'];
    }
}

$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
$hours = range(1, 5);
?>
<?php
include 'db.php';

// Default class to view
$class_id = $_GET['class_id'] ?? 1; // you can later allow dropdown to choose

// Get subjects for timetable
$timetable = [];
$sql = "SELECT day_of_week, hour, name 
        FROM timetable t 
        JOIN subjects s ON t.subject_id = s.subject_id 
        WHERE class_id = $class_id";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $timetable[$row['day_of_week']][$row['hour']] = $row['name'];
}

$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
$hours = [1, 2, 3, 4, 5];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Class Timetable View</title>
    <style>
        table { border-collapse: collapse; width: 80%; margin: auto; }
        th, td { border: 1px solid #aaa; padding: 10px; text-align: center; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Weekly Timetable (Class ID: <?= $class_id ?>)</h2>
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
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Create Timetable</title>
    <style>
        table, th, td { border: 1px solid black; border-collapse: collapse; padding: 5px; text-align: center; }
        select { width: 120px; }
    </style>
</head>
<body>
    <h2>Create / Edit Weekly Timetable</h2>

    <form method="POST">
        <label>Select Class:</label>
        <select name="class_id" onchange="this.form.submit()" required>
            <option value="">-- Select Class --</option>
            <?php
            mysqli_data_seek($classes, 0);
            while ($row = $classes->fetch_assoc()) {
                $selected = $class_id == $row['class_id'] ? "selected" : "";
                echo "<option value='{$row['class_id']}' $selected>{$row['class_name']}</option>";
            }
            ?>
        </select>
    </form>

    <?php if ($class_id): ?>
        <form method="POST">
            <input type="hidden" name="class_id" value="<?= $class_id ?>">
            <table>
                <tr>
                    <th>Day / Hour</th>
                    <?php foreach ($hours as $hour) echo "<th>$hour</th>"; ?>
                </tr>
                <?php foreach ($days as $day): ?>
                    <tr>
                        <th><?= $day ?></th>
                        <?php foreach ($hours as $hour): ?>
                            <td>
                                <select name="timetable[<?= $day ?>][<?= $hour ?>]">
                                    <option value="">--</option>
                                    <?php foreach ($subject_options as $id => $name): ?>
                                        <?php
                                        $selected = (isset($timetable_data[$day][$hour]) && $timetable_data[$day][$hour] == $id) ? "selected" : "";
                                        ?>
                                        <option value="<?= $id ?>" <?= $selected ?>><?= $name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            <br>
            <input type="submit" name="save" value="Save Timetable">
        </form>
        <p><?= $msg ?></p>
    <?php endif; ?>
</body>
</html>
