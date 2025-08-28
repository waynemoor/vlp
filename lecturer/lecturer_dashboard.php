@@ .. @@
     <div class="nav-links">
         <a href="#" data-section="upload_files">Upload Notes</a>
        <a href="#" data-section="receive_assignments">Receive Assignments</a>
-        <a href="#" class="active" data-section="create_quiz">Create Quiz</a>
+        <a href="#" data-section="create_quiz">Create Quiz</a>
+        <a href="#" data-section="quiz_results">Quiz Results</a>
         <a href="#" data-section="student_profiles">Student Profiles</a>
         <a href="#" data-section="announcement"> Announcement</a>
+        <a href="#" data-section="messages">Messages</a>
         <a href="login.php">Logout</a>
     </div>
 </nav>
@@ .. @@
 <div class="content">
     <section id="create_quiz">
         <h2>Create Quiz</h2>
         <p>Design and publish quizzes for your students to attempt online.</p>
+        
+        <form id="create-quiz-form">
+            <div class="form-group">
+                <label for="quiz-title">Quiz Title</label>
+                <input type="text" name="title" id="quiz-title" required>
+            </div>
+            
+            <div class="form-group">
+                <label for="quiz-description">Description</label>
+                <textarea name="description" id="quiz-description" rows="3"></textarea>
+            </div>
+            
+            <div class="form-group">
+                <label for="quiz-module">Module</label>
+                <select name="module_id" id="quiz-module" required>
+                    <option value="">Select Module</option>
+                </select>
+            </div>
+            
+            <div class="form-row">
+                <div class="form-group">
+                    <label for="time-limit">Time Limit (minutes)</label>
+                    <input type="number" name="time_limit" id="time-limit" value="30" min="1" max="180">
+                </div>
+                
+                <div class="form-group">
+                    <label for="max-attempts">Max Attempts</label>
+                    <input type="number" name="max_attempts" id="max-attempts" value="1" min="1" max="5">
+                </div>
+            </div>
+            
+            <div id="questions-container"></div>
+            
+            <div class="form-actions">
+                <button type="button" onclick="addQuestion()">Add Question</button>
+                <button type="submit">Create Quiz</button>
+            </div>
+        </form>
+        
+        <div id="quiz-creation-status"></div>
     </section>

     <section id="upload_files" style="display:none;">
         <h2>Upload Lecturer PDF</h2>
-           <h2>Upload Assignment</h2>
-        <div class="assignments-upload">
-   <form action="lecturer_pdf.php" method="POST" enctype="multipart/form-data">
-        <label for="lecturer_id">Lecturer ID:</label>
-        <input type="text" name="lecturer_id" required><br><br>
-
-        <label for="title">Note Title:</label>
-        <input type="text" name="title" required><br><br>
-
-        <label for="pdf">Choose PDF File:</label>
-        <input type="file" name="pdf" accept="application/pdf" required><br><br>
-
-        <button type="submit">Upload</button>
-    </form>
+        
+        <form id="upload-notes-form" enctype="multipart/form-data">
+            <div class="form-group">
+                <label for="notes-title">Note Title</label>
+                <input type="text" name="title" id="notes-title" required>
+            </div>
+            
+            <div class="form-group">
+                <label for="notes-module">Module</label>
+                <select name="module_id" id="notes-module" required>
+                    <option value="">Select Module</option>
+                </select>
+            </div>
+            
+            <div class="form-group">
+                <label for="pdf-file">Choose PDF File</label>
+                <input type="file" name="pdf_file" id="pdf-file" accept="application/pdf" required>
+            </div>
+            
+            <button type="submit">Upload Notes</button>
+        </form>
+        
         <div id="upload-status"></div>

         <h3>Uploaded Notes</h3>
         <ul id="uploaded-pdfs-list"></ul>
@@ .. @@
 </section>


-<section id="create_quiz" class="active" style="display:none;">
-    <h2>Create Quiz</h2>
-    <form id="create-quiz-form">
-        <input type="text" name="title" placeholder="Quiz Title" required>
-        <div id="questions-container"></div>
-        <button type="button" onclick="addQuestion()">Add Question</button>
-        <button type="submit">Save Quiz</button>
-    </form>
+<section id="quiz_results" style="display:none;">
+    <h2>Quiz Results</h2>
+    <div id="quiz-results-container">
+        <!-- Quiz results will be loaded here -->
+    </div>
 </section>

