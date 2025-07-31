<?php
session_start();
require 'conn.php'; // Ensure this file establishes a $conn variable

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $message = "❌ Please fill in both fields.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, full_name, password_hash, user_type FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id, $full_name, $password_hash, $user_type);
            $stmt->fetch();

            if (password_verify($password, $password_hash)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['full_name'] = $full_name;
                $_SESSION['user_type'] = $user_type;

                header("Location: dashboard.php");
                exit();
            } else {
                $message = "❌ Incorrect password.";
            }
        } else {
            $message = "❌ Email not found.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WarehousePro - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a365d; /* Deep blue */
            --secondary: #2a9d8f; /* Teal */
            --accent: #f4a261; /* Sandy orange */
            --light: #f8f9fa; /* Light background */
            --dark: #1b263b; /* Darker navy */
            --text: #333;
            --gray: #6c757d;
            --danger: #e76f51;
            --success: #2a9d8f;
            --warning: #e9c46a;
            --info: #4cc9f0;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        /* Dark mode variables */
        .dark-mode {
            --primary: #0a1128;
            --secondary: #1a7e72;
            --light: #121212;
            --dark: #e0e0e0;
            --text: #f0f0f0;
            --gray: #9e9e9e;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light);
            color: var(--text);
            line-height: 1.6;
            position: relative;
            min-height: 100vh;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect width="200" height="200" fill="%231a365d" opacity="0.03"/><path d="M0,100 Q50,50 100,100 T200,100" stroke="%23f4a261" stroke-width="1" fill="none" opacity="0.1"/></svg>');
            transition: var(--transition);
        }

        h1, h2, h3, h4 {
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-weight: 700;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .btn {
            display: inline-block;
            padding: 14px 32px;
            background-color: var(--secondary);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-size: 16px;
            box-shadow: var(--shadow);
        }

        .btn:hover {
            background-color: #1a7e72;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--secondary);
            color: var(--secondary);
        }

        .btn-outline:hover {
            background-color: var(--secondary);
            color: white;
        }

        /* Header Styles */
        header {
            background: linear-gradient(135deg, var(--primary) 0%, #1a365d 100%);
            padding: 15px 0;
            box-shadow: var(--shadow);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            font-size: 32px;
            color: var(--accent);
        }

        .logo-text {
            font-size: 24px;
            font-weight: 800;
            color: white;
            letter-spacing: 1px;
        }

        .logo-text span {
            color: var(--accent);
        }

        /* Theme Toggle Button */
        .theme-toggle {
            background: transparent;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: var(--transition);
            padding: 8px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-toggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Login Section */
        .login-section {
            display: flex;
            min-height: calc(100vh - 70px);
            align-items: center;
            padding: 60px 0;
        }

        .login-container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .dark-mode .login-container {
            background-color: #1e1e1e;
        }

        .login-image {
            flex: 1;
            background: linear-gradient(135deg, rgba(26, 54, 93, 0.9) 0%, rgba(42, 157, 143, 0.8) 100%), 
                        url('https://images.unsplash.com/photo-1600585152220-90363fe7e115?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
            color: white;
            position: relative;
        }

        .login-image h2 {
            font-size: 32px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .login-image p {
            margin-bottom: 30px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .login-image ul {
            list-style: none;
            margin-bottom: 40px;
            position: relative;
            z-index: 1;
        }

        .login-image li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .login-image i {
            color: var(--accent);
        }

        .login-form {
            flex: 1;
            padding: 60px 40px;
        }

        .dark-mode .login-form {
            color: var(--text);
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-header h2 {
            font-size: 32px;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .form-header p {
            color: var(--gray);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        .form-control {
            width: 100%;
            padding: 14px 20px 14px 45px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: var(--transition);
            background-color: var(--light);
            color: var(--text);
        }

        .dark-mode .form-control {
            border-color: #444;
            background-color: #2d2d2d;
        }

        .form-control:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(42, 157, 143, 0.2);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input {
            width: 16px;
            height: 16px;
        }

        .forgot-password {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .form-footer {
            margin-top: 30px;
            text-align: center;
        }

        .form-footer p {
            margin-top: 20px;
            color: var(--gray);
        }

        .form-footer a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 500;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        /* Social Login */
        .social-login {
            margin: 30px 0;
            text-align: center;
        }

        .social-login p {
            position: relative;
            color: var(--gray);
            margin-bottom: 20px;
        }

        .social-login p::before,
        .social-login p::after {
            content: "";
            position: absolute;
            height: 1px;
            width: 30%;
            background-color: #ddd;
            top: 50%;
        }

        .dark-mode .social-login p::before,
        .dark-mode .social-login p::after {
            background-color: #444;
        }

        .social-login p::before {
            left: 0;
        }

        .social-login p::after {
            right: 0;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            transition: var(--transition);
            cursor: pointer;
        }

        .social-icon:hover {
            transform: translateY(-3px);
        }

        .google {
            background-color: #DB4437;
        }

        .microsoft {
            background-color: #00A1F1;
        }

        .linkedin {
            background-color: #0077B5;
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--primary) 0%, #1a365d 100%);
            color: white;
            padding: 30px 0;
            text-align: center;
        }

        .copyright {
            font-size: 14px;
            opacity: 0.8;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                max-width: 600px;
            }
            
            .login-image {
                padding: 30px;
            }
            
            .login-form {
                padding: 40px 30px;
            }
        }

        @media (max-width: 576px) {
            .login-image h2 {
                font-size: 26px;
            }
            
            .form-header h2 {
                font-size: 26px;
            }
            
            .form-control {
                padding: 12px 15px 12px 40px;
            }
            
            .remember-forgot {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-container">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div class="logo-text">Warehouse<span>Pro</span></div>
            </div>
            <button class="theme-toggle" id="themeToggle">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </header>

    <!-- Login Section -->
    <section class="login-section">
        <div class="container">
            <div class="login-container">
                <div class="login-image">
                    <h2>Welcome to WarehousePro</h2>
                    <p>Log in to access your warehouse management dashboard and streamline your inventory operations.</p>
                    
                    <ul>
                        <li><i class="fas fa-check-circle"></i> Real-time inventory tracking</li>
                        <li><i class="fas fa-check-circle"></i> Automated stock management</li>
                        <li><i class="fas fa-check-circle"></i> Advanced reporting tools</li>
                        <li><i class="fas fa-check-circle"></i> Multi-location support</li>
                    </ul>
                    
                    <p>Don't have an account? <a href="registerx.php" style="color: var(--accent); font-weight: 500;"> click here</a></p>
                </div>
                
                <div class="login-form"  >>
                    <div class="form-header">
                        <h2>Sign In</h2>
                        <p>Enter your credentials to access your account</p>
                    </div>
                    
                    <form id="loginForm" method="POST" action="dashboard.php">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email" class="form-control" placeholder="john@example.com" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password" class="form-control" placeholder="Enter your password" required>
                            </div>
                        </div>
                        
                        <div class="remember-forgot">
                            <div class="remember-me">
                                <input type="checkbox" id="remember">
                                <label for="remember">Remember me</label>
                            </div>
                            <a href="#" class="forgot-password">Forgot password?</a>
                        </div>
                        
                        <div class="form-group" style="margin-top: 30px;">
                            <button type="submit" class="btn" style="width: 100%;">
                                <i class="fas fa-sign-in-alt"></i> Sign In
                            </button>
                        </div>
                        
                        <div class="social-login">
                            <p>Or sign in with</p>
                            <div class="social-icons">
                                <div class="social-icon google">
                                    <i class="fab fa-google"></i>
                                </div>
                                <div class="social-icon microsoft">
                                    <i class="fab fa-microsoft"></i>
                                </div>
                                <div class="social-icon linkedin">
                                    <i class="fab fa-linkedin-in"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-footer">
                            <p>By signing in, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="copyright">
                &copy; 2023 WarehousePro Management System. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
    

        // Social login buttons
        document.querySelectorAll('.social-icon').forEach(icon => {
            icon.addEventListener('click', function() {
                const provider = this.classList.contains('google') ? 'Google' : 
                               this.classList.contains('microsoft') ? 'Microsoft' : 'LinkedIn';
                alert(`Redirecting to ${provider} login...`);
                // In a real app, this would redirect to the OAuth provider
            });
        });

        // Theme toggle functionality
        const themeToggle = document.getElementById('themeToggle');
        const icon = themeToggle.querySelector('i');
        
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            
            const isDarkMode = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDarkMode);
            
            // Change icon based on mode
            if (isDarkMode) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }
        
        themeToggle.addEventListener('click', toggleTheme);

        // Check for saved theme preference
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }

        // Forgot password functionality
        document.querySelector('.forgot-password').addEventListener('click', function(e) {
            e.preventDefault();
            const email = prompt('Please enter your email address to reset your password:');
            if (email) {
                alert(`Password reset link has been sent to ${email}`);
            }
        });
    </script>
</body>
</html> 
