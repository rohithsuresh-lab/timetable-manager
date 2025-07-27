<?php
include 'db.php';

// Add Subject
if (isset($_POST['add_subject'])) {
    $name = $_POST['name'];
    $teacher_id = $_POST['teacher_id'];
    $stmt = $conn->prepare("INSERT INTO subjects (name, teacher_id) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $teacher_id);
    $stmt->execute();
    header("Location: manage_subjects.php");
    exit;
}

// Edit Subject
if (isset($_POST['edit_subject'])) {
    $subject_id = $_POST['subject_id'];
    $name = $_POST['name'];
    $teacher_id = $_POST['teacher_id'];
    $stmt = $conn->prepare("UPDATE subjects SET name = ?, teacher_id = ? WHERE subject_id = ?");
    $stmt->bind_param("sii", $name, $teacher_id, $subject_id);
    $stmt->execute();
    header("Location: manage_subjects.php");
    exit;
}

// Delete Subject
if (isset($_POST['delete_subject'])) {
    $subject_id = $_POST['subject_id'];
    $stmt = $conn->prepare("DELETE FROM subjects WHERE subject_id = ?");
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    header("Location: manage_subjects.php");
    exit;
}

// Fetch subjects with teacher names from users
$subjects = $conn->query("
    SELECT s.subject_id, s.name AS subject_name, u.user_id, u.name AS teacher_name
    FROM subjects s
    LEFT JOIN teachers t ON s.teacher_id = t.teacher_id
    LEFT JOIN users u ON t.teacher_id = u.user_id
");

// Fetch all teachers (from users table via teachers table)
$teachers = $conn->query("
    SELECT u.user_id, u.name
    FROM users u
    INNER JOIN teachers t ON u.user_id = t.teacher_id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Subjects</title>
</head>
<body>
    <h2>Subjects</h2>

    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Subject Name</th>
            <th>Assigned Teacher</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $subjects->fetch_assoc()): ?>
            <tr>
                <form method="POST">
                    <input type="hidden" name="subject_id" value="<?= $row['subject_id'] ?>">
                    <td><?= $row['subject_id'] ?></td>
                    <td><input type="text" name="name" value="<?= htmlspecialchars($row['subject_name']) ?>" required></td>
                    <td>
                        <select name="teacher_id" required>
                            <?php
                            $teachers->data_seek(0); // reset pointer
                            while ($teacher = $teachers->fetch_assoc()):
                            ?>
                                <option value="<?= $teacher['user_id'] ?>" <?= ($teacher['user_id'] == $row['user_id']) ? "selected" : "" ?>>
                                    <?= htmlspecialchars($teacher['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </td>
                    <td>
                        <button type="submit" name="edit_subject">Update</button>
                        <button type="submit" name="delete_subject" onclick="return confirm('Delete this subject?')">Delete</button>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
    </table>

    <hr>

    <h3>Add New Subject</h3>
    <form method="POST">
        <label>Subject Name:</label>
        <input type="text" name="name" required><br><br>

        <label>Assign Teacher:</label>
        <select name="teacher_id" required>
            <option value="">-- Select Teacher --</option>
            <?php
            $teachers->data_seek(0);
            while ($teacher = $teachers->fetch_assoc()):
            ?>
                <option value="<?= $teacher['user_id'] ?>"><?= htmlspecialchars($teacher['name']) ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit" name="add_subject">Add Subject</button>
    </form>
</body>
</html>