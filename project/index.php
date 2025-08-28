<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Virtual Learning Platform Home</title>

  <link rel="stylesheet" href="style.css" type="text/css" />

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      background-color: #f3f2f1;
    }

    .noticebar {
      background: limegreen;
      color: white;
      padding: 10px 20px;
      font-weight: 600;
      text-align: center;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }

    .content {
      padding-top: 50px; /* space below fixed notice bar */
      flex-grow: 1;
      max-width: 1200px;
      margin: 0 auto;
      width: 90%;
    }

    .menubar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 15px 0;
      border-bottom: 2px solid limegreen;
    }

    .logo h2 {
      color: limegreen;
      letter-spacing: 3px;
    }

    .nav-links {
      list-style: none;
      display: flex;
      gap: 25px;
    }

    .nav-links li a {
      text-decoration: none;
      color: #333;
      font-weight: 600;
      padding: 8px 12px;
      border-radius: 6px;
      transition: background-color 0.3s ease;
    }

    .nav-links li a:hover {
      background-color: limegreen;
      color: white;
    }

    .main-content {
      margin-top: 30px;
      display: flex;
      flex-wrap: wrap;
      gap: 25px;
      justify-content: center;
    }

    .main-content h1 {
      width: 100%;
      text-align: center;
      margin-bottom: 10px;
      color: #333;
    }

    .main-content p {
      width: 100%;
      text-align: center;
      margin-bottom: 40px;
      color: #555;
      font-size: 1.1rem;
    }

    /* Small boxes container style */
    section {
      background: white;
      border: 3px solid limegreen;
      border-radius: 10px;
      box-shadow: 0 6px 15px rgb(0 0 0 / 0.1);
      padding: 20px 30px;
      width: 320px;
      color: #333;
      font-weight: 500;
      display: flex;
      flex-direction: column;
      gap: 12px;
      transition: transform 0.2s ease;
      cursor: default;
    }

    section:hover {
      transform: scale(1.04);
      box-shadow: 0 10px 25px rgb(0 0 0 / 0.15);
    }

    section h2 {
      color: limegreen;
      font-weight: 700;
      font-size: 1.5rem;
      margin-bottom: 12px;
      text-align: center;
    }

    section ul {
      list-style: none;
      padding-left: 0;
      color: #444;
      font-size: 1rem;
      line-height: 1.4;
    }

    section ul li {
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* Login button styling */
    .login-button {
      display: block;
      width: fit-content;
      margin: 15px auto 0 auto;
      padding: 10px 25px;
      background: limegreen;
      color: white;
      font-weight: 700;
      text-decoration: none;
      border-radius: 25px;
      box-shadow: 0 5px 12px rgb(0 128 0 / 0.6);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      text-align: center;
    }

    .login-button:hover {
      background-color: #0078D4;
      box-shadow: 0 8px 18px rgb(50 205 50 / 0.8);
    }

    /* Responsive adjustments */
    @media (max-width: 1000px) {
      .main-content {
        justify-content: center;
      }

      section {
        width: 90%;
        max-width: 400px;
      }
    }
  </style>
</head>
<body>

  <!-- Notice bar (Fixed at top) -->
  <div class="noticebar">
    📢 Welcome to the Virtual Learning Platform!
  </div>

  <!-- Main content wrapper -->
  <div class="content">

    <!-- Navigation bar -->
    <div class="menubar">
      <div class="logo">
        <h2>V.L PLATFORM</h2>
      </div>
      <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="#">About</a></li>
        <li><a href="#">Courses</a></li>
        <li><a href="#">How It Works</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </div>

    <!-- Main Welcome Section -->
    <div class="main-content">
      <h1>Welcome to V.L Platform</h1>
      <p>Learn anytime, anywhere — even while on attachment or retaking modules.</p>

      <section class="how-it-works">
        <h2>🎓 How It Works</h2>
        <ul>
          <li>📥 <strong>Register/Login</strong> using your email and password</li>
          <li>🎥 <strong>Access Recorded Lectures</strong> anytime, anywhere</li>
          <li>📚 <strong>Download Notes</strong> and study materials with ease</li>
          <li>👨‍🏫 <strong>Interact with Lecturers</strong> through Q&A sessions</li>
          <li>📅 <strong>Stay Updated</strong> with announcements and schedules</li>
        </ul>
      </section>

      <section class="login-prompt">
        <h2>🔐 Log In to Get Started</h2>
        <p>Access full features like:</p>
        <ul>
          <li>📊 Personal Dashboard</li>
          <li>📈 Track Your Progress</li>
          <li>🧾 View Class History</li>
          <li>📤 Submit Assignments</li>
        </ul>
        <a href="login.php" class="login-button">👉 Login Now</a>
      </section>

      <section class="extra">
        <h2>🚀 Why Choose VLP?</h2>
        <p>Whether you're on attachment, repeating a module, or just revising, VLP supports you with:</p>
        <ul>
          <li>✅ 24/7 Access</li>
          <li>✅ Mobile-Friendly Interface</li>
          <li>✅ Offline Downloads</li>
          <li>✅ Secure & Student-Centered</li>
        </ul>
      </section>
    </div>
<button><a href="Ad_dashboard.php">admin</a></button>
<button><a href="lect_dashboard.php">lecturer</a></button>
  </div>

  <script src="script.js"></script>
    <footer>
    &copy; <?= date('Y') ?> Virtual Learning Platform. All rights reserved.
  </footer>
</body>
</html>