-<!-- Section For Quiz of Test -->
+<section id="messages" style="display:none;">
+    <h2>Messages</h2>
+    <div class="messages-container">
+        <!-- Messages interface for lecturers -->
+        <div id="lecturer-messages">
+            <!-- Messages will be loaded here -->
+        </div>
+    </div>
+</section>


     <section id="student_profiles" style="display:none;">
@@ .. @@
 <script>


+// Global variables
+let questionCount = 0;
+
+// Load lecturer's modules
+async function loadLecturerModules() {
+    try {
+        const res = await fetch('get_modules.php');
+        const modules = await res.json();
+        
+        // Populate quiz module dropdown
+        const quizModuleSelect = document.getElementById('quiz-module');
+        const notesModuleSelect = document.getElementById('notes-module');
+        
+        [quizModuleSelect, notesModuleSelect].forEach(select => {
+            if (select) {
+                select.innerHTML = '<option value="">Select Module</option>';
+                modules.forEach(module => {
+                    const option = document.createElement('option');
+                    option.value = module.id;
+                    option.textContent = `${module.module_code} - ${module.module_name}`;
+                    select.appendChild(option);
+                });
+            }
+        });
+    } catch (e) {
+        console.error('Failed to load modules:', e);
+    }
+}
+
+// Add question to quiz
+function addQuestion() {
+    questionCount++;
+    const container = document.getElementById('questions-container');
+    const div = document.createElement('div');
+    div.className = 'question-block';
+    div.innerHTML = `
+        <h4>Question ${questionCount}</h4>
+        <div class="form-group">
+            <label>Question Text</label>
+            <textarea name="question_text_${questionCount}" required></textarea>
+        </div>
+        <div class="options-grid">
+            <div class="form-group">
+                <label>Option A</label>
+                <input type="text" name="option_a_${questionCount}" required>
+            </div>
+            <div class="form-group">
+                <label>Option B</label>
+                <input type="text" name="option_b_${questionCount}" required>
+            </div>
+            <div class="form-group">
+                <label>Option C</label>
+                <input type="text" name="option_c_${questionCount}" required>
+            </div>
+            <div class="form-group">
+                <label>Option D</label>
+                <input type="text" name="option_d_${questionCount}" required>
+            </div>
+        </div>
+        <div class="form-row">
+            <div class="form-group">
+                <label>Correct Answer</label>
+                <select name="correct_option_${questionCount}" required>
+                    <option value="">Select Correct Option</option>
+                    <option value="A">A</option>
+                    <option value="B">B</option>
+                    <option value="C">C</option>
+                    <option value="D">D</option>
+                </select>
+            </div>
+            <div class="form-group">
+                <label>Points</label>
+                <input type="number" name="points_${questionCount}" value="1" min="1" max="10">
+            </div>
+        </div>
+        <button type="button" onclick="removeQuestion(this)" class="btn-remove">Remove Question</button>
+        <hr>
+    `;
+    container.appendChild(div);
+}
+
+// Remove question from quiz
+function removeQuestion(button) {
+    button.parentElement.remove();
+}
+
+// Handle quiz creation
+document.getElementById('create-quiz-form').addEventListener('submit', async function(e) {
+    e.preventDefault();
+    
+    if (questionCount === 0) {
+        alert('Please add at least one question to the quiz.');
+        return;
+    }
+    
+    const formData = new FormData(this);
+    
+    try {
+        const res = await fetch('create_quiz.php', {
+            method: 'POST',
+            body: formData
+        });
+        
+        const result = await res.json();
+        
+        const statusDiv = document.getElementById('quiz-creation-status');
+        
+        if (result.success) {
+            statusDiv.innerHTML = `<div class="success">${result.success}</div>`;
+            this.reset();
+            document.getElementById('questions-container').innerHTML = '';
+            questionCount = 0;
+        } else {
+            statusDiv.innerHTML = `<div class="error">${result.error}</div>`;
+        }
+    } catch (e) {
+        console.error('Failed to create quiz:', e);
+        document.getElementById('quiz-creation-status').innerHTML = '<div class="error">Failed to create quiz. Please try again.</div>';
+    }
+});
+
+// Handle notes upload
+document.getElementById('upload-notes-form').addEventListener('submit', async function(e) {
+    e.preventDefault();
+    
+    const formData = new FormData(this);
+    
+    try {
+        const res = await fetch('upload_notes.php', {
+            method: 'POST',
+            body: formData
+        });
+        
+        const result = await res.json();
+        
+        const statusDiv = document.getElementById('upload-status');
+        
+        if (result.success) {
+            statusDiv.innerHTML = `<div class="success">${result.success}</div>`;
+            this.reset();
+            loadUploadedNotes();
+        } else {
+            statusDiv.innerHTML = `<div class="error">${result.error}</div>`;
+        }
+    } catch (e) {
+        console.error('Failed to upload notes:', e);
+        document.getElementById('upload-status').innerHTML = '<div class="error">Failed to upload notes. Please try again.</div>';
+    }
+});
+
+// Load uploaded notes
+async function loadUploadedNotes() {
+    try {
+        const res = await fetch('get_uploaded_notes.php');
+        const notes = await res.json();
+        
+        const list = document.getElementById('uploaded-pdfs-list');
+        list.innerHTML = '';
+        
+        notes.forEach(note => {
+            const li = document.createElement('li');
+            li.innerHTML = `
+                <strong>${note.title}</strong><br>
+                Module: ${note.module_name}<br>
+                Uploaded: ${new Date(note.uploaded_at).toLocaleString()}<br>
+                <a href="../${note.file_path}" target="_blank">View PDF</a>
+            `;
+            list.appendChild(li);
+        });
+    } catch (e) {
+        console.error('Failed to load uploaded notes:', e);
+    }
+}
+
+// Load quiz results
+async function loadQuizResults() {
+    try {
+        const res = await fetch('get_quiz_results.php');
+        const results = await res.json();
+        
+        const container = document.getElementById('quiz-results-container');
+        container.innerHTML = '';
+        
+        if (results.length === 0) {
+            container.innerHTML = '<p>No quiz results yet.</p>';
+            return;
+        }
+        
+        // Group results by quiz
+        const groupedResults = {};
+        results.forEach(result => {
+            if (!groupedResults[result.quiz_title]) {
+                groupedResults[result.quiz_title] = [];
+            }
+            groupedResults[result.quiz_title].push(result);
+        });
+        
+        Object.keys(groupedResults).forEach(quizTitle => {
+            const quizDiv = document.createElement('div');
+            quizDiv.className = 'quiz-results-section';
+            
+            const attempts = groupedResults[quizTitle];
+            const avgScore = attempts.reduce((sum, a) => sum + parseFloat(a.percentage), 0) / attempts.length;
+            
+            quizDiv.innerHTML = `
+                <h3>${quizTitle}</h3>
+                <p><strong>Module:</strong> ${attempts[0].module_name}</p>
+                <p><strong>Total Attempts:</strong> ${attempts.length}</p>
+                <p><strong>Average Score:</strong> ${avgScore.toFixed(2)}%</p>
+                
+                <table class="results-table">
+                    <thead>
+                        <tr>
+                            <th>Student</th>
+                            <th>Student ID</th>
+                            <th>Score</th>
+                            <th>Percentage</th>
+                            <th>Time Taken</th>
+                            <th>Submitted</th>
+                        </tr>
+                    </thead>
+                    <tbody>
+                        ${attempts.map(attempt => `
+                            <tr>
+                                <td>${attempt.stud_name}</td>
+                                <td>${attempt.stud_id}</td>
+                                <td>${attempt.score}/${attempt.total_questions}</td>
+                                <td>${attempt.percentage}%</td>
+                                <td>${attempt.time_taken} min</td>
+                                <td>${new Date(attempt.submitted_at).toLocaleString()}</td>
+                            </tr>
+                        `).join('')}
+                    </tbody>
+                </table>
+            `;
+            
+            container.appendChild(quizDiv);
+        });
+    } catch (e) {
+        console.error('Failed to load quiz results:', e);
+    }
+}
+
 async function loadAssignments() {
-    const response = await fetch('get_assignments.php');
+    const response = await fetch('get_student_assignments.php');
     const assignments = await response.json();

     const list = document.getElementById('submitted-assignments-list');
@@ .. @@
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
+    
+    // Load initial data
+    loadLecturerModules();
+    loadUploadedNotes();

     navLinks.forEach(link => {
         link.addEventListener('click', function(e) {
             e.preventDefault();
             const sectionId = this.getAttribute('data-section');
             sections.forEach(sec => {
                 sec.style.display = sec.id === sectionId ? 'block' : 'none';
             });
             navLinks.forEach(l => l.classList.remove('active'));
             this.classList.add('active');
+            
+            // Load section-specific data
+            if (sectionId === 'quiz_results') {
+                loadQuizResults();
+            }
+            if (sectionId === 'receive_assignments') {
+                loadAssignments();
+            }
         });
     });
 });
@@ .. @@
 loadAnnouncements();


 </script>


 </body>
-<script src="lecturer.js"></script>

 </html>