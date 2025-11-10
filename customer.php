<?php
// customer.php
session_start();
require_once 'config.php';

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit();
}

$customer_id = $_SESSION['user_id'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'transfer_money':
            $from_account = $_POST['from_account'];
            $to_account = $_POST['to_account'];
            $amount = $_POST['amount'];
            $description = $_POST['description'];
            transferMoney($from_account, $to_account, $amount, $description, $customer_id);
            break;
            
        case 'mobile_topup':
            $account_id = $_POST['account_id'];
            $operator = $_POST['operator'];
            $phone_number = $_POST['phone_number'];
            $amount = $_POST['amount'];
            mobileTopup($account_id, $operator, $phone_number, $amount, $customer_id);
            break;
            
        case 'create_savings_goal':
            $name = $_POST['name'];
            $target_amount = $_POST['target_amount'];
            $target_date = $_POST['target_date'];
            createSavingsGoal($name, $target_amount, $target_date, $customer_id);
            break;
            
        case 'apply_loan':
            $loan_type = $_POST['loan_type'];
            $amount = $_POST['amount'];
            $purpose = $_POST['purpose'];
            applyForLoan($loan_type, $amount, $purpose, $customer_id);
            break;
            
        case 'update_profile':
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            updateProfile($customer_id, $first_name, $last_name, $email, $phone, $address);
            break;
            
        case 'update_password':
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            updatePassword($customer_id, $current_password, $new_password);
            break;

        case 'create_fd_account':
            $account_type_id = $_POST['account_type_id'];
            $branch_id = $_POST['branch_id'];
            $initial_deposit = $_POST['initial_deposit'];
            $term_months = $_POST['term_months'];
            createFixedDeposit($customer_id, $account_type_id, $branch_id, $initial_deposit, $term_months);
            break;

        case 'create_rd_account':
            $account_type_id = $_POST['account_type_id'];
            $branch_id = $_POST['branch_id'];
            $monthly_deposit = $_POST['monthly_deposit'];
            $term_months = $_POST['term_months'];
            createRecurringDeposit($customer_id, $account_type_id, $branch_id, $monthly_deposit, $term_months);
            break;
    }
}

// Get customer data
$customer = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$customer->execute([$customer_id]);
$customer_data = $customer->fetch(PDO::FETCH_ASSOC);

// Get accounts
$accounts = $pdo->prepare("
    SELECT a.*, at.name as account_type, b.name as branch_name 
    FROM accounts a 
    JOIN account_types at ON a.account_type_id = at.id 
    JOIN branches b ON a.branch_id = b.id 
    WHERE a.user_id = ? AND a.status = 'active'
");
$accounts->execute([$customer_id]);
$customer_accounts = $accounts->fetchAll(PDO::FETCH_ASSOC);

// Get transactions
$transactions = $pdo->prepare("
    SELECT t.*, a.account_number 
    FROM transactions t 
    JOIN accounts a ON t.account_id = a.id 
    WHERE a.user_id = ? 
    ORDER BY t.transaction_date DESC 
    LIMIT 10
");
$transactions->execute([$customer_id]);
$recent_transactions = $transactions->fetchAll(PDO::FETCH_ASSOC);

// Get savings goals
$savings_goals = $pdo->prepare("SELECT * FROM savings_goals WHERE user_id = ? AND status = 'active'");
$savings_goals->execute([$customer_id]);
$customer_savings_goals = $savings_goals->fetchAll(PDO::FETCH_ASSOC);

// Get loans
$loans = $pdo->prepare("SELECT * FROM loans WHERE user_id = ?");
$loans->execute([$customer_id]);
$customer_loans = $loans->fetchAll(PDO::FETCH_ASSOC);

// Get notifications
$notifications = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$notifications->execute([$customer_id]);
$customer_notifications = $notifications->fetchAll(PDO::FETCH_ASSOC);

// Get account types for FD/RD
$account_types = $pdo->query("SELECT * FROM account_types WHERE name IN ('Fixed Deposit', 'Recurring Deposit')")->fetchAll(PDO::FETCH_ASSOC);

// Get branches
$branches = $pdo->query("SELECT * FROM branches")->fetchAll(PDO::FETCH_ASSOC);

// Calculate total balance
$total_balance = 0;
foreach ($customer_accounts as $account) {
    $total_balance += $account['balance'];
}

// Functions
function transferMoney($from_account, $to_account, $amount, $description, $customer_id) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Check if from account belongs to customer
        $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ? AND user_id = ?");
        $stmt->execute([$from_account, $customer_id]);
        $from_account_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$from_account_data) {
            throw new Exception("Invalid account selected");
        }
        
        // Check sufficient balance
        if ($from_account_data['balance'] < $amount) {
            throw new Exception("Insufficient balance");
        }
        
        // Check if to account exists
        $stmt = $pdo->prepare("SELECT * FROM accounts WHERE account_number = ?");
        $stmt->execute([$to_account]);
        $to_account_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$to_account_data) {
            throw new Exception("Recipient account not found");
        }
        
        // Create withdrawal transaction
        $transaction_id = 'TXN-' . date('YmdHis') . rand(100, 999);
        $stmt = $pdo->prepare("INSERT INTO transactions (transaction_id, account_id, type, amount, description, recipient_account, recipient_name, status) VALUES (?, ?, 'transfer', ?, ?, ?, ?, 'completed')");
        $stmt->execute([
            $transaction_id,
            $from_account,
            $amount,
            $description,
            $to_account,
            'External Account'
        ]);
        
        // Update from account balance
        $stmt = $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$amount, $from_account]);
        
        // Create deposit transaction for recipient
        $recipient_transaction_id = 'TXN-' . date('YmdHis') . rand(100, 999);
        $recipient_description = "Transfer from " . $from_account_data['account_number'];
        
        $stmt = $pdo->prepare("INSERT INTO transactions (transaction_id, account_id, type, amount, description, status) VALUES (?, ?, 'deposit', ?, ?, 'completed')");
        $stmt->execute([
            $recipient_transaction_id,
            $to_account_data['id'],
            $amount,
            $recipient_description
        ]);
        
        // Update to account balance
        $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $to_account_data['id']]);
        
        $pdo->commit();
        
        // Create notification
        $notification_message = "Transfer of BDT " . number_format($amount, 2) . " to account " . $to_account . " completed successfully.";
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Transfer Completed', ?, 'success')");
        $stmt->execute([$customer_id, $notification_message]);
        
        logAction($customer_id, 'TRANSFER_COMPLETED', "Customer transferred BDT $amount from account $from_account to $to_account");
        
        $_SESSION['success_message'] = "Transfer completed successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Transfer failed: " . $e->getMessage();
    }
}

function mobileTopup($account_id, $operator, $phone_number, $amount, $customer_id) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Check if account belongs to customer
        $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ? AND user_id = ?");
        $stmt->execute([$account_id, $customer_id]);
        $account_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$account_data) {
            throw new Exception("Invalid account selected");
        }
        
        // Check sufficient balance
        if ($account_data['balance'] < $amount) {
            throw new Exception("Insufficient balance");
        }
        
        // Create transaction
        $transaction_id = 'TXN-' . date('YmdHis') . rand(100, 999);
        $stmt = $pdo->prepare("INSERT INTO transactions (transaction_id, account_id, type, amount, description, status) VALUES (?, ?, 'payment', ?, ?, 'completed')");
        $stmt->execute([
            $transaction_id,
            $account_id,
            $amount,
            "Mobile top-up: $operator - $phone_number"
        ]);
        
        // Update account balance
        $stmt = $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$amount, $account_id]);
        
        $pdo->commit();
        
        // Create notification
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Mobile Top-up', ?, 'success')");
        $stmt->execute([$customer_id, "Mobile top-up of BDT " . number_format($amount, 2) . " to $phone_number ($operator) completed successfully."]);
        
        logAction($customer_id, 'MOBILE_TOPUP', "Customer topped up BDT $amount to $phone_number ($operator)");
        
        $_SESSION['success_message'] = "Mobile top-up completed successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Mobile top-up failed: " . $e->getMessage();
    }
}

