<?php
// register.php
require_once 'config.php';

$success_message = '';
$error_message = '';

// Get cities, branches, and account types from database
try {
    $cities = $pdo->query("SELECT DISTINCT city FROM branches ORDER BY city")->fetchAll(PDO::FETCH_ASSOC);
    $branches = $pdo->query("SELECT * FROM branches ORDER BY city, name")->fetchAll(PDO::FETCH_ASSOC);
    $account_types = $pdo->query("SELECT * FROM account_types ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $email = trim($_POST['email'] ?? '');
        $first_name = trim($_POST['firstName'] ?? '');
        $last_name = trim($_POST['lastName'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $nid_number = trim($_POST['nid'] ?? '');
        $date_of_birth = $_POST['dob'] ?? '';
        $branch_id = $_POST['branch'] ?? '';
        $account_type_id = $_POST['accountType'] ?? '';
        $initial_deposit = $_POST['initialDeposit'] ?? '';

        // Validation
        if (empty($username) || empty($password) || empty($email) || empty($first_name) || empty($last_name) || 
            empty($phone) || empty($address) || empty($city) || empty($nid_number) || empty($date_of_birth) || 
            empty($branch_id) || empty($account_type_id) || empty($initial_deposit)) {
            $error_message = "All fields are required.";
        } elseif (strlen($password) < 6) {
            $error_message = "Password must be at least 6 characters long.";
        } elseif ($initial_deposit < 1000) {
            $error_message = "Initial deposit must be at least BDT 1000.";
        } else {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT username FROM users WHERE username = ? UNION SELECT username FROM s_pending_registrations WHERE username = ?");
            $stmt->execute([$username, $username]);
            
            if ($stmt->fetch()) {
                $error_message = "Username already exists. Please choose a different username.";
            } else {
                // Check if email already exists
                $stmt = $pdo->prepare("SELECT email FROM users WHERE email = ? UNION SELECT email FROM s_pending_registrations WHERE email = ?");
                $stmt->execute([$email, $email]);
                
                if ($stmt->fetch()) {
                    $error_message = "Email already exists. Please use a different email address.";
                } else {
                    // Check if NID already exists
                    $stmt = $pdo->prepare("SELECT nid_number FROM users WHERE nid_number = ? UNION SELECT nid_number FROM s_pending_registrations WHERE nid_number = ?");
                    $stmt->execute([$nid_number, $nid_number]);
                    
                    if ($stmt->fetch()) {
                        $error_message = "National ID already registered. Please check your NID number.";
                    } else {
                        // Get account type minimum balance
                        $stmt = $pdo->prepare("SELECT min_balance FROM account_types WHERE id = ?");
                        $stmt->execute([$account_type_id]);
                        $account_type = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if (!$account_type) {
                            $error_message = "Invalid account type selected.";
                        } elseif ($initial_deposit < $account_type['min_balance']) {
                            $error_message = "Initial deposit must be at least BDT " . number_format($account_type['min_balance'], 2) . " for the selected account type.";
                        } else {
                            // Insert into pending registrations
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            
                            $stmt = $pdo->prepare("INSERT INTO s_pending_registrations (username, password, email, first_name, last_name, phone, address, city, nid_number, date_of_birth, branch_id, account_type_id, initial_deposit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt->execute([
                                $username, $hashed_password, $email, $first_name, $last_name, $phone, 
                                $address, $city, $nid_number, $date_of_birth, $branch_id, 
                                $account_type_id, $initial_deposit
                            ]);

                            $success_message = "Your registration has been submitted successfully! It will be reviewed by our staff within 24-48 hours. You will receive an email once your account is approved.";
                            
                            logAction('REGISTRATION_SUBMITTED', "New registration submitted for: $username");
                            
                            // Clear form fields after successful submission
                            $_POST = array();
                        }
                    }
                }
            }
        }
    } catch (Exception $e) {
        $error_message = "An error occurred during registration. Please try again. Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOSERS BANK - Open Your Account</title>
    <link rel="icon" type="image/png" sizes="32x32" href="https://salman.rfnhsc.com/bank/uploads/losers.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        :root {
            --primary-color: #1a2a6c;
            --secondary-color: #b21f1f;
            --accent-color: #fdbb2d;
            --text-color: #333;
            --light-gray: #e1e1e1;
            --error-color: #b21f1f;
            --success-color: #28a745;
            --white: #fff;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: var(--shadow);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .logo img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
        }
        
        .bank-name {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .bank-tagline {
            font-size: 16px;
            color: var(--secondary-color);
            font-weight: 500;
        }
        
        .main-content {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .welcome-section {
            flex: 1;
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-section h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 28px;
            border-bottom: 2px solid var(--accent-color);
            padding-bottom: 10px;
        }
        
        .registration-form {
            flex: 1;
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }
        
        .form-title {
            color: var(--primary-color);
            margin-bottom: 25px;
            font-size: 24px;
            text-align: center;
            border-bottom: 2px solid var(--accent-color);
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(26, 42, 108, 0.2);
            outline: none;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .btn {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 14px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
        }
        
        .btn:hover {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .required::after {
            content: " *";
            color: var(--error-color);
        }
        
        .success-message {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid var(--success-color);
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            text-align: center;
            color: var(--success-color);
            font-weight: 500;
        }
        
        .error-message {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid var(--error-color);
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            text-align: center;
            color: var(--error-color);
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .main-content {
                flex-direction: column;
            }
            
            header {
                flex-direction: column;
                text-align: center;
            }
            
            .logo-container {
                margin-bottom: 15px;
                justify-content: center;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo-container">
                <div class="logo">
                    <img src="https://salman.rfnhsc.com/bank/uploads/losers.png" alt="Losers Bank Logo">
                </div>
                <div>
                    <h1 class="bank-name">LOSERS BANK</h1>
                    <p class="bank-tagline">Your Trust, Our Responsibility - Serving Chattogram since 1995</p>
                </div>
            </div>
            <div>
                <p><i class="fas fa-phone"></i> +880 31-XXXXXX</p>
                <p><i class="fas fa-map-marker-alt"></i> Chattogram, Bangladesh</p>
            </div>
        </header>
        
        <div class="main-content">
            <section class="welcome-section">
                <h2>Welcome to LOSERS BANK</h2>
                <p>Opening a bank account with LOSERS BANK is your first step towards financial security and prosperity. We offer a range of accounts tailored to meet your personal and business banking needs.</p>
                
                <?php if ($success_message): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                </div>
                <?php endif; ?>
                
                <div style="margin-top: 25px;">
                    <h3 style="color: var(--primary-color); margin-bottom: 15px;">Why Choose LOSERS BANK?</h3>
                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <i class="fas fa-shield-alt" style="background: var(--primary-color); color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;"></i>
                        <span>Advanced Security & Fraud Protection</span>
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <i class="fas fa-mobile-alt" style="background: var(--primary-color); color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;"></i>
                        <span>24/7 Mobile & Online Banking</span>
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <i class="fas fa-hand-holding-usd" style="background: var(--primary-color); color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;"></i>
                        <span>Competitive Interest Rates</span>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-users" style="background: var(--primary-color); color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;"></i>
                        <span>Dedicated Customer Support</span>
                    </div>
                </div>
            </section>
            
            <section class="registration-form">
                <h2 class="form-title">Open Your Account Now</h2>
                <form id="registrationForm" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName" class="required">First Name</label>
                            <input type="text" id="firstName" name="firstName" class="form-control" value="<?php echo htmlspecialchars($_POST['firstName'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName" class="required">Last Name</label>
                            <input type="text" id="lastName" name="lastName" class="form-control" value="<?php echo htmlspecialchars($_POST['lastName'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="required">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone" class="required">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="nid" class="required">National ID Number</label>
                        <input type="text" id="nid" name="nid" class="form-control" value="<?php echo htmlspecialchars($_POST['nid'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="dob" class="required">Date of Birth</label>
                        <input type="date" id="dob" name="dob" class="form-control" value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address" class="required">Address</label>
                        <textarea id="address" name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="city" class="required">City</label>
                        <select id="city" name="city" class="form-control" required>
                            <option value="">Select City</option>
                            <?php foreach ($cities as $city): ?>
                            <option value="<?php echo htmlspecialchars($city['city']); ?>" 
                                <?php echo ($_POST['city'] ?? '') === $city['city'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($city['city']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="branch" class="required">Branch</label>
                        <select id="branch" name="branch" class="form-control" required>
                            <option value="">Select Branch</option>
                            <?php foreach ($branches as $branch): ?>
                            <option value="<?php echo $branch['id']; ?>" data-city="<?php echo htmlspecialchars($branch['city']); ?>"
                                <?php echo ($_POST['branch'] ?? '') == $branch['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($branch['name']); ?> - <?php echo htmlspecialchars($branch['city']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="accountType" class="required">Account Type</label>
                        <select id="accountType" name="accountType" class="form-control" required>
                            <option value="">Select Account Type</option>
                            <?php foreach ($account_types as $type): ?>
                            <option value="<?php echo $type['id']; ?>" data-min-balance="<?php echo $type['min_balance']; ?>"
                                <?php echo ($_POST['accountType'] ?? '') == $type['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type['name']); ?> (Min: BDT <?php echo number_format($type['min_balance'], 2); ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username" class="required">Username</label>
                            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password" class="required">Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="initialDeposit" class="required">Initial Deposit (BDT)</label>
                        <input type="number" id="initialDeposit" name="initialDeposit" class="form-control" min="1000" step="0.01" 
                               value="<?php echo htmlspecialchars($_POST['initialDeposit'] ?? '1000'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="terms" required <?php echo isset($_POST['terms']) ? 'checked' : ''; ?>>
                            I agree to the Terms & Conditions and Privacy Policy
                        </label>
                    </div>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-user-plus"></i> Submit for Approval
                    </button>
                </form>
            </section>
        </div>
        
        <footer style="background: rgba(255, 255, 255, 0.9); padding: 20px; border-radius: 10px; text-align: center; margin-top: 30px;">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px; margin-bottom: 20px;">
                    <h3 style="color: var(--primary-color); margin-bottom: 15px;">Contact Us</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Agrabad Branch, Chattogram</p>
                    <p><i class="fas fa-phone"></i> +880 31-625841</p>
                    <p><i class="fas fa-envelope"></i> info@losersbank.com</p>
                </div>
                
                <div style="flex: 1; min-width: 250px; margin-bottom: 20px;">
                    <h3 style="color: var(--primary-color); margin-bottom: 15px;">Quick Links</h3>
                    <a href="index.php" style="color: #555; margin-bottom: 8px; display: block; text-decoration: none;">Home</a>
                    <a href="index.php#about" style="color: #555; margin-bottom: 8px; display: block; text-decoration: none;">About Us</a>
                    <a href="index.php" style="color: #555; margin-bottom: 8px; display: block; text-decoration: none;">Services</a>
                    <a href="index.php" style="color: #555; margin-bottom: 8px; display: block; text-decoration: none;">Contact</a>
                </div>
            </div>
            
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; color: #777;">
                <p>&copy; 2023 LOSERS BANK. All Rights Reserved. | Regulated by Bangladesh Bank</p>
            </div>
        </footer>
    </div>

    <script>

document.addEventListener('DOMContentLoaded', function() {
    const citySelect = document.getElementById('city');
    const branchSelect = document.getElementById('branch');
    const accountTypeSelect = document.getElementById('accountType');
    const initialDepositInput = document.getElementById('initialDeposit');
    
    // Store all branch options when page loads
    const allBranchOptions = Array.from(branchSelect.querySelectorAll('option'));
    
    // Filter branches based on selected city
    function filterBranches() {
        const selectedCity = citySelect.value;
        
        // Clear current options except the first one
        branchSelect.innerHTML = '<option value="">Select Branch</option>';
        
        if (selectedCity) {
            // Add branches that match the selected city
            allBranchOptions.forEach(option => {
                if (option.value && option.getAttribute('data-city') === selectedCity) {
                    branchSelect.appendChild(option.cloneNode(true));
                }
            });
            
            // If no branches found for selected city, show message
            if (branchSelect.options.length === 1) {
                const noBranchOption = document.createElement('option');
                noBranchOption.value = '';
                noBranchOption.textContent = 'No branches available for this city';
                branchSelect.appendChild(noBranchOption);
            }
        } else {
            // If no city selected, show all branches
            allBranchOptions.forEach(option => {
                if (option.value) {
                    branchSelect.appendChild(option.cloneNode(true));
                }
            });
        }
    }
    
    // Initial filter on page load based on selected city
    if (citySelect.value) {
        filterBranches();
    }
    
    // Update branches when city changes
    citySelect.addEventListener('change', filterBranches);
    
    // Update minimum deposit based on account type
    accountTypeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const minBalance = parseFloat(selectedOption.getAttribute('data-min-balance'));
        
        if (minBalance > 0) {
            initialDepositInput.min = minBalance;
            const currentValue = parseFloat(initialDepositInput.value);
            if (currentValue < minBalance || isNaN(currentValue)) {
                initialDepositInput.value = minBalance;
            }
        }
    });
    
    // Set data attributes for account types
    <?php foreach ($account_types as $type): ?>
    const option = document.querySelector('option[value="<?php echo $type['id']; ?>"]');
    if (option) {
        option.setAttribute('data-min-balance', '<?php echo $type['min_balance']; ?>');
    }
    <?php endforeach; ?>
    
    // Form validation
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        if (password.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long.');
            return false;
        }
        
        const initialDeposit = parseFloat(document.getElementById('initialDeposit').value);
        const selectedAccountType = document.getElementById('accountType').value;
        
        if (selectedAccountType) {
            const selectedOption = document.querySelector(`#accountType option[value="${selectedAccountType}"]`);
            const minBalance = parseFloat(selectedOption.getAttribute('data-min-balance'));
            if (initialDeposit < minBalance) {
                e.preventDefault();
                alert(`Initial deposit must be at least BDT ${minBalance.toFixed(2)} for the selected account type.`);
                return false;
            }
        }
        
        const selectedBranch = document.getElementById('branch').value;
        if (!selectedBranch) {
            e.preventDefault();
            alert('Please select a branch.');
            return false;
        }
        
        if (!document.querySelector('input[name="terms"]').checked) {
            e.preventDefault();
            alert('You must agree to the Terms & Conditions and Privacy Policy.');
            return false;
        }
    });

    // Debug function to check available branches
    function debugBranches() {
        console.log('All branch options:', allBranchOptions);
        allBranchOptions.forEach(option => {
            if (option.value) {
                console.log(`Branch: ${option.textContent}, City: ${option.getAttribute('data-city')}`);
            }
        });
    }
    
    // Uncomment the line below for debugging
    // debugBranches();
    
    // Add GSAP animations
    gsap.from('header', { duration: 0.8, opacity: 0, y: -20, ease: 'power3.out' });
    gsap.from('.welcome-section', { duration: 0.7, opacity: 0, x: -30, ease: 'power2.out', delay: 0.2 });
    gsap.from('.registration-form', { duration: 0.7, opacity: 0, x: 30, ease: 'power2.out', delay: 0.2 });
    gsap.from('footer', { duration: 0.7, opacity: 0, y: 20, ease: 'power2.out', delay: 0.4 });
});
</script>
    
</body>
</html>