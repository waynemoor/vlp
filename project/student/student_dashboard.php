<?php
session_start();
require '../db_connection.php'; // PDO connection
require 'quiz.php';
require 'quiz_result.php';
require 'Messages.php';
require 'CommunityMessages.php';




if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;

}

$quiz = new Quiz($conn, $_SESSION['user_id']);
$quizResult = new QuizResult($conn, $_SESSION['user_id']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Student Dashboard</title>
<link rel="stylesheet" href="student_style.css">
</head>


<body>

  <nav class="sidebar">
    <h2>Student Panel</h2>
    <div class="nav-links">
      <a href="#" class="active" data-section="profile">Profile</a>
      <a href="#" data-section="assignments">Upload Assignment</a>
      <a href="#" data-section="lecturer-pdfs">Lecturer PDFs</a>
      <a href="#" data-section="messages">Messages</a>
      <a href="#" data-section="communities">Communities</a>
      <a href="#" data-section="feedback">Feedback</a>
      <a href="#" data-section="performance">Performance</a>
      <a href="#" data-section="quiz">Quiz</a>
      <a href="#" data-section="announcements">Announcements</a>
      <a href="logout.php" >LogOut</a>
    </div>
  </nav>

  <main class="main">
    <header>
      <h1>Welcome, Student</h1>
      <div class="notification-bell" data-count="3" title="Notifications">&#128276;</div>
    </header>

    <div class="content">
      
      <!-- Profile Section -->
      <section id="profile" class="active-section">
        <h2>Profile Summary</h2>
        <div class="profile-summary">
          <img src="../images/profie.png" alt="Student Photo" />
          <div class="profile-info">
            <strong>Name:</strong> Wayne More</br>
            <strong>Email:</strong> wayne@example.com</br>
            <strong>Student ID:</strong> T222345678C</br>
            <strong>Program:</strong> Software Engineering
          </div>
        </div>
      </section>

      <!-- Assignments Upload -->
  <section id="assignments" style="display:none;">
    <h2>Upload Assignment</h2>
    <div class="assignments-upload card">
        <form id="upload-assignment-form" class="upload-form horizontal-form" enctype="multipart/form-data" action="assignments.php" method="POST">

            <div class="form-group">
                <label for="user_id">Student ID</label>
                <input type="text" name="user_id" id="user_id" required>
            </div>

            <div class="form-group">
                <label for="assignmentFile">Select Assignment</label>
                <input type="file" name="assignmentFile" id="assignmentFile" required>
            </div>

            <div class="form-group">
                <label for="assignment_title">Assignment Title</label>
                <input type="text" name="assignment_title" id="assignment_title" required>
            </div>

            <div class="form-group submit-group">
                <button type="submit" class="btn-primary">Upload</button>
            </div>
        </form>

        <ul class="uploaded-list" id="uploaded-assignments-list">
            <!-- Uploaded assignments will appear here -->
        </ul>
    </div>
</section>



      <!-- Lecturer PDFs -->
      <section id="lecturer-pdfs" style="display:none;">
        <h2>Lecturer PDFs</h2>
        <div class="pdf-list">
          <ul>
            <li><a href="lecturer_notes/week1.pdf" target="_blank">Week 1 Notes</a></li>
            <li><a href="lecturer_notes/week2.pdf" target="_blank">Week 2 Notes</a></li>
            <li><a href="lecturer_notes/week3.pdf" target="_blank">Week 3 Notes</a></li>
          </ul>
        </div>
      </section>

      <!-- Messages -->
<section id="messages" style="display:none;">
<div class="messages-wrapper">
  <div class="message-sidebar">
    <h3>Recipients</h3>
    <select id="recipient-type">
      <option value="peer">Peers</option>
      <option value="lecturer">Lecturers</option>
      <option value="community">Communities</option>
    </select>
    <ul id="recipient-list">
      <!-- Populated dynamically via JS -->
    </ul>
  </div>

  <div class="message-container">
    <div id="chat-box">
      <!-- Messages will appear here -->
    </div>
    <form id="message-form">
      <input type="text" id="message-input" placeholder="Type your message..." required>
      <button type="submit">Send</button>
    </form>
  </div>
</div>

</section>


      <!-- Communities -->
      <section id="communities" style="display:none;">
        <h2>Communities</h2>
        <div class="communities-list">
          <ul>
            <li>Study Group - Computer Science</li>
            <li>Project Team - AI Research</li>
            <li>Book Club</li>
          </ul>
        </div>
        <div class="communities-create">
          <h3>Create New Community</h3>
          <form id="create-community-form">
            <input type="text" name="communityName" placeholder="Community Name" required />
            <button type="submit">Create</button>
          </form>
        </div>
      </section>

      <!-- Feedback -->
      <section id="feedback" style="display:none;">
        <h2>Feedback from Lecturers</h2>
        <div class="feedback-list">
          <ul>
            <li>"Great improvement on your last assignment!" - Prof. Smith</li>
            <li>"Please review the lecture on data structures again."</li>
            <li>"Excellent participation in class discussions."</li>
          </ul>
        </div>
      </section>

      <!-- Performance -->
      <section id="performance" style="display:none;">
        <h2>Performance Score</h2>
        <div class="performance-score">
          <table class="score-table">
            <thead>
              <tr>
                <th>Course</th>
                <th>Assignment</th>
                <th>Score</th>
                <th>Grade</th>
              </tr>
            </thead>
            <tbody>
              <tr><td>Programming 101</td><td>Assignment 1</td><td>85</td><td>B</td></tr>
              <tr><td>Data Structures</td><td>Project</td><td>92</td><td>A</td></tr>
              <tr><td>Engineering Maths</td><td>Quiz 3</td><td>78</td><td>C+</td></tr>
              <tr><td>Programming III</td><td>Test</td><td>78</td><td>P</td></tr>
              <tr><td>Maths</td><td>Test 2</td><td>65</td><td>C+</td></tr>
              <tr><td>Maths</td><td>Assignment </td><td>88</td><td>A+</td></tr>
              <tr><td>Maths</td><td>Quiz 2</td><td>50</td><td>C+</td></tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Announcement -->
      <section id="announcements" style="display:none;">
    <h2>Announcements</h2>
    <ul id="student-announcements-list"></ul>
</section>



<section id="quiz" style="display:none;">
    <h2>Take Quiz</h2>
    <div class="quiz" id="quiz-container">
        <form id="take-quiz-form">
            <div id="quiz-questions-container"></div>
            <button type="submit">Submit Quiz</button>
        </form>
        <div id="quiz-result" style="margin-top:10px; font-weight:bold;"></div>

        <h3>Previous Attempts</h3>
        <ul id="quiz-attempts-list"></ul>
    </div>
</section>

       
<script src="student.js">


</script>


  
</body>
</html>