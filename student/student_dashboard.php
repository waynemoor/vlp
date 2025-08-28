@@ .. @@
       <!-- Assignments Upload -->
   <section id="assignments" style="display:none;">
     <h2>Upload Assignment</h2>
     <div class="assignments-upload card">
         <form id="upload-assignment-form" class="upload-form horizontal-form" enctype="multipart/form-data" action="assignments.php" method="POST">

             <div class="form-group">
                 <label for="user_id">Student ID</label>
                 <input type="text" name="user_id" id="user_id" required>
             </div>

+            <div class="form-group">
+                <label for="assignment-module">Module (Optional)</label>
+                <select name="module_id" id="assignment-module">
+                    <option value="">Select Module</option>
+                </select>
+            </div>
+
             <div class="form-group">
                 <label for="assignmentFile">Select Assignment</label>
                 <input type="file" name="assignmentFile" id="assignmentFile" required>
@@ .. @@
         <ul class="uploaded-list" id="uploaded-assignments-list">
             <!-- Uploaded assignments will appear here -->
         </ul>
     </div>
 </section>



       <!-- Lecturer PDFs -->
       <section id="lecturer-pdfs" style="display:none;">
         <h2>Lecturer PDFs</h2>
-        <div class="pdf-list">
-          <ul>
-            <li><a href="lecturer_notes/week1.pdf" target="_blank">Week 1 Notes</a></li>
-            <li><a href="lecturer_notes/week2.pdf" target="_blank">Week 2 Notes</a></li>
-            <li><a href="lecturer_notes/week3.pdf" target="_blank">Week 3 Notes</a></li>
-          </ul>
+        <div class="pdf-list" id="lecturer-notes-list">
+          <!-- Notes will be loaded dynamically -->
         </div>
       </section>
@@ .. @@
       <!-- Announcement -->
       <section id="announcements" style="display:none;">
     <h2>Announcements</h2>
     <ul id="student-announcements-list"></ul>
 </section>

+      <!-- Module Registration -->
+      <section id="module-registration" style="display:none;">
+        <h2>Module Registration</h2>
+        <div id="module-registration-container">
+          <!-- Module registration content will be loaded here -->
+        </div>
+      </section>
+
+      <!-- Notifications -->
+      <section id="notifications" style="display:none;">
+        <h2>Notifications</h2>
+        <ul id="notifications-list">
+          <!-- Notifications will be loaded here -->
+        </ul>
+      </section>


 <section id="quiz" style="display:none;">
     <h2>Take Quiz</h2>
-    <div class="quiz" id="quiz-container">
-        <form id="take-quiz-form">
-            <div id="quiz-questions-container"></div>
-            <button type="submit">Submit Quiz</button>
-        </form>
-        <div id="quiz-result" style="margin-top:10px; font-weight:bold;"></div>
-
-        <h3>Previous Attempts</h3>
-        <ul id="quiz-attempts-list"></ul>
+    
+    <div class="quiz-section">
+        <h3>Available Quizzes</h3>
+        <div id="available-quizzes">
+            <!-- Available quizzes will be loaded here -->
+        </div>
+        
+        <div id="quiz-container">
+            <!-- Quiz questions will appear here when started -->
+        </div>
+        
+        <h3>Your Quiz Results</h3>
+        <div id="quiz-results">
+            <!-- Quiz results will be loaded here -->
+        </div>
     </div>
 </section>
@@ .. @@
       <a href="#" data-section="profile">Profile</a>
       <a href="#" data-section="assignments">Upload Assignment</a>
       <a href="#" data-section="lecturer-pdfs">Lecturer PDFs</a>
+      <a href="#" data-section="module-registration">Module Registration</a>
       <a href="#" data-section="messages">Messages</a>
       <a href="#" data-section="communities">Communities</a>
       <a href="#" data-section="feedback">Feedback</a>
       <a href="#" data-section="performance">Performance</a>
       <a href="#" data-section="quiz">Quiz</a>
       <a href="#" data-section="announcements">Announcements</a>
+      <a href="#" data-section="notifications">Notifications</a>
       <a href="logout.php" >LogOut</a>
     </div>
   </nav>