function createSavingsGoal($name, $target_amount, $target_date, $customer_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO savings_goals (user_id, name, target_amount, target_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$customer_id, $name, $target_amount, $target_date]);
    
    logAction($customer_id, 'SAVINGS_GOAL_CREATED', "Customer created savings goal: $name");
    $_SESSION['success_message'] = "Savings goal created successfully!";
}

function applyForLoan($loan_type, $amount, $purpose, $customer_id) {
    global $pdo;
    
    $loan_number = 'LN-' . date('YmdHis') . rand(100, 999);
    
    // Default interest rates based on loan type
    $interest_rates = [
        'home' => 7.75,
        'car' => 9.25,
        'personal' => 11.50,
        'business' => 10.00
    ];
    
    $interest_rate = $interest_rates[$loan_type] ?? 10.00;
    $term_months = 60; // Default 5 years
    
    // Calculate monthly payment
    $monthly_interest = $interest_rate / 100 / 12;
    $monthly_payment = ($amount * $monthly_interest * pow(1 + $monthly_interest, $term_months)) / (pow(1 + $monthly_interest, $term_months) - 1);
    
    $stmt = $pdo->prepare("INSERT INTO loans (loan_number, user_id, loan_type, amount, interest_rate, term_months, remaining_balance, monthly_payment, applied_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE())");
    $stmt->execute([
        $loan_number,
        $customer_id,
        $loan_type,
        $amount,
        $interest_rate,
        $term_months,
        $amount,
        $monthly_payment
    ]);
    
    logAction($customer_id, 'LOAN_APPLIED', "Customer applied for $loan_type loan: BDT $amount");
    $_SESSION['success_message'] = "Loan application submitted successfully! Your application is under review.";
}

function updateProfile($customer_id, $first_name, $last_name, $email, $phone, $address) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->execute([$first_name, $last_name, $email, $phone, $address, $customer_id]);
    
    // Update session variables
    $_SESSION['first_name'] = $first_name;
    $_SESSION['last_name'] = $last_name;
    $_SESSION['email'] = $email;
    
    logAction($customer_id, 'PROFILE_UPDATED', "Customer updated profile");
    $_SESSION['success_message'] = "Profile updated successfully!";
}

function updatePassword($customer_id, $current_password, $new_password) {
    global $pdo;
    
    // Verify current password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$customer_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (password_verify($current_password, $user['password'])) {
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $customer_id]);
        
        logAction($customer_id, 'PASSWORD_CHANGED', "Customer changed password");
        $_SESSION['success_message'] = "Password updated successfully!";
    } else {
        $_SESSION['error_message'] = "Current password is incorrect!";
    }
}

function createFixedDeposit($customer_id, $account_type_id, $branch_id, $initial_deposit, $term_months) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Get account type details
        $stmt = $pdo->prepare("SELECT * FROM account_types WHERE id = ?");
        $stmt->execute([$account_type_id]);
        $account_type = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$account_type || $account_type['name'] != 'Fixed Deposit') {
            throw new Exception("Invalid account type for Fixed Deposit");
        }
        
        // Create account number
        $account_number = 'FD' . date('YmdHis') . rand(100, 999);
        
        // Create FD account
        $stmt = $pdo->prepare("INSERT INTO accounts (account_number, user_id, account_type_id, branch_id, balance, opened_date, status) VALUES (?, ?, ?, ?, ?, CURDATE(), 'active')");
        $stmt->execute([$account_number, $customer_id, $account_type_id, $branch_id, $initial_deposit]);
        
        // Create transaction record
        $transaction_id = 'TXN-' . date('YmdHis') . rand(100, 999);
        $stmt = $pdo->prepare("INSERT INTO transactions (transaction_id, account_id, type, amount, description, status) VALUES (?, ?, 'deposit', ?, 'Fixed Deposit Account Opening', 'completed')");
        $stmt->execute([$transaction_id, $pdo->lastInsertId(), $initial_deposit]);
        
        $pdo->commit();
        
        logAction($customer_id, 'FD_CREATED', "Customer created Fixed Deposit: BDT $initial_deposit for $term_months months");
        $_SESSION['success_message'] = "Fixed Deposit account created successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Failed to create Fixed Deposit: " . $e->getMessage();
    }
}

function createRecurringDeposit($customer_id, $account_type_id, $branch_id, $monthly_deposit, $term_months) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Get account type details
        $stmt = $pdo->prepare("SELECT * FROM account_types WHERE id = ?");
        $stmt->execute([$account_type_id]);
        $account_type = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$account_type || $account_type['name'] != 'Recurring Deposit') {
            throw new Exception("Invalid account type for Recurring Deposit");
        }
        
        // Create account number
        $account_number = 'RD' . date('YmdHis') . rand(100, 999);
        
        // Create RD account with initial deposit
        $stmt = $pdo->prepare("INSERT INTO accounts (account_number, user_id, account_type_id, branch_id, balance, opened_date, status) VALUES (?, ?, ?, ?, ?, CURDATE(), 'active')");
        $stmt->execute([$account_number, $customer_id, $account_type_id, $branch_id, $monthly_deposit]);
        
        $account_id = $pdo->lastInsertId();
        
        // Create transaction record for first deposit
        $transaction_id = 'TXN-' . date('YmdHis') . rand(100, 999);
        $stmt = $pdo->prepare("INSERT INTO transactions (transaction_id, account_id, type, amount, description, status) VALUES (?, ?, 'deposit', ?, 'Recurring Deposit Account Opening - First Installment', 'completed')");
        $stmt->execute([$transaction_id, $account_id, $monthly_deposit]);
        
        $pdo->commit();
        
        logAction($customer_id, 'RD_CREATED', "Customer created Recurring Deposit: BDT $monthly_deposit monthly for $term_months months");
        $_SESSION['success_message'] = "Recurring Deposit account created successfully! First installment deposited.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Failed to create Recurring Deposit: " . $e->getMessage();
    }
}

