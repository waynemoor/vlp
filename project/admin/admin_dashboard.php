<?php
include'../db_connection.php';
session_start();


// Fetch students
$sql_students = "SELECT * FROM students ORDER BY stud_name ASC";
$stmt = $conn->prepare($sql_students);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch lecturers
$sql_lecturers = "SELECT * FROM lecturers ORDER BY name ASC";
$stmt = $conn->prepare($sql_lecturers);
$stmt->execute();
$lecturers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Now $students and $lecturers contain all rows




//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//    header("Location: ../login.php");
//   exit;
//};
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Administrator Dashboard</title>
<link rel="stylesheet" href="admin_style.css">

</head>
<body>

<nav class="sidebar">
  <h2>Admin Panel</h2>
  <div class="hamburger" onclick="toggleMenu()">
    <span></span>
    <span></span>
    <span></span>
  </div>
  <div class="nav-links">
    <a href="#" class="active" data-section="overview">Overview</a>
    <a href="#" data-section="students">Manage Students</a>
    <a href="#" data-section="lecturers">Manage Lecturers</a>
    <a href="#" data-section="assignments">Assignments</a>
    <a href="#" data-section="communities">Communities</a>
    <a href="#" data-section="performance">Performance</a>
    <a href="#" data-section="database">Database Management</a>
     <a href="#" data-section="notifications">Notifcations </a>
    <a href="#" data-section="feedback">Feedback</a>
    <a href="project/login.php">Logout</a>
    
   
  </div>
</nav>

<main class="main">
  <header>
    <h1>Welcome, Administrator</h1>
    <div class="notification-bell" data-count="5" title="Notifications">🔔</div>
  </header>
  <div class="content">
<!-- Overview Section -->
<section id="overview">
    <h2>Admin Dashboard Overview</h2>
    <div class="overview-cards" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px;">
        <div class="card" style="padding:20px; background:#fff; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1);">
            <h3>Total Students</h3>
            <p id="total-students">0</p>
        </div>
        <div class="card" style="padding:20px; background:#fff; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1);">
            <h3>Total Lecturers</h3>
            <p id="total-lecturers">0</p>
        </div>
        <div class="card" style="padding:20px; background:#fff; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1);">
            <h3>Assignments Uploaded</h3>
            <p id="total-assignments">0</p>
        </div>
        <div class="card" style="padding:20px; background:#fff; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1);">
            <h3>Announcements Sent</h3>
            <p id="total-announcements">0</p>
        </div>
    </div>
</section>
<section id="students" style="display:none">
    <h2>Student Management</h2>

    <!-- Add New Student Form -->
    <div class="add-student-form" style="margin-bottom:20px;">
        <h3>Add New Student</h3>
        <form id="add-student-form" action="add_student.php" method="POST">
            <input type="text" name="name" id="name" placeholder="Full Name" required>
            <input type="email" name="email" id="email" placeholder="Email" required>
            <input type="text" name="student_id" id="student_id" placeholder="Student ID" required>
            <input type="text" name="program" id="program" placeholder="Program" required>
            <input type="text" name="no_carries" id="no_carries" placeholder="Number of carries" required>
            <button type="submit">Add Student</button>
        </form>
        <div id="add-student-status"></div>
    </div>

    <!-- Student List Table -->
    <div class="student-list">
        <h3>All Students</h3>
        <table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Student ID</th>
                    <th>Program</th>
                    <th>Number__of_carries</th>
                </tr>
          <?php
            foreach($students as $info)
            {
 
            echo"<tr>";
                  echo"<td>" .htmlspecialchars($info['stud_name'])."</td>";
             
                  echo"<td>" .htmlspecialchars($info['stud_email'])."</td>";
             
                  echo"<td>" .htmlspecialchars($info['stud_id'])."</td>";
             
                  echo"<td>" .htmlspecialchars($info['program'])."</td>";
             
                  echo"<td>" .htmlspecialchars($info['no_carries'])."</td>";
            

            echo"</tr>";
                
            }
             ?>   
            </thead>
            <tbody id="students-table-body">
                <!-- Students will be loaded dynamically here -->
            </tbody>
        </table>
        
    </div>
