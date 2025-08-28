<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Lecturer Dashboard</title>
<link rel="stylesheet" href="lecturer.css">

</head>
<body>

<nav class="sidebar">
    <h2>Lecturer Panel</h2>
    <div class="hamburger" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="nav-links">
        <a href="#" data-section="upload_files">Upload Notes</a>
       <a href="#" data-section="receive_assignments">Receive Assignments</a>
        <a href="#" class="active" data-section="create_quiz">Create Quiz</a>
        <a href="#" data-section="student_profiles">Student Profiles</a>
        <a href="#" data-section="announcement"> Announcement</a>
        <a href="login.php">Logout</a>
    </div>
</nav>

<main class="main">
<header>
    <h1>Welcome, Lecturer</h1>
</header>

<div class="content">
    <section id="create_quiz">
        <h2>Create Quiz</h2>
        <p>Design and publish quizzes for your students to attempt online.</p>
    </section>

    <section id="upload_files" style="display:none;">
        <h2>Upload Lecturer PDF</h2>
           <h2>Upload Assignment</h2>
        <div class="assignments-upload">
   <form action="lecturer_pdf.php" method="POST" enctype="multipart/form-data">
        <label for="lecturer_id">Lecturer ID:</label>
        <input type="text" name="lecturer_id" required><br><br>

        <label for="title">Note Title:</label>
        <input type="text" name="title" required><br><br>

        <label for="pdf">Choose PDF File:</label>
        <input type="file" name="pdf" accept="application/pdf" required><br><br>

        <button type="submit">Upload</button>
    </form>
        <div id="upload-status"></div>

        <h3>Uploaded Notes</h3>
        <ul id="uploaded-pdfs-list"></ul>
    </section>

<section id="receive_assignments" class="receive-assignments">
    <h2>Received Assignments</h2>
    <p>Review and download submitted student assignments.</p>

    <div class="table-container">
        <table id="assignments-table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Assignment Title</th>
                    <th>Uploaded At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="submitted-assignments-list">
                <!-- Rows populated via JS -->
            </tbody>
        </table>
    </div>
</section>


<section id="create_quiz" class="active" style="display:none;">
    <h2>Create Quiz</h2>
    <form id="create-quiz-form">
        <input type="text" name="title" placeholder="Quiz Title" required>
        <div id="questions-container"></div>
        <button type="button" onclick="addQuestion()">Add Question</button>
        <button type="submit">Save Quiz</button>
    </form>
</section>

<!-- Section For Quiz of Test -->


    <section id="student_profiles" style="display:none;">
        <h2>Student Profiles</h2>
        <p>Access individual student profiles and track their performance.</p>
            <div id="student-list"></div>
    </section>


<!-- Section For Announcements -->

  <section id="announcements" style="display:none;">
    <h2>Broadcast Announcement</h2>
    <form id="announcement-form">
        <textarea name="message" rows="3" placeholder="Write your announcement..." required></textarea>
        <button type="submit">Send Announcement</button>
    </form>
    <div id="announcement-status"></div>
    <h3>Previous Announcements</h3>
    <ul id="announcements-list"></ul>
</section>
  
</div>
</main>

<script>


async function loadAssignments() {
    const response = await fetch('get_assignments.php');
    const assignments = await response.json();

    const list = document.getElementById('submitted-assignments-list');
    list.innerHTML = '';

    assignments.forEach(a => {
        const li = document.createElement('li');
        li.innerHTML = `
            <strong>Student:</strong> ${a.student_id} <br>
            <strong>Title:</strong> <span id="title-${a.id}">${a.assignment_title}</span> <br>
            <strong>Uploaded:</strong> ${a.uploaded_at} <br>
            <a href="${a.file_path}" target="_blank"> Open</a>
            <button onclick="editTitle(${a.id})"> Edit</button>
            <hr>
        `;
        list.appendChild(li);
    });
}

function editTitle(id) {
    const currentTitle = document.getElementById(`title-${id}`).innerText;
    const newTitle = prompt("Enter new title:", currentTitle);

    if (newTitle && newTitle !== currentTitle) {
        fetch('update_assignment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&title=${encodeURIComponent(newTitle)}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`title-${id}`).innerText = newTitle;
            } else {
                alert("Error updating title");
            }
        });
    }
}

// Auto-load when section is visible
document.addEventListener('DOMContentLoaded', loadAssignments);




//
function toggleMenu() {
    const nav = document.querySelector('.nav-links');
    nav.classList.toggle('show');
}