function logAction($user_id, $action, $description) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO s_system_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $action, $description, $_SERVER['REMOTE_ADDR']]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Losers Bank - Customer Dashboard</title>
    <link rel="icon" type="image/png" sizes="32x32" href="https://salman.rfnhsc.com/bank/uploads/losers.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://salman.rfnhsc.com/bank/uploads/losers.png">
    <link rel="shortcut icon" href="https://salman.rfnhsc.com/bank/uploads/losers.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Enhanced Responsive CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        :root {
            --primary: #1a2a6c;
            --secondary: #b21f1f;
            --accent: #fdbb2d;
            --electric-blue: #00a8ff;
            --electric-purple: #9c27b0;
            --light: #f8f9fa;
            --dark: #343a40;
            --success: #28a745;
            --warning: #ff9800;
            --danger: #dc3545;
            --gray: #6c757d;
            --border-radius: 10px;
            --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        body {
            background: linear-gradient(135deg, #0c2461, #1e3799, #4a69bd);
            color: var(--dark);
            display: flex;
            min-height: 100vh;
        }
        
        /* Success/Error Messages */
        .success-message, .error-message {
            padding: 15px;
            margin: 15px 0;
            border-radius: var(--border-radius);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .success-message {
            background: rgba(39, 174, 96, 0.1);
            border: 1px solid var(--success);
            color: var(--success);
        }
        
        .error-message {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid var(--danger);
            color: var(--danger);
        }

        /* Mobile First Responsive Design */
        .sidebar {
            width: 250px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: white;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            transition: var(--transition);
            z-index: 1000;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .sidebar-header h2 {
            font-size: 22px;
            margin-bottom: 5px;
            color: var(--accent);
        }
        
        .sidebar-header p {
            font-size: 12px;
            opacity: 0.8;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu ul {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 4px solid var(--accent);
        }
        
        .sidebar-menu i {
            width: 30px;
            font-size: 18px;
        }
        
        .sidebar-menu span {
            font-weight: 500;
        }
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            transition: var(--transition);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header h1 {
            color: white;
            font-size: clamp(24px, 5vw, 28px);
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .notification-icon {
            position: relative;
            cursor: pointer;
        }
        
        .notification-icon i {
            font-size: 24px;
            color: white;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid var(--accent);
        }
        
        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .page-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .page-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Card Styles */
        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eaeaea;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .card-header h2 {
            color: var(--primary);
            font-size: clamp(18px, 4vw, 20px);
        }
        
        /* Dashboard Page */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .dashboard-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .card-title {
            font-size: 16px;
            color: var(--gray);
            margin-bottom: 10px;
        }
        
        .card-value {
            font-size: clamp(20px, 4vw, 24px);
            font-weight: bold;
            color: var(--primary);
        }
        
        .card-change {
            font-size: 14px;
            margin-top: 5px;
        }
        
        .positive {
            color: var(--success);
        }
        
        .negative {
            color: var(--danger);
        }
        
        /* Account Cards */
        .account-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .account-card {
            background: linear-gradient(135deg, var(--primary), #2a3a8c);
            color: white;
            border-radius: var(--border-radius);
            padding: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .account-card.credit {
            background: linear-gradient(135deg, var(--electric-purple), #7b1fa2);
        }
        
        .account-card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .account-card::after {
            content: '';
            position: absolute;
            bottom: -30px;
            right: 10px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .account-type {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 5px;
        }
        
        .account-balance {
            font-size: clamp(20px, 4vw, 24px);
            font-weight: bold;
            margin: 10px 0;
        }
        
        .account-number {
            font-size: 14px;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }
        
        .account-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            min-height: 40px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-success {
            background-color: var(--success);
            color: white;
        }
        
        .btn-warning {
            background-color: var(--warning);
            color: white;
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid white;
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            opacity: 0.9;
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Transactions Page */
        .transaction-filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filter-select {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: white;
            min-width: 150px;
        }
        
        .transaction-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .transaction-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-radius: var(--border-radius);
            background: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .transaction-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        
        .transaction-info {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
            min-width: 250px;
        }
        
        .transaction-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            flex-shrink: 0;
        }
        
        .transaction-details h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .transaction-details p {
            font-size: 14px;
            color: var(--gray);
        }
        
        .transaction-amount {
            font-weight: bold;
            font-size: 18px;
            flex-shrink: 0;
        }
        
        .income {
            color: var(--success);
        }
        
        .expense {
            color: var(--danger);
        }
        
        /* Savings Page */
        .savings-goal {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .goal-progress {
            flex: 1;
            min-width: 300px;
        }
        
        .progress-bar {
            height: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .progress {
            height: 100%;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border-radius: 5px;
        }
        
        .goal-info {
            text-align: right;
            flex-shrink: 0;
        }
        
        .goal-amount {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary);
        }
        
        /* Loans Page */
        .loan-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .loan-card {
            border-left: 4px solid var(--primary);
        }
        
        .loan-amount {
            font-size: 22px;
            font-weight: bold;
            color: var(--primary);
            margin: 10px 0;
        }
        
        .loan-details {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }
        
        /* Mobile Top-up Page */
        .topup-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .topup-option {
            background: white;
            border: 2px solid #eaeaea;
            border-radius: var(--border-radius);
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .topup-option:hover, .topup-option.selected {
            border-color: var(--primary);
            background-color: rgba(26, 42, 108, 0.05);
        }
        
        .topup-amount {
            font-size: 20px;
            font-weight: bold;
            color: var(--primary);
            margin: 10px 0;
        }
        
        .topup-bonus {
            font-size: 14px;
            color: var(--success);
        }
        
        .phone-input {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .phone-input input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        /* Settings Page */
        .settings-menu {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .settings-tab {
            padding: 10px 20px;
            background: white;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            flex: 1;
            min-width: 120px;
            text-align: center;
        }
        
        .settings-tab.active {
            background: var(--primary);
            color: white;
        }
        
        .settings-content {
            display: none;
        }
        
        .settings-content.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
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
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(26, 42, 108, 0.2);
        }
        
        /* Logout Page */
        .logout-container {
            text-align: center;
            padding: 50px 20px;
        }
        
        .logout-icon {
            font-size: 80px;
            color: var(--primary);
            margin-bottom: 20px;
        }
        
        .logout-message {
            font-size: 24px;
            margin-bottom: 30px;
            color: var(--dark);
        }
        
        /* Notification Panel */
        .notification-panel {
            position: fixed;
            top: 80px;
            right: 20px;
            width: min(350px, 90vw);
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            z-index: 1001;
            display: none;
            overflow: hidden;
        }
        
        .notification-header {
            padding: 15px 20px;
            background: var(--primary);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .notification-item {
            padding: 15px 20px;
            border-bottom: 1px solid #eaeaea;
            display: flex;
            gap: 15px;
            transition: var(--transition);
        }
        
        .notification-item:hover {
            background: #f8f9fa;
        }
        
        .notification-item.unread {
            background: rgba(26, 42, 108, 0.05);
        }
        
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }
        
        .notification-content h4 {
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .notification-content p {
            font-size: 13px;
            color: var(--gray);
        }
        
        .notification-time {
            font-size: 12px;
            color: var(--gray);
            margin-top: 5px;
        }
        
        /* New Features Styles */
        .transfer-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .transfer-option {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            cursor: pointer;
            text-align: center;
        }
        
        .transfer-option:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .transfer-icon {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .transfer-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: var(--primary);
        }
        
        .transfer-description {
            font-size: 14px;
            color: var(--gray);
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1100;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .modal-content {
            background: white;
            border-radius: var(--border-radius);
            width: 100%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.3s ease;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eaeaea;
        }
        
        .modal-header h3 {
            color: var(--primary);
            font-size: 22px;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--gray);
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 25px;
            flex-wrap: wrap;
        }
        
        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            cursor: pointer;
        }
        
        .checkbox-container input {
            margin-right: 10px;
        }
        
        /* FD/RD Account Creation */
        .account-creation-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .account-creation-option {
            background: white;
            border-radius: var(--border-radius);
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            border: 2px solid transparent;
        }
        
        .account-creation-option:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .account-creation-icon {
            font-size: 50px;
            color: var(--primary);
            margin-bottom: 20px;
        }
        
        .account-creation-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
            color: var(--primary);
        }
        
        .account-creation-description {
            font-size: 14px;
            color: var(--gray);
            margin-bottom: 20px;
        }
        
        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: white;
            cursor: pointer;
            padding: 10px;
        }
        
        /* Responsive Styles */
        @media (max-width: 1200px) {
            .sidebar {
                width: 70px;
                overflow: visible;
            }
            
            .sidebar-header h2, .sidebar-header p, .sidebar-menu span {
                display: none;
            }
            
            .sidebar-menu a {
                justify-content: center;
                padding: 15px;
            }
            
            .sidebar-menu i {
                width: auto;
                font-size: 20px;
            }
            
            .main-content {
                margin-left: 70px;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-menu-toggle {
                display: block;
            }
            
            .account-cards, .loan-cards, .dashboard-cards, .transfer-options {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .header-actions {
                align-self: stretch;
                justify-content: space-between;
            }
            
            .notification-panel {
                width: 90vw;
                right: 5vw;
            }
            
            .transaction-info {
                min-width: 200px;
            }
            
            .settings-menu {
                flex-direction: column;
            }
            
            .modal-content {
                padding: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .main-content {
                padding: 10px;
            }
            
            .card {
                padding: 15px;
            }
            
            .btn {
                padding: 10px 15px;
                font-size: 14px;
                width: 100%;
                justify-content: center;
            }
            
            .account-actions {
                flex-direction: column;
            }
            
            .transaction-filters {
                flex-direction: column;
            }
            
            .filter-select {
                width: 100%;
            }
            
            .phone-input {
                flex-direction: column;
            }
            
            .modal-footer {
                flex-direction: column;
            }
            
            .modal-footer .btn {
                width: 100%;
            }
        }
        
        /* Loading States */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--primary);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>Losers Bank</h2>
            <p>Bangladesh's Trusted Bank</p>
        </div>
        <div class="sidebar-menu">
            <ul>
                <li><a href="#" class="nav-link active" data-page="dashboard"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="#" class="nav-link" data-page="accounts"><i class="fas fa-wallet"></i> <span>Accounts</span></a></li>
                <li><a href="#" class="nav-link" data-page="transactions"><i class="fas fa-exchange-alt"></i> <span>Transactions</span></a></li>
                <li><a href="#" class="nav-link" data-page="transfer"><i class="fas fa-money-bill-transfer"></i> <span>Transfer Money</span></a></li>
                <li><a href="#" class="nav-link" data-page="fd-rd"><i class="fas fa-piggy-bank"></i> <span>FD/RD Accounts</span></a></li>
                <li><a href="#" class="nav-link" data-page="savings"><i class="fas fa-chart-line"></i> <span>Savings Goals</span></a></li>
                <li><a href="#" class="nav-link" data-page="loans"><i class="fas fa-hand-holding-usd"></i> <span>Loans</span></a></li>
                <li><a href="#" class="nav-link" data-page="topup"><i class="fas fa-mobile-alt"></i> <span>Mobile Top-up</span></a></li>
                <li><a href="#" class="nav-link" data-page="settings"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                <li><a href="#" class="nav-link" data-page="logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1 id="page-title">Dashboard</h1>
            <div class="header-actions">
                <div class="notification-icon" id="notification-icon">
                    <i class="fas fa-bell"></i>
                    <div class="notification-badge"><?php echo count($customer_notifications); ?></div>
                </div>
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="https://salman.rfnhsc.com/salman.png" alt="<?php echo $customer_data['first_name'] . ' ' . $customer_data['last_name']; ?>">
                    </div>
                    <div>
                        <div style="font-weight: bold; color: white;"><?php echo $customer_data['first_name'] . ' ' . $customer_data['last_name']; ?></div>
                        <div style="font-size: 12px; color: rgba(255,255,255,0.7);"><?php echo $customer_data['city'] ?? 'Bangladesh'; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
        <?php endif; ?>

        <!-- Notification Panel -->
        <div class="notification-panel" id="notification-panel">
            <div class="notification-header">
                <h3>Notifications</h3>
                <span id="close-notifications" style="cursor: pointer;"><i class="fas fa-times"></i></span>
            </div>
            <div class="notification-list">
                <?php foreach ($customer_notifications as $notification): ?>
                <div class="notification-item <?php echo $notification['is_read'] ? '' : 'unread'; ?>">
                    <div class="notification-icon" style="background-color: 
                        <?php 
                        switch($notification['type']) {
                            case 'success': echo 'var(--success)'; break;
                            case 'warning': echo 'var(--warning)'; break;
                            case 'danger': echo 'var(--danger)'; break;
                            default: echo 'var(--primary)';
                        }
                        ?>;">
                        <i class="fas 
                            <?php 
                            switch($notification['type']) {
                                case 'success': echo 'fa-check-circle'; break;
                                case 'warning': echo 'fa-exclamation-triangle'; break;
                                case 'danger': echo 'fa-times-circle'; break;
                                default: echo 'fa-info-circle';
                            }
                            ?>"></i>
                    </div>
                    <div class="notification-content">
                        <h4><?php echo $notification['title']; ?></h4>
                        <p><?php echo $notification['message']; ?></p>
                        <div class="notification-time"><?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Dashboard Page -->
        <div class="page-content active" id="dashboard">
            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <div class="card-title">Total Balance</div>
                    <div class="card-value">BDT <?php echo number_format($total_balance, 2); ?></div>
                    <div class="card-change positive">+2.5% from last month</div>
                </div>
                <div class="dashboard-card">
                    <div class="card-title">Number of Accounts</div>
                    <div class="card-value"><?php echo count($customer_accounts); ?></div>
                    <div class="card-change positive">Active accounts</div>
                </div>
                <div class="dashboard-card">
                    <div class="card-title">Recent Transactions</div>
                    <div class="card-value"><?php echo count($recent_transactions); ?></div>
                    <div class="card-change positive">Last 30 days</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Your Accounts</h2>
                </div>
                <div class="account-cards">
                    <?php foreach ($customer_accounts as $account): ?>
                    <div class="account-card">
                        <div class="account-type"><?php echo $account['account_type']; ?></div>
                        <div class="account-balance">BDT <?php echo number_format($account['balance'], 2); ?></div>
                        <div class="account-number"><?php echo $account['account_number']; ?></div>
                        <div class="account-actions">
                            <button class="btn btn-outline view-details" data-account="<?php echo $account['id']; ?>">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                            <button class="btn btn-outline transfer" data-account="<?php echo $account['id']; ?>">
                                <i class="fas fa-exchange-alt"></i> Transfer
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Quick Actions</h2>
                </div>
                <div class="transfer-options">
                    <div class="transfer-option" id="quick-transfer">
                        <div class="transfer-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="transfer-title">Transfer Money</div>
                        <div class="transfer-description">Send money to other accounts</div>
                    </div>
                    <div class="transfer-option" id="quick-topup">
                        <div class="transfer-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="transfer-title">Mobile Top-up</div>
                        <div class="transfer-description">Recharge your mobile</div>
                    </div>
                    <div class="transfer-option" id="quick-loan">
                        <div class="transfer-icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div class="transfer-title">Apply for Loan</div>
                        <div class="transfer-description">Get instant loan approval</div>
                    </div>
                    <div class="transfer-option" id="quick-fd-rd">
                        <div class="transfer-icon">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                        <div class="transfer-title">Open FD/RD</div>
                        <div class="transfer-description">Create fixed or recurring deposit</div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Recent Transactions</h2>
                    <button class="btn btn-primary" id="view-all-transactions">
                        <i class="fas fa-list"></i> View All
                    </button>
                </div>
                <div class="transaction-list">
                    <?php foreach ($recent_transactions as $transaction): ?>
                    <div class="transaction-item">
                        <div class="transaction-info">
                            <div class="transaction-icon" style="background-color: 
                                <?php echo $transaction['type'] == 'deposit' ? 'var(--success)' : 'var(--danger)'; ?>;">
                                <i class="fas <?php echo $transaction['type'] == 'deposit' ? 'fa-arrow-down' : 'fa-arrow-up'; ?>"></i>
                            </div>
                            <div class="transaction-details">
                                <h3><?php echo $transaction['description']; ?></h3>
                                <p><?php echo date('M j, Y', strtotime($transaction['transaction_date'])); ?> • <?php echo $transaction['account_number']; ?></p>
                            </div>
                        </div>
                        <div class="transaction-amount <?php echo $transaction['type'] == 'deposit' ? 'income' : 'expense'; ?>">
                            <?php echo $transaction['type'] == 'deposit' ? '+' : '-'; ?>BDT <?php echo number_format($transaction['amount'], 2); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Accounts Page -->
        <div class="page-content" id="accounts">
            <div class="card">
                <div class="card-header">
                    <h2>Your Accounts</h2>
                </div>
                <div class="account-cards">
                    <?php foreach ($customer_accounts as $account): ?>
                    <div class="account-card">
                        <div class="account-type"><?php echo $account['account_type']; ?></div>
                        <div class="account-balance">BDT <?php echo number_format($account['balance'], 2); ?></div>
                        <div class="account-number"><?php echo $account['account_number']; ?></div>
                        <div class="account-actions">
                            <button class="btn btn-outline view-details" data-account="<?php echo $account['id']; ?>">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                            <button class="btn btn-outline transfer" data-account="<?php echo $account['id']; ?>">
                                <i class="fas fa-exchange-alt"></i> Transfer
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Transactions Page -->
        <div class="page-content" id="transactions">
            <div class="card">
                <div class="card-header">
                    <h2>Transaction History</h2>
                    <div class="transaction-filters">
                        <select class="filter-select" id="account-filter">
                            <option>All Accounts</option>
                            <?php foreach ($customer_accounts as $account): ?>
                            <option value="<?php echo $account['id']; ?>"><?php echo $account['account_type']; ?> (<?php echo $account['account_number']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <select class="filter-select" id="time-filter">
                            <option>Last 30 Days</option>
                            <option>Last 3 Months</option>
                            <option>Last 6 Months</option>
                            <option>Last Year</option>
                        </select>
                        <select class="filter-select" id="type-filter">
                            <option>All Transactions</option>
                            <option value="deposit">Income</option>
                            <option value="withdrawal">Expenses</option>
                            <option value="transfer">Transfers</option>
                        </select>
                    </div>
                </div>
                <div class="transaction-list" id="transactions-list">
                    <?php foreach ($recent_transactions as $transaction): ?>
                    <div class="transaction-item">
                        <div class="transaction-info">
                            <div class="transaction-icon" style="background-color: 
                                <?php echo $transaction['type'] == 'deposit' ? 'var(--success)' : 'var(--danger)'; ?>;">
                                <i class="fas <?php echo $transaction['type'] == 'deposit' ? 'fa-arrow-down' : 'fa-arrow-up'; ?>"></i>
                            </div>
                            <div class="transaction-details">
                                <h3><?php echo $transaction['description']; ?></h3>
                                <p><?php echo date('M j, Y', strtotime($transaction['transaction_date'])); ?> • <?php echo $transaction['account_number']; ?></p>
                            </div>
                        </div>
                        <div class="transaction-amount <?php echo $transaction['type'] == 'deposit' ? 'income' : 'expense'; ?>">
                            <?php echo $transaction['type'] == 'deposit' ? '+' : '-'; ?>BDT <?php echo number_format($transaction['amount'], 2); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Transfer Money Page -->
        <div class="page-content" id="transfer">
            <div class="card">
                <div class="card-header">
                    <h2>Transfer Money</h2>
                </div>
                <form id="transfer-form" method="POST">
                    <input type="hidden" name="action" value="transfer_money">
                    <div class="form-group">
                        <label for="from-account">From Account</label>
                        <select id="from-account" name="from_account" class="form-control" required>
                            <option value="">Select Account</option>
                            <?php foreach ($customer_accounts as $account): ?>
                            <option value="<?php echo $account['id']; ?>" data-balance="<?php echo $account['balance']; ?>">
                                <?php echo $account['account_type']; ?> (<?php echo $account['account_number']; ?>) - BDT <?php echo number_format($account['balance'], 2); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="to-account">To Account Number</label>
                        <input type="text" id="to-account" name="to_account" class="form-control" placeholder="Enter recipient account number" required>
                    </div>
                    <div class="form-group">
                        <label for="transfer-amount">Amount (BDT)</label>
                        <input type="number" id="transfer-amount" name="amount" class="form-control" placeholder="Enter amount" min="1" required>
                        <small id="balance-warning" style="color: var(--danger); display: none;">Insufficient balance</small>
                    </div>
                    <div class="form-group">
                        <label for="transfer-description">Description (Optional)</label>
                        <input type="text" id="transfer-description" name="description" class="form-control" placeholder="Add a note">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 18px;">
                        <i class="fas fa-paper-plane"></i> Confirm Transfer
                    </button>
                </form>
            </div>
        </div>

        <!-- FD/RD Accounts Page -->
        <div class="page-content" id="fd-rd">
            <div class="card">
                <div class="card-header">
                    <h2>Fixed Deposit & Recurring Deposit Accounts</h2>
                </div>
                <div class="account-creation-options">
                    <div class="account-creation-option" id="create-fd-option">
                        <div class="account-creation-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="account-creation-title">Fixed Deposit</div>
                        <div class="account-creation-description">
                            Lock your money for a fixed period and earn higher interest rates. 
                            Perfect for long-term savings with guaranteed returns.
                        </div>
                        <div class="account-creation-features">
                            <p><i class="fas fa-check text-success"></i> Higher interest rates</p>
                            <p><i class="fas fa-check text-success"></i> Fixed tenure</p>
                            <p><i class="fas fa-check text-success"></i> Lump sum deposit</p>
                        </div>
                        <button class="btn btn-primary" id="create-fd-btn">
                            <i class="fas fa-plus"></i> Create FD Account
                        </button>
                    </div>
                    
                    <div class="account-creation-option" id="create-rd-option">
                        <div class="account-creation-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="account-creation-title">Recurring Deposit</div>
                        <div class="account-creation-description">
                            Save regularly with fixed monthly deposits. Build your savings 
                            systematically while earning competitive interest rates.
                        </div>
                        <div class="account-creation-features">
                            <p><i class="fas fa-check text-success"></i> Monthly deposits</p>
                            <p><i class="fas fa-check text-success"></i> Fixed tenure</p>
                            <p><i class="fas fa-check text-success"></i> Systematic savings</p>
                        </div>
                        <button class="btn btn-primary" id="create-rd-btn">
                            <i class="fas fa-plus"></i> Create RD Account
                        </button>
                    </div>
                </div>
            </div>

            <!-- Existing FD/RD Accounts -->
            <div class="card">
                <div class="card-header">
                    <h2>Your FD/RD Accounts</h2>
                </div>
                <div class="account-cards">
                    <?php 
                    $fd_rd_accounts = array_filter($customer_accounts, function($account) {
                        return in_array($account['account_type'], ['Fixed Deposit', 'Recurring Deposit']);
                    });
                    
                    if (count($fd_rd_accounts) > 0): 
                        foreach ($fd_rd_accounts as $account): ?>
                        <div class="account-card">
                            <div class="account-type"><?php echo $account['account_type']; ?></div>
                            <div class="account-balance">BDT <?php echo number_format($account['balance'], 2); ?></div>
                            <div class="account-number"><?php echo $account['account_number']; ?></div>
                            <div class="account-details">
                                <p>Branch: <?php echo $account['branch_name']; ?></p>
                                <p>Opened: <?php echo date('M j, Y', strtotime($account['opened_date'])); ?></p>
                            </div>
                        </div>
                        <?php endforeach;
                    else: ?>
                    <div class="no-accounts" style="text-align: center; padding: 40px; color: var(--gray);">
                        <i class="fas fa-piggy-bank" style="font-size: 50px; margin-bottom: 20px;"></i>
                        <h3>No FD/RD Accounts Yet</h3>
                        <p>Create your first Fixed Deposit or Recurring Deposit account to start earning higher interest rates.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Savings Goals Page -->
        <div class="page-content" id="savings">
            <div class="card">
                <div class="card-header">
                    <h2>Savings Goals</h2>
                    <button class="btn btn-primary" id="create-goal">
                        <i class="fas fa-plus"></i> Create New Goal
                    </button>
                </div>
                <?php if (count($customer_savings_goals) > 0): ?>
                    <?php foreach ($customer_savings_goals as $goal): ?>
                    <div class="savings-goal">
                        <div class="goal-progress">
                            <h3><?php echo $goal['name']; ?></h3>
                            <div class="progress-bar">
                                <div class="progress" style="width: <?php echo ($goal['current_amount'] / $goal['target_amount']) * 100; ?>%;"></div>
                            </div>
                            <p>BDT <?php echo number_format($goal['current_amount'], 2); ?> saved of BDT <?php echo number_format($goal['target_amount'], 2); ?> goal</p>
                        </div>
                        <div class="goal-info">
                            <div class="goal-amount"><?php echo number_format(($goal['current_amount'] / $goal['target_amount']) * 100, 1); ?>%</div>
                            <p>Target: <?php echo date('M Y', strtotime($goal['target_date'])); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="no-goals" style="text-align: center; padding: 40px; color: var(--gray);">
                    <i class="fas fa-bullseye" style="font-size: 50px; margin-bottom: 20px;"></i>
                    <h3>No Savings Goals Yet</h3>
                    <p>Create your first savings goal to track your progress and achieve your financial targets.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Loans Page -->
        <div class="page-content" id="loans">
            <div class="card">
                <div class="card-header">
                    <h2>Your Loans</h2>
                    <button class="btn btn-primary" id="apply-loan">
                        <i class="fas fa-hand-holding-usd"></i> Apply for Loan
                    </button>
                </div>
                <div class="loan-cards">
                    <?php if (count($customer_loans) > 0): ?>
                        <?php foreach ($customer_loans as $loan): ?>
                        <div class="card loan-card">
                            <h3><?php echo ucfirst($loan['loan_type']); ?> Loan</h3>
                            <div class="loan-amount">BDT <?php echo number_format($loan['amount'], 2); ?></div>
                            <div class="loan-details">
                                <span>Remaining Balance</span>
                                <span>BDT <?php echo number_format($loan['remaining_balance'], 2); ?></span>
                            </div>
                            <div class="loan-details">
                                <span>Interest Rate</span>
                                <span><?php echo $loan['interest_rate']; ?>%</span>
                            </div>
                            <div class="loan-details">
                                <span>Status</span>
                                <span style="color: 
                                    <?php 
                                    switch($loan['status']) {
                                        case 'approved': echo 'var(--success)'; break;
                                        case 'pending': echo 'var(--warning)'; break;
                                        default: echo 'var(--gray)';
                                    }
                                    ?>;">
                                    <?php echo ucfirst($loan['status']); ?>
                                </span>
                            </div>
                            <?php if ($loan['status'] == 'active'): ?>
                            <button class="btn btn-primary" style="margin-top: 15px; width: 100%;">
                                <i class="fas fa-credit-card"></i> Make Payment
                            </button>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <div class="no-loans" style="text-align: center; padding: 40px; color: var(--gray);">
                        <i class="fas fa-hand-holding-usd" style="font-size: 50px; margin-bottom: 20px;"></i>
                        <h3>No Active Loans</h3>
                        <p>Apply for a loan to meet your financial needs with flexible repayment options.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Mobile Top-up Page -->
        <div class="page-content" id="topup">
            <div class="card">
                <div class="card-header">
                    <h2>Mobile Top-up</h2>
                </div>
                <form id="topup-form" method="POST">
                    <input type="hidden" name="action" value="mobile_topup">
                    <div class="form-group">
                        <label for="operator">Mobile Operator</label>
                        <select id="operator" name="operator" class="form-control" required>
                            <option value="">Select Operator</option>
                            <option value="Grameenphone">Grameenphone</option>
                            <option value="Banglalink">Banglalink</option>
                            <option value="Robi">Robi</option>
                            <option value="Airtel">Airtel</option>
                            <option value="Teletalk">Teletalk</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone-number">Phone Number</label>
                        <div class="phone-input">
                            <input type="tel" id="phone-number" name="phone_number" class="form-control" placeholder="Enter phone number" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="topup-account">Payment Account</label>
                        <select id="topup-account" name="account_id" class="form-control" required>
                            <option value="">Select Account</option>
                            <?php foreach ($customer_accounts as $account): ?>
                            <option value="<?php echo $account['id']; ?>" data-balance="<?php echo $account['balance']; ?>">
                                <?php echo $account['account_type']; ?> (<?php echo $account['account_number']; ?>) - BDT <?php echo number_format($account['balance'], 2); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="topup-amount">Amount (BDT)</label>
                        <input type="number" id="topup-amount" name="amount" class="form-control" placeholder="Enter amount" min="10" required>
                        <small id="topup-balance-warning" style="color: var(--danger); display: none;">Insufficient balance</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 18px;">
                        <i class="fas fa-mobile-alt"></i> Complete Top-up
                    </button>
                </form>
            </div>
        </div>

        <!-- Settings Page -->
        <div class="page-content" id="settings">
            <div class="settings-menu">
                <div class="settings-tab active" data-tab="profile">Profile</div>
                <div class="settings-tab" data-tab="security">Security</div>
                <div class="settings-tab" data-tab="preferences">Preferences</div>
            </div>
            
            <div class="settings-content active" id="profile-tab">
                <div class="card">
                    <div class="card-header">
                        <h2>Personal Information</h2>
                    </div>
                    <form id="profile-form" method="POST">
                        <input type="hidden" name="action" value="update_profile">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label for="full-name">First Name</label>
                                <input type="text" id="full-name" name="first_name" class="form-control" value="<?php echo $customer_data['first_name']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last-name">Last Name</label>
                                <input type="text" id="last-name" name="last_name" class="form-control" value="<?php echo $customer_data['last_name']; ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo $customer_data['email']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo $customer_data['phone']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" class="form-control" value="<?php echo $customer_data['address']; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="settings-content" id="security-tab">
                <div class="card">
                    <div class="card-header">
                        <h2>Security Settings</h2>
                    </div>
                    <form id="security-form" method="POST">
                        <input type="hidden" name="action" value="update_password">
                        <div class="form-group">
                            <label for="current-password">Current Password</label>
                            <input type="password" id="current-password" name="current_password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="new-password">New Password</label>
                            <input type="password" id="new-password" name="new_password" class="form-control" required minlength="6">
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">Confirm New Password</label>
                            <input type="password" id="confirm-password" class="form-control" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="settings-content" id="preferences-tab">
                <div class="card">
                    <div class="card-header">
                        <h2>Account Preferences</h2>
                    </div>
                    <div class="form-group">
                        <label for="language">Language</label>
                        <select id="language" class="form-control">
                            <option>English</option>
                            <option>Bengali</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="currency">Default Currency</label>
                        <select id="currency" class="form-control">
                            <option selected>BDT - Bangladeshi Taka</option>
                            <option>USD - US Dollar</option>
                            <option>EUR - Euro</option>
                        </select>
                    </div>
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Preferences
                    </button>
                </div>
            </div>
        </div>

        <!-- Logout Page -->
        <div class="page-content" id="logout">
            <div class="logout-container">
                <div class="logout-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <div class="logout-message">Are you sure you want to logout?</div>
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <button class="btn btn-primary" style="padding: 12px 30px; font-size: 18px;" id="confirm-logout">
                        <i class="fas fa-sign-out-alt"></i> Yes, Logout
                    </button>
                    <button class="btn" style="padding: 12px 30px; font-size: 18px; background: #e9ecef;" id="cancel-logout">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Goal Modal -->
    <div class="modal" id="create-goal-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Create Savings Goal</h3>
                <button class="close-modal">&times;</button>
            </div>
            <form id="create-goal-form" method="POST">
                <input type="hidden" name="action" value="create_savings_goal">
                <div class="form-group">
                    <label for="goal-name">Goal Name</label>
                    <input type="text" id="goal-name" name="name" class="form-control" placeholder="e.g., Vacation Fund" required>
                </div>
                <div class="form-group">
                    <label for="target-amount">Target Amount (BDT)</label>
                    <input type="number" id="target-amount" name="target_amount" class="form-control" placeholder="Enter target amount" min="1" required>
                </div>
                <div class="form-group">
                    <label for="target-date">Target Date</label>
                    <input type="date" id="target-date" name="target_date" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" id="cancel-goal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Goal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Apply Loan Modal -->
    <div class="modal" id="apply-loan-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Apply for Loan</h3>
                <button class="close-modal">&times;</button>
            </div>
            <form id="apply-loan-form" method="POST">
                <input type="hidden" name="action" value="apply_loan">
                <div class="form-group">
                    <label for="loan-type">Loan Type</label>
                    <select id="loan-type" name="loan_type" class="form-control" required>
                        <option value="">Select Loan Type</option>
                        <option value="personal">Personal Loan</option>
                        <option value="home">Home Loan</option>
                        <option value="car">Car Loan</option>
                        <option value="business">Business Loan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="loan-amount">Loan Amount (BDT)</label>
                    <input type="number" id="loan-amount" name="amount" class="form-control" placeholder="Enter loan amount" min="1" required>
                </div>
                <div class="form-group">
                    <label for="loan-purpose">Purpose</label>
                    <textarea id="loan-purpose" name="purpose" class="form-control" placeholder="Describe the purpose of the loan" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" id="cancel-loan">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Apply for Loan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create FD Account Modal -->
    <div class="modal" id="create-fd-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Create Fixed Deposit Account</h3>
                <button class="close-modal">&times;</button>
            </div>
            <form id="create-fd-form" method="POST">
                <input type="hidden" name="action" value="create_fd_account">
                <div class="form-group">
                    <label for="fd-account-type">Account Type</label>
                    <select id="fd-account-type" name="account_type_id" class="form-control" required>
                        <option value="">Select Account Type</option>
                        <?php foreach ($account_types as $type): ?>
                            <?php if ($type['name'] == 'Fixed Deposit'): ?>
                            <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?> - <?php echo $type['interest_rate']; ?>% Interest</option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fd-branch">Branch</label>
                    <select id="fd-branch" name="branch_id" class="form-control" required>
                        <option value="">Select Branch</option>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo $branch['id']; ?>"><?php echo $branch['name']; ?> - <?php echo $branch['city']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fd-initial-deposit">Initial Deposit (BDT)</label>
                    <input type="number" id="fd-initial-deposit" name="initial_deposit" class="form-control" placeholder="Enter deposit amount" min="1000" required>
                </div>
                <div class="form-group">
                    <label for="fd-term-months">Term (Months)</label>
                    <select id="fd-term-months" name="term_months" class="form-control" required>
                        <option value="6">6 Months</option>
                        <option value="12">12 Months</option>
                        <option value="24">24 Months</option>
                        <option value="36">36 Months</option>
                        <option value="60">60 Months</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" id="cancel-fd">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create FD Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create RD Account Modal -->
    <div class="modal" id="create-rd-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Create Recurring Deposit Account</h3>
                <button class="close-modal">&times;</button>
            </div>
            <form id="create-rd-form" method="POST">
                <input type="hidden" name="action" value="create_rd_account">
                <div class="form-group">
                    <label for="rd-account-type">Account Type</label>
                    <select id="rd-account-type" name="account_type_id" class="form-control" required>
                        <option value="">Select Account Type</option>
                        <?php foreach ($account_types as $type): ?>
                            <?php if ($type['name'] == 'Recurring Deposit'): ?>
                            <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?> - <?php echo $type['interest_rate']; ?>% Interest</option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="rd-branch">Branch</label>
                    <select id="rd-branch" name="branch_id" class="form-control" required>
                        <option value="">Select Branch</option>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo $branch['id']; ?>"><?php echo $branch['name']; ?> - <?php echo $branch['city']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="rd-monthly-deposit">Monthly Deposit (BDT)</label>
                    <input type="number" id="rd-monthly-deposit" name="monthly_deposit" class="form-control" placeholder="Enter monthly deposit amount" min="100" required>
                </div>
                <div class="form-group">
                    <label for="rd-term-months">Term (Months)</label>
                    <select id="rd-term-months" name="term_months" class="form-control" required>
                        <option value="6">6 Months</option>
                        <option value="12">12 Months</option>
                        <option value="24">24 Months</option>
                        <option value="36">36 Months</option>
                        <option value="60">60 Months</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" id="cancel-rd">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create RD Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.getElementById('sidebar');
            
            mobileMenuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });

            // Navigation between pages
            const navLinks = document.querySelectorAll('.nav-link');
            const pageContents = document.querySelectorAll('.page-content');
            const pageTitle = document.getElementById('page-title');
            
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all links and pages
                    navLinks.forEach(l => l.classList.remove('active'));
                    pageContents.forEach(p => p.classList.remove('active'));
                    
                    // Add active class to clicked link
                    this.classList.add('active');
                    
                    // Show corresponding page
                    const pageId = this.getAttribute('data-page');
                    document.getElementById(pageId).classList.add('active');
                    
                    // Update page title
                    pageTitle.textContent = this.querySelector('span').textContent;
                    
                    // Close mobile menu
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('active');
                    }
                });
            });
            
            // Notification panel
            const notificationIcon = document.getElementById('notification-icon');
            const notificationPanel = document.getElementById('notification-panel');
            const closeNotifications = document.getElementById('close-notifications');
            
            notificationIcon.addEventListener('click', function() {
                notificationPanel.style.display = notificationPanel.style.display === 'block' ? 'none' : 'block';
            });
            
            closeNotifications.addEventListener('click', function() {
                notificationPanel.style.display = 'none';
            });
            
            // Close notification panel when clicking outside
            document.addEventListener('click', function(e) {
                if (!notificationIcon.contains(e.target) && !notificationPanel.contains(e.target)) {
                    notificationPanel.style.display = 'none';
                }
            });
            
            // Settings tabs
            const settingsTabs = document.querySelectorAll('.settings-tab');
            const settingsContents = document.querySelectorAll('.settings-content');
            
            settingsTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Remove active class from all tabs and contents
                    settingsTabs.forEach(t => t.classList.remove('active'));
                    settingsContents.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    this.classList.add('active');
                    document.getElementById(`${tabId}-tab`).classList.add('active');
                });
            });
            
            // Logout functionality
            document.getElementById('confirm-logout').addEventListener('click', function() {
                window.location.href = 'logout.php';
            });
            
            document.getElementById('cancel-logout').addEventListener('click', function() {
                // Go back to dashboard
                navLinks.forEach(l => l.classList.remove('active'));
                pageContents.forEach(p => p.classList.remove('active'));
                document.querySelector('.nav-link[data-page="dashboard"]').classList.add('active');
                document.getElementById('dashboard').classList.add('active');
                pageTitle.textContent = 'Dashboard';
            });
            
            // Quick actions
            document.getElementById('quick-transfer').addEventListener('click', function() {
                navLinks.forEach(l => l.classList.remove('active'));
                pageContents.forEach(p => p.classList.remove('active'));
                document.querySelector('.nav-link[data-page="transfer"]').classList.add('active');
                document.getElementById('transfer').classList.add('active');
                pageTitle.textContent = 'Transfer Money';
            });
            
            document.getElementById('quick-topup').addEventListener('click', function() {
                navLinks.forEach(l => l.classList.remove('active'));
                pageContents.forEach(p => p.classList.remove('active'));
                document.querySelector('.nav-link[data-page="topup"]').classList.add('active');
                document.getElementById('topup').classList.add('active');
                pageTitle.textContent = 'Mobile Top-up';
            });
            
            document.getElementById('quick-loan').addEventListener('click', function() {
                document.getElementById('apply-loan-modal').style.display = 'flex';
            });
            
            document.getElementById('quick-fd-rd').addEventListener('click', function() {
                navLinks.forEach(l => l.classList.remove('active'));
                pageContents.forEach(p => p.classList.remove('active'));
                document.querySelector('.nav-link[data-page="fd-rd"]').classList.add('active');
                document.getElementById('fd-rd').classList.add('active');
                pageTitle.textContent = 'FD/RD Accounts';
            });
            
            // Modal functionality
            const modals = document.querySelectorAll('.modal');
            const closeModalButtons = document.querySelectorAll('.close-modal');
            
            // Create goal modal
            document.getElementById('create-goal').addEventListener('click', function() {
                document.getElementById('create-goal-modal').style.display = 'flex';
            });
            
            document.getElementById('cancel-goal').addEventListener('click', function() {
                document.getElementById('create-goal-modal').style.display = 'none';
            });
            
            // Apply loan modal
            document.getElementById('apply-loan').addEventListener('click', function() {
                document.getElementById('apply-loan-modal').style.display = 'flex';
            });
            
            document.getElementById('cancel-loan').addEventListener('click', function() {
                document.getElementById('apply-loan-modal').style.display = 'none';
            });
            
            // Create FD modal
            document.getElementById('create-fd-btn').addEventListener('click', function() {
                document.getElementById('create-fd-modal').style.display = 'flex';
            });
            
            document.getElementById('cancel-fd').addEventListener('click', function() {
                document.getElementById('create-fd-modal').style.display = 'none';
            });
            
            // Create RD modal
            document.getElementById('create-rd-btn').addEventListener('click', function() {
                document.getElementById('create-rd-modal').style.display = 'flex';
            });
            
            document.getElementById('cancel-rd').addEventListener('click', function() {
                document.getElementById('create-rd-modal').style.display = 'none';
            });
            
            // Close modals
            closeModalButtons.forEach(button => {
                button.addEventListener('click', function() {
                    modals.forEach(modal => modal.style.display = 'none');
                });
            });
            
            // Close modals when clicking outside
            modals.forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            });
            
            // Password confirmation validation
            const securityForm = document.getElementById('security-form');
            const confirmPassword = document.getElementById('confirm-password');
            const newPassword = document.getElementById('new-password');
            
            securityForm.addEventListener('submit', function(e) {
                if (newPassword.value !== confirmPassword.value) {
                    e.preventDefault();
                    alert('New password and confirmation password do not match!');
                }
            });
            
            // View all transactions
            document.getElementById('view-all-transactions').addEventListener('click', function() {
                navLinks.forEach(l => l.classList.remove('active'));
                pageContents.forEach(p => p.classList.remove('active'));
                document.querySelector('.nav-link[data-page="transactions"]').classList.add('active');
                document.getElementById('transactions').classList.add('active');
                pageTitle.textContent = 'Transactions';
            });
            
            // Transfer buttons
            document.querySelectorAll('.transfer').forEach(button => {
                button.addEventListener('click', function() {
                    const accountId = this.getAttribute('data-account');
                    navLinks.forEach(l => l.classList.remove('active'));
                    pageContents.forEach(p => p.classList.remove('active'));
                    document.querySelector('.nav-link[data-page="transfer"]').classList.add('active');
                    document.getElementById('transfer').classList.add('active');
                    pageTitle.textContent = 'Transfer Money';
                    
                    // Pre-select the account
                    document.getElementById('from-account').value = accountId;
                    checkTransferBalance();
                });
            });
            
            // Balance validation for transfers
            const fromAccount = document.getElementById('from-account');
            const transferAmount = document.getElementById('transfer-amount');
            const balanceWarning = document.getElementById('balance-warning');
            
            function checkTransferBalance() {
                const selectedOption = fromAccount.options[fromAccount.selectedIndex];
                const accountBalance = selectedOption ? parseFloat(selectedOption.getAttribute('data-balance')) : 0;
                const amount = parseFloat(transferAmount.value) || 0;
                
                if (amount > accountBalance) {
                    balanceWarning.style.display = 'block';
                    transferAmount.style.borderColor = 'var(--danger)';
                } else {
                    balanceWarning.style.display = 'none';
                    transferAmount.style.borderColor = '#ddd';
                }
            }
            
            fromAccount.addEventListener('change', checkTransferBalance);
            transferAmount.addEventListener('input', checkTransferBalance);
            
            // Balance validation for top-up
            const topupAccount = document.getElementById('topup-account');
            const topupAmount = document.getElementById('topup-amount');
            const topupBalanceWarning = document.getElementById('topup-balance-warning');
            
            function checkTopupBalance() {
                const selectedOption = topupAccount.options[topupAccount.selectedIndex];
                const accountBalance = selectedOption ? parseFloat(selectedOption.getAttribute('data-balance')) : 0;
                const amount = parseFloat(topupAmount.value) || 0;
                
                if (amount > accountBalance) {
                    topupBalanceWarning.style.display = 'block';
                    topupAmount.style.borderColor = 'var(--danger)';
                } else {
                    topupBalanceWarning.style.display = 'none';
                    topupAmount.style.borderColor = '#ddd';
                }
            }
            
            topupAccount.addEventListener('change', checkTopupBalance);
            topupAmount.addEventListener('input', checkTopupBalance);
            
            // Form submission loading states
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitButton = this.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.innerHTML = '<span class="spinner"></span> Processing...';
                        submitButton.disabled = true;
                        this.classList.add('loading');
                    }
                });
            });
            
            // Prevent form resubmission on page refresh
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
            
            // Responsive adjustments
            function handleResize() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('active');
                }
            }
            
            window.addEventListener('resize', handleResize);
            
            // Initialize balance checks
            checkTransferBalance();
            checkTopupBalance();
        });
    </script>
</body>
</html>