</section>
<section id="lecturers" style="display:none">
    <h2>Lecturer Management</h2>

    <!-- Add New Lecturer Form -->
    <div class="add-lecturer-form" style="margin-bottom:20px;">
        <h3>Add New Lecturer</h3>
        <form id="add-lecturer-form" action="add_lecturer.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="employee_id" placeholder="Employee ID" required>
            <input type="text" name="department" placeholder="Department" required>
            <button type="submit">Add Lecturer</button>
        </form>
        <div id="add-lecturer-status"></div>
    </div>

    <!-- Lecturer List Table -->
    <div class="lecturer-list">
        <h3>All Lecturers</h3>
        <table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Employee ID</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
                          <?php
            foreach($lecturers as $info)
            {
 
            echo"<tr>";
                  echo"<td>" .htmlspecialchars($info['name'])."</td>";
             
                  echo"<td>" .htmlspecialchars($info['email'])."</td>";
             
                  echo"<td>" .htmlspecialchars($info['employee_id'])."</td>";
             
                  echo"<td>" .htmlspecialchars($info['department'])."</td>";
             
              
            

            echo"</tr>";
                
            }
             ?>  
            </thead>
            <tbody id="lecturers-table-body">
                <!-- Lecturers will be loaded dynamically here -->
            </tbody>
        </table>
    </div>
</section>
    <section id="assignments" style="display:none">Assignments submitted overview.</section>
<section id="communities" style="display:none">
    <h2>Communities Management</h2>

    <!-- Add New Community Form -->
    <div class="add-community-form" style="margin-bottom:20px;">
        <h3>Create New Community</h3>
        <form id="add-community-form">
            <input type="text" name="community_name" placeholder="Community Name" required>
            <input type="text" name="description" placeholder="Description">
            <button type="submit">Add Community</button>
        </form>
        <div id="add-community-status"></div>
    </div>

    <!-- Community List Table -->
    <div class="community-list">
        <h3>All Communities</h3>
        <table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th>Community Name</th>
                    <th>Description</th>
                    <th>Members</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="communities-table-body">
                <!-- Communities will be loaded dynamically here -->
            </tbody>
        </table>
    </div>
</section>
<section id="performance" style="display:none">
    <h2>Performance Monitoring</h2>

    <!-- Filters (optional) -->
    <div class="performance-filters" style="margin-bottom:15px;">
        <label for="filter-course">Course:</label>
        <select id="filter-course">
            <option value="">All</option>
            <option value="Programming 101">Programming 101</option>
            <option value="Data Structures">Data Structures</option>
            <option value="Maths">Maths</option>
        </select>

        <label for="filter-student">Student:</label>
        <input type="text" id="filter-student" placeholder="Search by student name or ID">

        <button id="apply-filters">Apply Filters</button>
    </div>

    <!-- Performance Table -->
    <table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Student ID</th>
                <th>Course</th>
                <th>Assignment</th>
                <th>Score</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody id="performance-table-body">
            <!-- Performance data loaded dynamically -->
        </tbody>
    </table>
</section>
<section id="notifications" style="display:none">
    <h2>Notifications</h2>

    <!-- Send Notification Form -->
    <div class="send-notification" style="margin-bottom:20px;">
        <h3>Send New Notification</h3>
        <form id="notification-form">
            <label for="notification-type">Type:</label>
            <select id="notification-type" name="type" required>
                <option value="email">Email</option>
                <option value="system">System</option>
            </select><br><br>

            <label for="notification-message">Message:</label><br>
            <textarea id="notification-message" name="message" rows="4" cols="50" required></textarea><br><br>

            <button type="submit">Send Notification</button>
        </form>
        <div id="notification-status" style="margin-top:10px; font-weight:bold;"></div>
    </div>

    <!-- List of Past Notifications -->
    <h3>Past Notifications</h3>
    <ul id="notification-list">
        <!-- Notifications will be dynamically loaded here -->
    </ul>
</section>
<section id="database" style="display:none">
    <h2>Database Administration</h2>

    <!-- Table List -->
    <div>
        <h3>Database Tables</h3>
        <ul id="tables-list"></ul>
        <button id="refresh-tables">Refresh</button>
    </div>

    <!-- Selected Table Data -->
    <div id="table-data" style="margin-top:20px; display:none;">
        <h3 id="table-title"></h3>
        <table id="table-rows" style="width:100%; border-collapse: collapse;">
            <thead></thead>
            <tbody></tbody>
        </table>
        <button id="back-to-tables">Back to Tables</button>
    </div>

    <!-- SQL Console -->
    <div style="margin-top:20px;">
        <h3>SQL Console</h3>
        <textarea id="sql-query" rows="5" style="width:100%;"></textarea>
        <button id="run-sql">Run Query</button>
        <pre id="sql-result"></pre>
    </div>

    <!-- Logs -->
    <div style="margin-top:20px;">
        <h3>System Logs</h3>
        <pre id="system-logs"></pre>
        <button id="refresh-logs">Refresh Logs</button>
    </div>
</section>
  </div>
</main>
<script src="script.js"></script>
</body>
</html>
