<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
    header { background-color: #333; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
    h1 { margin: 0; }
    .logout-btn { background-color: #e74c3c; border: none; padding: 8px 16px; color: white; cursor: pointer; border-radius: 4px; text-decoration: none; }
    .container { padding: 20px; }
    .section { background-color: white; margin-bottom: 20px; padding: 15px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
    .section h2 { margin-top: 0; }
    ul li a { text-decoration: none; color: #3498db; }
  </style>
</head>
<body>
  <header>
    <h1>Admin Dashboard</h1>
    <a class="logout-btn" href="index.php">Logout</a>
  </header>
  <div class="container">

    <div class="section">
      <h2>User Management</h2>
      <ul>
        <li><a href="approve_students.php">Approve student signups</a></li>
        <li><a href="create_teacher.php">Create teacher accounts</a></li>
        <li><a href="assign_students.php">Assign students to classes</a></li>
        <li><a href="set_rep.php">Set student as class rep</a></li>
      </ul>
    </div>

    <div class="section">
      <h2>Class & Subject Management</h2>
      <ul>
        <li><a href="manage_classes.php">Manage classes (S1–S6)</a></li>
        <li><a href="manage_subjects.php">Create and manage subjects</a></li>
      </ul>
    </div>

    <div class="section">
      <h2>Timetable Management</h2>
      <ul>
        <li><a href="create_timetable.php">Create/Edit weekly timetable for each class</a></li>
        <li><a href="view_timetable.php">View complete timetable</a></li>
      </ul>
    </div>

    <div class="section">
      <h2>Leave & Substitution Management</h2>
      <ul>
        <li><a href="view_leaves.php">View teacher leave applications</a></li>
        <li><a href="approve_substitutes.php">Approve/reject substitute teacher requests</a></li>
      </ul>
    </div>

    <div class="section">
      <h2>Monitoring & Reports (Optional)</h2>
      <ul>
        <li><a href="track_leaves.php">Track teacher leaves</a></li>
        <li><a href="generate_reports.php">Generate reports on substitutions</a></li>
      </ul>
    </div>

  </div>
</body>
</html>
