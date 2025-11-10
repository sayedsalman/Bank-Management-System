<?php
session_start();
require_once 'config.php';

// Initialize error variable
$error = '';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    redirectBasedOnRole();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate credentials
    $user = validateCredentials($username, $password);
    
    if ($user) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        // Log login action
        logAction('LOGIN', "User logged in: " . $username);
        
        // Redirect based on role
        redirectBasedOnRole();
    } else {
        $error = "Invalid username or password!";
        logAction('LOGIN_FAILED', "Failed login attempt for username: " . $username);
    }
}

function validateCredentials($username, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Check if password matches (both hashed and plain text for development)
            if (password_verify($password, $user['password'])) {
                return $user;
            }
            // Fallback for existing plain text passwords in development
            elseif ($user['password'] === $password) {
                // Upgrade to hashed password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $updateStmt->execute([$hashedPassword, $user['id']]);
                return $user;
            }
        }
        return false;
    } catch (PDOException $e) {
        error_log("Database error in validateCredentials: " . $e->getMessage());
        return false;
    }
}

function redirectBasedOnRole() {
    $role = $_SESSION['role'] ?? '';
    
    switch($role) {
        case 'admin':
            header('Location: admin.php');
            break;
        case 'employee':
            header('Location: employee.php');
            break;
        case 'customer':
            header('Location: customer.php');
            break;
        default:
            header('Location: login.php');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LosersBank | Login</title>
    <link rel="icon" type="image/png" sizes="32x32" href="https://salman.rfnhsc.com/bank/uploads/losers.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://salman.rfnhsc.com/bank/uploads/losers.png">
    <link rel="shortcut icon" href="https://salman.rfnhsc.com/bank/uploads/losers.png">
    
    <!-- GSAP Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* All the original CSS styles remain the same */
        :root {
            --primary-color: #1a2a6c;
            --secondary-color: #b21f1f;
            --accent-color: #fdbb2d;
            --text-color: #333;
            --light-gray: #e1e1e1;
            --error-color: #b21f1f;
            --success-color: #28a745;
            --white: #fff;
            --shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color), var(--accent-color));
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow: hidden;
            cursor: default;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px);
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color), var(--accent-color));
        }

        .bank-logo {
            margin-bottom: 30px;
            opacity: 0;
            transform: translateY(10px);
        }

        .bank-logo h1 {
            color: var(--primary-color);
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .bank-logo p {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
            opacity: 0;
        }

        .login-form {
            text-align: left;
            opacity: 0;
            transform: translateY(10px);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
            opacity: 0;
            transform: translateY(10px);
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            font-size: 16px;
            transition: var(--transition);
        }

        .form-group input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(26, 42, 108, 0.2);
        }

        .form-group input.valid {
            border-color: var(--success-color);
        }

        .form-group input.invalid {
            border-color: var(--error-color);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 42px;
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            transition: var(--transition);
            font-size: 18px;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .validation-message {
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .validation-message.valid {
            color: var(--success-color);
            display: block;
        }

        .validation-message.invalid {
            color: var(--error-color);
            display: block;
        }

        .tooltip {
            position: relative;
            display: inline-block;
            margin-left: 5px;
            color: var(--primary-color);
            cursor: help;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 200px;
            background-color: var(--primary-color);
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 12px;
            font-weight: normal;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            opacity: 0;
            transform: translateY(10px);
        }

        .remember-me input {
            margin-right: 8px;
        }

        .remember-me label {
            font-size: 14px;
            color: var(--text-color);
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
            opacity: 0;
            transform: translateY(10px);
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .error-message {
            color: var(--error-color);
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
            display: <?php echo isset($error) ? 'block' : 'none'; ?>;
            opacity: <?php echo isset($error) ? 1 : 0; ?>;
        }

        .security-notice {
            margin-top: 25px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 10px;
            font-size: 12px;
            color: #666;
            text-align: center;
            opacity: 0;
            transform: translateY(10px);
        }

        .security-notice i {
            color: var(--primary-color);
            margin-right: 5px;
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
            opacity: 0;
            transform: translateY(10px);
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
            transition: var(--transition);
        }

        .forgot-password a:hover {
            text-decoration: underline;
            color: var(--secondary-color);
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="bank-logo">
            <h1>LosersBank</h1>
            <p>Your Financial Security is Our Priority</p>
        </div>

        <form class="login-form" id="loginForm" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required autocomplete="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                <div class="validation-message" id="usernameValidation"></div>
            </div>

            <div class="form-group">
                <label for="password">
                    Password 
                    <span class="tooltip">
                        <i class="fas fa-info-circle"></i>
                        <span class="tooltiptext">Password must be at least 6 characters</span>
                    </span>
                </label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                <button type="button" class="password-toggle" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
                <div class="validation-message" id="passwordValidation"></div>
            </div>

            <div class="remember-me">
                <input type="checkbox" id="rememberMe" name="remember_me">
                <label for="rememberMe">Remember me</label>
            </div>

            <button type="submit" class="login-btn" id="loginButton">
                <div class="spinner" id="loginSpinner"></div>
                <span id="loginText">Login to Your Account</span>
            </button>

            <div class="error-message" id="errorMessage">
                <?php echo isset($error) ? $error : ''; ?>
            </div>

            <div class="forgot-password">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </form>

        <div class="security-notice">
            <i class="fas fa-shield-alt"></i> We use advanced encryption to protect your information
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const errorMessage = document.getElementById('errorMessage');
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const usernameInput = document.getElementById('username');
            const loginButton = document.getElementById('loginButton');
            const loginSpinner = document.getElementById('loginSpinner');
            const loginText = document.getElementById('loginText');
            const usernameValidation = document.getElementById('usernameValidation');
            const passwordValidation = document.getElementById('passwordValidation');
            const rememberMe = document.getElementById('rememberMe');
            
            // GSAP Animations
            const tl = gsap.timeline();
            
            // Main container animation
            tl.to('.login-container', {
                duration: 0.8,
                opacity: 1,
                y: 0,
                ease: 'power3.out'
            })
            // Logo animation with bounce
            .to('.bank-logo', {
                duration: 0.6,
                opacity: 1,
                y: 0,
                ease: 'bounce.out'
            }, '-=0.4')
            // Slogan animation
            .to('.bank-logo p', {
                duration: 0.8,
                opacity: 1,
                y: 0,
                ease: 'power2.out'
            }, '-=0.3')
            // Form animation
            .to('.login-form', {
                duration: 0.6,
                opacity: 1,
                y: 0,
                ease: 'power2.out'
            }, '-=0.3')
            // Form fields animation with stagger
            .to('.form-group', {
                duration: 0.5,
                opacity: 1,
                y: 0,
                stagger: 0.1,
                ease: 'power2.out'
            }, '-=0.2')
            // Remember me animation
            .to('.remember-me', {
                duration: 0.5,
                opacity: 1,
                y: 0,
                ease: 'power2.out'
            }, '-=0.2')
            // Button animation
            .to('.login-btn', {
                duration: 0.5,
                opacity: 1,
                y: 0,
                ease: 'power2.out'
            }, '-=0.2')
            // Forgot password links animation
            .to('.forgot-password', {
                duration: 0.5,
                opacity: 1,
                y: 0,
                ease: 'power2.out'
            }, '-=0.2')
            // Security notice animation
            .to('.security-notice', {
                duration: 0.5,
                opacity: 1,
                y: 0,
                ease: 'power2.out'
            }, '-=0.2');

            // Auto-focus on username
            gsap.delayedCall(1, () => {
                usernameInput.focus();
            });

            // Check for saved username
            const savedUsername = localStorage.getItem('losersbank_username');
            if (savedUsername) {
                usernameInput.value = savedUsername;
                rememberMe.checked = true;
                
                // Animate the input field when loading saved username
                gsap.fromTo(usernameInput, 
                    { backgroundColor: 'rgba(26, 42, 108, 0.1)' },
                    { backgroundColor: 'white', duration: 1.5, ease: 'power2.out' }
                );
            }

            // Toggle password visibility with eye icon
            togglePassword.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    togglePassword.innerHTML = '<i class="fas fa-eye-slash"></i>';
                    
                    // GSAP animation for toggle
                    gsap.to(togglePassword, {
                        duration: 0.3,
                        scale: 1.2,
                        yoyo: true,
                        repeat: 1
                    });
                } else {
                    passwordInput.type = 'password';
                    togglePassword.innerHTML = '<i class="fas fa-eye"></i>';
                    
                    // GSAP animation for toggle
                    gsap.to(togglePassword, {
                        duration: 0.3,
                        scale: 1.2,
                        yoyo: true,
                        repeat: 1
                    });
                }
            });

            // Real-time validation
            usernameInput.addEventListener('input', function() {
                validateUsername();
            });

            passwordInput.addEventListener('input', function() {
                validatePassword();
            });

            function validateUsername() {
                const username = usernameInput.value.trim();
                
                if (username.length === 0) {
                    usernameInput.classList.remove('valid', 'invalid');
                    usernameValidation.textContent = '';
                    usernameValidation.className = 'validation-message';
                    return false;
                }
                
                // Basic validation - check if username is not empty
                if (username.length >= 3) {
                    usernameInput.classList.remove('invalid');
                    usernameInput.classList.add('valid');
                    usernameValidation.textContent = '✓ Username format is valid';
                    usernameValidation.className = 'validation-message valid';
                    
                    // Animate the validation check
                    gsap.fromTo(usernameValidation, 
                        { scale: 0.8, opacity: 0 },
                        { scale: 1, opacity: 1, duration: 0.3, ease: 'back.out(1.7)' }
                    );
                    return true;
                } else {
                    usernameInput.classList.remove('valid');
                    usernameInput.classList.add('invalid');
                    usernameValidation.textContent = '✗ Username must be at least 3 characters';
                    usernameValidation.className = 'validation-message invalid';
                    return false;
                }
            }

            function validatePassword() {
                const password = passwordInput.value;
                
                if (password.length === 0) {
                    passwordInput.classList.remove('valid', 'invalid');
                    passwordValidation.textContent = '';
                    passwordValidation.className = 'validation-message';
                    return false;
                }
                
                if (password.length >= 6) {
                    passwordInput.classList.remove('invalid');
                    passwordInput.classList.add('valid');
                    passwordValidation.textContent = '✓ Password format is valid';
                    passwordValidation.className = 'validation-message valid';
                    
                    // Animate the validation check
                    gsap.fromTo(passwordValidation, 
                        { scale: 0.8, opacity: 0 },
                        { scale: 1, opacity: 1, duration: 0.3, ease: 'back.out(1.7)' }
                    );
                    return true;
                } else {
                    passwordInput.classList.remove('valid');
                    passwordInput.classList.add('invalid');
                    passwordValidation.textContent = '✗ Password must be at least 6 characters';
                    passwordValidation.className = 'validation-message invalid';
                    return false;
                }
            }

            // Form submission
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const username = usernameInput.value.trim();
                const password = passwordInput.value;

                // Basic validation
                if (!validateUsername() || !validatePassword()) {
                    // Show error with animation
                    errorMessage.textContent = 'Please fix the validation errors above';
                    errorMessage.style.display = 'block';
                    
                    gsap.to(errorMessage, {
                        duration: 0.3,
                        opacity: 1,
                        y: 0,
                        ease: 'power2.out'
                    });
                    
                    return;
                }

                // Show loading spinner
                loginSpinner.style.display = 'block';
                loginText.textContent = 'Logging in...';
                loginButton.disabled = true;
                
                // Save username if "Remember me" is checked
                if (rememberMe.checked) {
                    localStorage.setItem('losersbank_username', username);
                } else {
                    localStorage.removeItem('losersbank_username');
                }

                // Submit the form
                loginForm.submit();
            });

            // Input focus animations
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    gsap.to(this, {
                        duration: 0.3,
                        scale: 1.02,
                        boxShadow: '0 0 0 3px rgba(26, 42, 108, 0.2)',
                        ease: 'power2.out'
                    });
                });
                
                input.addEventListener('blur', function() {
                    gsap.to(this, {
                        duration: 0.3,
                        scale: 1,
                        boxShadow: '0 0 0 0px rgba(26, 42, 108, 0.2)',
                        ease: 'power2.out'
                    });
                });
            });

            // Keyboard UX - Enter key to submit
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    loginForm.dispatchEvent(new Event('submit'));
                }
            });

            // If there's an error message from PHP, animate it
            <?php if (!empty($error)): ?>
            gsap.to(errorMessage, {
                duration: 0.5,
                opacity: 1,
                y: 0,
                ease: 'power2.out'
            });
            
            // Shake animation for error
            gsap.to(loginForm, {
                duration: 0.5,
                x: 0,
                ease: 'power2.out',
                keyframes: {
                    "0%": { x: 0 },
                    "20%": { x: -10 },
                    "40%": { x: 10 },
                    "60%": { x: -10 },
                    "80%": { x: 10 },
                    "100%": { x: 0 }
                }
            });
            <?php endif; ?>
        });
    </script>
</body>
</html>