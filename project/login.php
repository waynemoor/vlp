<?php
session_start();
require_once 'db_connection.php'; // This defines $conn (PDO)

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        try {
            $stmt = $conn->prepare("
                SELECT u.password, u.email, r.role_name
                FROM users u
                JOIN roles r ON u.role_id = r.role_id
                WHERE u.username = :username OR u.email = :username
                LIMIT 1
            ");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['email']; // or $username
                $_SESSION['role'] = $user['role_name'];

                // Redirect user by role
                if ($_SESSION['role'] === 'admin') {
                    header('Location: ad_dashboard.php');
                } elseif ($_SESSION['role'] === 'lecturer') {
                    header('Location: lect_dashboard.php');
                } else {
                    header('Location: stud_dashboard.php');
                }
                exit;
            } else {
                $error = "Invalid username/email or password.";
            }

        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = "Please enter both username and password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Virtual Learning Platform</title>
  <style>
    /* Reset */
    * {
      margin: 0; padding: 0; box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f3f2f1;
      color: #333;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Menubar */
    .menubar {
      background-color: #0078D4;
      color: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }
    .menubar .logo h2 {
      font-weight: 700;
      letter-spacing: 2px;
    }
    .nav-links {
      list-style: none;
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }
    .nav-links li a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      padding: 8px 12px;
      border-radius: 4px;
      transition: background-color 0.3s;
    }
    .nav-links li a:hover,
    .nav-links li a.active-link {
      background-color: #a6ce39; /* limegreen */
      color: black;
    }

    /* Main login container */
    main {
      flex-grow: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
    }

    .login-container {
      background: white;
      padding: 40px 35px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 420px;
    }

    .login-container h2 {
      color: #0078D4;
      margin-bottom: 30px;
      font-weight: 700;
      text-align: center;
      letter-spacing: 1px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      color: #0078D4;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px 15px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 1rem;
      transition: border-color 0.3s;
    }
    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #a6ce39;
      outline: none;
    }

    .forgot-password {
      text-align: right;
      margin-bottom: 25px;
    }
    .forgot-password a {
      color: #0078D4;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.9rem;
      transition: color 0.3s;
    }
    .forgot-password a:hover {
      color: #a6ce39;
    }

    button[type="submit"] {
      width: 100%;
      background-color: #0078D4;
      color: white;
      font-weight: 700;
      padding: 14px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1.1rem;
      transition: background-color 0.3s;
    }
    button[type="submit"]:hover {
      background-color: #a6ce39;
      color: black;
    }

    /* Optional: Separate login links */
    .alt-login {
      margin-top: 15px;
      text-align: center;
    }
    .alt-login a {
      color: #0078D4;
      font-weight: 600;
      text-decoration: none;
      margin: 0 10px;
      padding: 8px 12px;
      border-radius: 5px;
      border: 1px solid transparent;
      transition: background-color 0.3s, border-color 0.3s;
    }
    .alt-login a:hover {
      background-color: #a6ce39;
      color: black;
      border-color: #8abf22;
    }

    /* Error message */
    .error-message {
      background-color: #f8d7da;
      color: #842029;
      border: 1px solid #f5c2c7;
      padding: 12px 15px;
      margin-bottom: 25px;
      border-radius: 6px;
      font-weight: 600;
      text-align: center;
    }

    /* Footer */
    footer {
      background-color: #0078D4;
      color: white;
      text-align: center;
      padding: 15px 10px;
      font-weight: 600;
      letter-spacing: 1px;
    }

    /* Responsive */
    @media (max-width: 480px) {
      .login-container {
        padding: 30px 20px;
        margin: 0 15px;
      }
      .menubar {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
      .nav-links {
        justify-content: flex-start;
        gap: 10px;
      }
    }
  </style>
</head>
<body>

  <!-- Menubar -->
  <div class="menubar">
    <div class="logo">
      <h2>V.L PLATFORM</h2>
    </div>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="login.php" class="active-link">Login</a></li>
      <li><a href="#">About</a></li>
      <li><a href="#">Courses</a></li>
      <li><a href="#">How It Works</a></li>
      <li><a href="#">Contact</a></li>
    </ul>
  </div>

  <!-- Login form -->
  <main>
    <div class="login-container">
      <h2>Login to Your Account</h2>

      <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="login_process.php" novalidate>
        <label for="username">Username</label>
        <input
          type="text"
          id="username"
          name="username"
          placeholder="Enter your username"
          required
          autocomplete="username"
          autofocus
        />

        <label for="password">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="Enter your password"
          required
          autocomplete="current-password"
        />

        <div class="forgot-password">
          <a href="forgot_password.php">Forgot Password?</a>
        </div>

        <button type="submit">Login</button>
      </form>

      <div class="alt-login">
        <p>Or login as:</p>
        <a href="stud_dashboard.php">Student</a>
        <a href="lect_dashboard.php">Lecturer</a>
        <a href="ad_dashboard.php">Administrator</a>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    &copy; <?= date('Y') ?> Virtual Learning Platform. All rights reserved.
  </footer>

</body>
</html>
