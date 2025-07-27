<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <!-- Styles can be added later using class names -->
</head>
<body>

    <h1>Admin Dashboard</h1>

    <!-- Section: Approve Student Signups -->
    <section id="approve-students">
        <h2>Approve Student Signups</h2>
        <form action="approve_students.php" method="POST">
            <!-- Dynamically list pending students from DB -->
            <div>
                <input type="checkbox" name="approve_ids[]" value="1"> student1@example.com (Year: 2)
            </div>
            <div>
                <input type="checkbox" name="approve_ids[]" value="2"> student2@example.com (Year: 3)
            </div>
            <button type="submit">Approve Selected</button>
        </form>
    </section>

    <hr>

    <!-- Section: Manage Teachers -->
    <section id="manage-teachers">
        <h2>Manage Teachers</h2>

        <!-- Add Teacher -->
        <h3>Add Teacher</h3>
        <form action="add_teacher.php" method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Add Teacher</button>
        </form>

        <!-- Remove Teacher -->
        <h3>Remove Teacher</h3>
        <form action="remove_teacher.php" method="POST">
            <label>Teacher ID:</label>
            <input type="number" name="teacher_id" required>
            <button type="submit">Remove Teacher</button>
        </form>
    </section>

    <hr>

    <!-- Section: Manage Subjects -->
    <section id="manage-subjects">
        <h2>Manage Subjects</h2>

        <!-- Add Subject -->
        <h3>Add Subject</h3>
        <form action="add_subject.php" method="POST">
            <label>Subject Name:</label>
            <input type="text" name="subject_name" required>
            <button type="submit">Add Subject</button>
        </form>

        <!-- Remove Subject -->
        <h3>Remove Subject</h3>
        <form action="remove_subject.php" method="POST">
            <label>Subject ID:</label>
            <input type="number" name="subject_id" required>
            <button type="submit">Remove Subject</button>
        </form>

        <!-- Link Subject to Teacher -->
        <h3>Assign Subject to Teacher</h3>
        <form action="assign_subject.php" method="POST">
            <label>Teacher ID:</label>
            <input type="number" name="teacher_id" required>
            <label>Subject ID:</label>
            <input type="number" name="subject_id" required>
            <button type="submit">Assign</button>
        </form>
    </section>

    <hr>

    <!-- Section: Edit Timetable -->
    <section id="edit-timetable">
        <h2>Edit Timetable</h2>
        <form action="update_timetable.php" method="POST">
            <label>Year:</label>
            <input type="number" name="year" min="1" max="4" required>

            <label>Day:</label>
            <select name="day">
                <option>Monday</option>
                <option>Tuesday</option>
                <option>Wednesday</option>
                <option>Thursday</option>
                <option>Friday</option>
            </select>

            <label>Hour (1 to 5):</label>
            <input type="number" name="hour" min="1" max="5" required>

            <label>Subject ID:</label>
            <input type="number" name="subject_id" required>

            <label>Teacher ID:</label>
            <input type="number" name="teacher_id" required>

            <button type="submit">Update Timetable</button>
        </form>
    </section>

    <hr>

    <!-- Section: Approve Absence and Occupy Requests -->
    <section id="absence-requests">
        <h2>Approve Absence & Occupy Requests</h2>

        <!-- Absence Requests -->
        <h3>Pending Absence Requests</h3>
        <form action="approve_absence.php" method="POST">
            <!-- Loop: dynamically list absence requests -->
            <div>
                <input type="checkbox" name="absence_ids[]" value="10"> Teacher A (Subject X) - Monday Hour 2
            </div>
            <button type="submit">Approve Selected Absences</button>
        </form>

        <!-- Occupy Requests -->
        <h3>Pending Occupy Requests</h3>
        <form action="approve_occupy.php" method="POST">
            <!-- Loop: dynamically list occupy requests -->
            <div>
                <input type="checkbox" name="occupy_ids[]" value="5"> Teacher B requests to occupy Teacher A's class - Monday Hour 2
            </div>
            <button type="submit">Approve Selected Occupy Requests</button>
        </form>
    </section>

</body>
</html>
