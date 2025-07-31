<?php
require 'conn.php'; // DB connection
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Grab inputs
    $fullName   = trim($_POST['fullName'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $userType   = $_POST['userType'] ?? '';
    $department = $_POST['department'] ?? '';
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirmPassword'] ?? '';

    // Validate required fields
    if (empty($fullName) || empty($email) || empty($phone) || empty($userType) || empty($department) || empty($password) || empty($confirm)) {
        $message = "❌ All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Invalid email format.";
    } elseif ($password !== $confirm) {
        $message = "❌ Passwords do not match.";
    } elseif (!in_array($userType, ['admin', 'staff'])) {
        $message = "❌ Invalid user type selected.";
    } elseif (!in_array($department, ['inventory', 'shipping', 'quality', 'logistics', 'admin'])) {
        $message = "❌ Invalid department selected.";
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "❌ Email already registered.";
        } else {
            $check->close();

            // Hash password
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, user_type, department, password_hash) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $fullName, $email, $phone, $userType, $department, $hashed);

            if ($stmt->execute()) {
                header("Location: login.php?register=success");
                exit();
            } else {
                $message = "❌ Registration failed. Please try again.";
            }
            $stmt->close();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WarehousePro - Register</title>
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

        /* Register Section */
        .register-section {
            display: flex;
            min-height: calc(100vh - 70px);
            align-items: center;
            padding: 60px 0;
        }

        .register-container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .dark-mode .register-container {
            background-color: #1e1e1e;
        }

        .register-image {
            flex: 1;
            background: linear-gradient(135deg, rgba(26, 54, 93, 0.9) 0%, rgba(42, 157, 143, 0.8) 100%), 
                        url('https://images.unsplash.com/photo-1556740738-b6a63e27c4df?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
            color: white;
            position: relative;
        }

        .register-image h2 {
            font-size: 32px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .register-image p {
            margin-bottom: 30px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .register-image ul {
            list-style: none;
            margin-bottom: 40px;
            position: relative;
            z-index: 1;
        }

        .register-image li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .register-image i {
            color: var(--accent);
        }

        .register-form {
            flex: 1;
            padding: 60px 40px;
        }

        .dark-mode .register-form {
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

        .select-control {
            width: 100%;
            padding: 14px 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: var(--transition);
            background-color: var(--light);
            color: var(--text);
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }

        .dark-mode .select-control {
            border-color: #444;
            background-color: #2d2d2d;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        }

        .select-control:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(42, 157, 143, 0.2);
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .radio-option input {
            width: 16px;
            height: 16px;
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
            .register-container {
                flex-direction: column;
                max-width: 600px;
            }
            
            .register-image {
                padding: 30px;
            }
            
            .register-form {
                padding: 40px 30px;
            }
        }

        @media (max-width: 576px) {
            .register-image h2 {
                font-size: 26px;
            }
            
            .form-header h2 {
                font-size: 26px;
            }
            
            .form-control {
                padding: 12px 15px 12px 40px;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 10px;
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

    <!-- Register Section -->
    <section class="register-section">
        <div class="container">
            <div class="register-container">
                <div class="register-image">
                    <h2>Join WarehousePro</h2>
                    <p>Register your account to access our comprehensive warehouse management system and optimize your inventory operations.</p>
                    
                    <ul>
                        <li><i class="fas fa-check-circle"></i> Streamline your warehouse operations</li>
                        <li><i class="fas fa-check-circle"></i> Track inventory in real-time</li>
                        <li><i class="fas fa-check-circle"></i> Generate detailed reports</li>
                        <li><i class="fas fa-check-circle"></i> Manage multiple warehouse locations</li>
                    </ul>
                    
                    <p>Already have an account? <a href="login.php" style="color: var(--accent); font-weight: 500;">Sign in here</a></p>
                </div>
                
                <div class="register-form">
                    <div class="form-header">
                        <h2>Create Account</h2>
                        <p>Fill in your details to register</p>
                    </div>
                    
                    <?php
                    // Initialize variables
                    $fullName = $email = $phone = $userType = $department = '';
                    $errors = [];
                    
                    // Check if form is submitted
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        // Validate and sanitize inputs
                        $fullName = htmlspecialchars(trim($_POST['fullName'] ?? ''));
                        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
                        $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
                        $userType = htmlspecialchars(trim($_POST['userType'] ?? ''));
                        $department = htmlspecialchars(trim($_POST['department'] ?? ''));
                        $password = $_POST['password'] ?? '';
                        $confirmPassword = $_POST['confirmPassword'] ?? '';
                        
                        // Validation
                        if (empty($fullName)) {
                            $errors['fullName'] = 'Full name is required';
                        }
                        
                        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $errors['email'] = 'Valid email is required';
                        }
                        
                        if (empty($phone)) {
                            $errors['phone'] = 'Phone number is required';
                        }
                        
                        if (empty($userType) || !in_array($userType, ['admin', 'staff'])) {
                            $errors['userType'] = 'Please select a valid user type';
                        }
                        
                        if (empty($department)) {
                            $errors['department'] = 'Please select a department';
                        }
                        
                        if (empty($password)) {
                            $errors['password'] = 'Password is required';
                        } elseif (strlen($password) < 8) {
                            $errors['password'] = 'Password must be at least 8 characters';
                        }
                        
                        if ($password !== $confirmPassword) {
                            $errors['confirmPassword'] = 'Passwords do not match';
                        }
                        
                        // If no errors, process registration
                        if (empty($errors)) {
                            // In a real application, you would:
                            // 1. Hash the password
                            // 2. Save to database
                            // 3. Redirect to success page or login
                            
                            // For demonstration, we'll just show a success message
                            echo '<div class="alert alert-success" style="background-color: var(--success); color: white; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                                Registration successful! You can now <a href="login.php" style="color: white; text-decoration: underline;">login</a>.
                            </div>';
                            
                            // Reset form fields
                            $fullName = $email = $phone = $userType = $department = '';
                        }
                    }
                    ?>
                    
                    <form id="registrationForm" method="POST" action="">
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" id="fullName" name="fullName" class="form-control" placeholder="John Smith" 
                                       value="<?php echo htmlspecialchars($fullName); ?>" required>
                            </div>
                            <?php if (isset($errors['fullName'])): ?>
                                <small style="color: var(--danger);"><?php echo $errors['fullName']; ?></small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com" 
                                       value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>
                            <?php if (isset($errors['email'])): ?>
                                <small style="color: var(--danger);"><?php echo $errors['email']; ?></small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <div class="input-with-icon">
                                <i class="fas fa-phone"></i>
                                <input type="tel" id="phone" name="phone" class="form-control" placeholder="+1 (555) 123-4567" 
                                       value="<?php echo htmlspecialchars($phone); ?>" required>
                            </div>
                            <?php if (isset($errors['phone'])): ?>
                                <small style="color: var(--danger);"><?php echo $errors['phone']; ?></small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label>User Type</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input type="radio" id="admin" name="userType" value="admin" 
                                           <?php echo ($userType === 'admin') ? 'checked' : ''; ?> required>
                                    <label for="admin">Administrator</label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" id="staff" name="userType" value="staff" 
                                           <?php echo ($userType === 'staff') ? 'checked' : ''; ?> required>
                                    <label for="staff">Staff Member</label>
                                </div>
                            </div>
                            <?php if (isset($errors['userType'])): ?>
                                <small style="color: var(--danger);"><?php echo $errors['userType']; ?></small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="department">Department</label>
                            <select id="department" name="department" class="select-control" required>
                                <option value="">Select Department</option>
                                <option value="inventory" <?php echo ($department === 'inventory') ? 'selected' : ''; ?>>Inventory Management</option>
                                <option value="shipping" <?php echo ($department === 'shipping') ? 'selected' : ''; ?>>Shipping & Receiving</option>
                                <option value="quality" <?php echo ($department === 'quality') ? 'selected' : ''; ?>>Quality Control</option>
                                <option value="logistics" <?php echo ($department === 'logistics') ? 'selected' : ''; ?>>Logistics</option>
                                <option value="admin" <?php echo ($department === 'admin') ? 'selected' : ''; ?>>Administration</option>
                            </select>
                            <?php if (isset($errors['department'])): ?>
                                <small style="color: var(--danger);"><?php echo $errors['department']; ?></small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
                            </div>
                            <?php if (isset($errors['password'])): ?>
                                <small style="color: var(--danger);"><?php echo $errors['password']; ?></small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm your password" required>
                            </div>
                            <?php if (isset($errors['confirmPassword'])): ?>
                                <small style="color: var(--danger);"><?php echo $errors['confirmPassword']; ?></small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group" style="margin-top: 30px;">
                            <button type="submit" class="btn" style="width: 100%;">
                                <i class="fas fa-user-plus"></i> Register Account
                            </button>
                        </div>
                        
                        <div class="form-footer">
                            <p>By registering, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</p>
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
                &copy; <?php echo date('Y'); ?> WarehousePro Management System. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
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
    </script>
</body>
</html>
