<?php
session_start();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'warehouse_db');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if username already exists
        $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username already taken. Please choose another.";
        } else {
            // Insert new user with hashed password
            $stmt->close();
            $password_hash = hash('sha256', $password);
            $insert = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            $insert->bind_param("ss", $username, $password_hash);
            if ($insert->execute()) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Error occurred during registration. Please try again.";
            }
            $insert->close();
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register | WMS</title>
  <style>
    /* Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: #0D5EA6;
      background-image: url('https://images.unsplash.com/photo-1581093588401-2688df26daba?auto=format&fit=crop&w=1350&q=80');
      background-size: cover;
      background-position: center;
      padding: 20px;
    }

    .register-container {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px 35px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      width: 350px;
      max-width: 100%;
      text-align: center;
    }

    h2 {
      margin-bottom: 30px;
      color: #222;
      font-weight: 700;
      font-size: 28px;
    }

    .input-group {
      margin-bottom: 22px;
      text-align: left;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      font-size: 15px;
      color: #555;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px 15px;
      font-size: 16px;
      border: 1.8px solid #ccc;
      border-radius: 6px;
      transition: border-color 0.3s ease;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #0d5ea6;
      outline: none;
    }

    button[type="submit"] {
      width: 100%;
      padding: 14px;
      font-size: 18px;
      font-weight: 700;
      background-color: #0d5ea6;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
      background-color: #094a82;
    }

    .login-link {
      margin-top: 25px;
      font-size: 14px;
      color: #444;
    }

    .login-link a {
      color: #0d5ea6;
      text-decoration: none;
      font-weight: 600;
      transition: text-decoration 0.3s ease;
    }

    .login-link a:hover {
      text-decoration: underline;
    }

    .error-message {
      margin-bottom: 18px;
      color: #d32f2f;
      font-weight: 600;
      font-size: 14px;
      text-align: center;
    }

    .success-message {
      margin-bottom: 18px;
      color: #2e7d32;
      font-weight: 600;
      font-size: 14px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="register-container">
    <h2>Create Your Account</h2>

    <?php if (!empty($error)): ?>
      <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
    <?php elseif (!empty($success)): ?>
      <p class="success-message"><?php echo $success; ?></p>
    <?php endif; ?>

    <form action="register.php" method="post" autocomplete="off">
      <div class="input-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus />
      </div>

      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
      </div>

      <div class="input-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required />
      </div>

      <button type="submit">Register</button>
    </form>

    <p class="login-link">
      Already have an account? <a href="login.php">Login here</a>
    </p>
  </div>
</body>
</html>