document.addEventListener('DOMContentLoaded', function () {
    const navLinks = document.querySelectorAll('.nav-links a');
    const sections = document.querySelectorAll('section');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('data-section');
            sections.forEach(sec => {
                sec.style.display = sec.id === sectionId ? 'block' : 'none';
            });
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });
});

// Load uploaded PDFs
async function loadLecturerPDFs() {
    const res = await fetch('list_pdfs.php'); // You will create this file to return JSON list of PDFs
    const data = await res.json();
    const ul = document.getElementById('uploaded-pdfs-list');
    ul.innerHTML = '';
    data.forEach(file => {
        const li = document.createElement('li');
        li.textContent = file;
        ul.appendChild(li);
    });
}

loadLecturerPDFs();

// Upload PDF handler
document.getElementById('upload-pdf-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const res = await fetch('upload_pdf.php', {
        method: 'POST',
        body: formData
    });
    const json = await res.json();
    document.getElementById('upload-status').innerText = json.success || json.error;
    loadLecturerPDFs();
});
async function loadStudents() {
    const res = await fetch('list_students.php');
    const students = await res.json();

    const container = document.getElementById('student-list');
    container.innerHTML = '';

    students.forEach(student => {
        const studentDiv = document.createElement('div');
        studentDiv.classList.add('student-card');
        studentDiv.style.marginBottom = '20px';
        studentDiv.style.padding = '10px';
        studentDiv.style.border = '1px solid #ccc';
        studentDiv.style.borderRadius = '6px';
        studentDiv.style.backgroundColor = '#fff';

        // Student info
        let html = `<strong>${student.name}</strong> (${student.student_id}) - ${student.email} - Program: ${student.program}`;

        // Performance table
        if (student.performance.length > 0) {
            html += `<table style="width:100%; margin-top:10px; border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th style="border:1px solid #ccc; padding:5px;">Course</th>
                                <th style="border:1px solid #ccc; padding:5px;">Assignment</th>
                                <th style="border:1px solid #ccc; padding:5px;">Score</th>
                                <th style="border:1px solid #ccc; padding:5px;">Grade</th>
                                <th style="border:1px solid #ccc; padding:5px;">Download</th>
                            </tr>
                        </thead>
                        <tbody>`;
            student.performance.forEach(p => {
                // Find assignment file matching this assignment
                let fileObj = student.assignments.find(a => a.filename.includes(p.assignment));
                let downloadBtn = fileObj ? `<a href="students/upload_assignment/${fileObj.filename}" download>Download</a>` : 'N/A';

                html += `<tr>
                            <td style="border:1px solid #ccc; padding:5px;">${p.course}</td>
                            <td style="border:1px solid #ccc; padding:5px;">${p.assignment}</td>
                            <td style="border:1px solid #ccc; padding:5px;">${p.score}</td>
                            <td style="border:1px solid #ccc; padding:5px;">${p.grade}</td>
                            <td style="border:1px solid #ccc; padding:5px;">${downloadBtn}</td>
                        </tr>`;
            });
            html += `</tbody></table>`;
        } else {
            html += `<p>No performance records yet.</p>`;
        }

        // List all uploaded assignments even if no score yet
        if (student.assignments.length > 0) {
            html += `<h4>Submitted Assignments:</h4><ul>`;
            student.assignments.forEach(a => {
                html += `<li><a href="students/upload_assignment/${a.filename}" download>${a.filename}</a></li>`;
            });
            html += `</ul>`;
        }

        studentDiv.innerHTML = html;
        container.appendChild(studentDiv);
    });
}


document.querySelector('a[data-section="student_profiles"]').addEventListener('click', loadStudents);

// Load announcements
async function loadAnnouncements() {
    const res = await fetch('list_announcements.php'); // returns JSON
    const data = await res.json();
    const ul = document.getElementById('announcements-list');
    ul.innerHTML = '';
    data.forEach(a => {
        const li = document.createElement('li');
        li.textContent = `${a.created_at} - ${a.message}`;
        ul.appendChild(li);
    });
}

// Submit announcement
document.getElementById('announcement-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    const res = await fetch('post_announcement.php', {
        method: 'POST',
        body: formData
    });

    const json = await res.json();
    document.getElementById('announcement-status').innerText = json.success || json.error;
    this.reset();
    loadAnnouncements();
});

loadAnnouncements();


</script>


</body>
<script src="lecturer.js"></script>

</html>
