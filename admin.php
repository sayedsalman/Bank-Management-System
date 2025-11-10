<?php
// admin.php
require_once 'config.php';

// Check if user is logged in and is admin
redirectIfNotLoggedIn();
if (getUserRole() != 'admin') {
    header("Location: login.php");
    exit();
}

// Get current user info
$current_user_id = $_SESSION['user_id'];
$current_user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$current_user_stmt->execute([$current_user_id]);
$current_user = $current_user_stmt->fetch();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'get_dashboard_stats':
            echo json_encode(getDashboardStats($pdo));
            break;
        case 'get_customers':
            echo json_encode(getCustomers($pdo));
            break;
        case 'add_customer':
            echo json_encode(addCustomer($pdo, $_POST));
            break;
        case 'update_customer':
            echo json_encode(updateCustomer($pdo, $_POST));
            break;
        case 'delete_customer':
            echo json_encode(deleteCustomer($pdo, $_POST));
            break;
        case 'get_accounts':
            echo json_encode(getAccounts($pdo));
            break;
        case 'create_account':
            echo json_encode(createAccount($pdo, $_POST));
            break;
        case 'get_transactions':
            echo json_encode(getTransactions($pdo, $_POST));
            break;
        case 'add_transaction':
            echo json_encode(addTransaction($pdo, $_POST));
            break;
        case 'get_loans':
            echo json_encode(getLoans($pdo));
            break;
        case 'add_loan':
            echo json_encode(addLoan($pdo, $_POST));
            break;
        case 'approve_loan':
            echo json_encode(approveLoan($pdo, $_POST));
            break;
        case 'reject_loan':
            echo json_encode(rejectLoan($pdo, $_POST));
            break;
        case 'get_employees':
            echo json_encode(getEmployees($pdo));
            break;
        case 'add_employee':
            echo json_encode(addEmployee($pdo, $_POST));
            break;
        case 'update_employee':
            echo json_encode(updateEmployee($pdo, $_POST));
            break;
        case 'delete_employee':
            echo json_encode(deleteEmployee($pdo, $_POST));
            break;
        case 'update_account_status':
            echo json_encode(updateAccountStatus($pdo, $_POST));
            break;
        case 'update_customer_status':
            echo json_encode(updateCustomerStatus($pdo, $_POST));
            break;
        case 'get_branches':
            echo json_encode(getBranches($pdo));
            break;
        case 'add_branch':
            echo json_encode(addBranch($pdo, $_POST));
            break;
        case 'update_branch':
            echo json_encode(updateBranch($pdo, $_POST));
            break;
        case 'delete_branch':
            echo json_encode(deleteBranch($pdo, $_POST));
            break;
        case 'get_system_logs':
            echo json_encode(getSystemLogs($pdo, $_POST));
            break;
        case 'get_pending_registrations':
            echo json_encode(getPendingRegistrations($pdo));
            break;
        case 'approve_registration':
            echo json_encode(approveRegistration($pdo, $_POST));
            break;
        case 'reject_registration':
            echo json_encode(rejectRegistration($pdo, $_POST));
            break;
        case 'get_system_settings':
            echo json_encode(getSystemSettings($pdo, $_POST['category'] ?? null));
            break;
        case 'update_system_settings':
            echo json_encode(updateSystemSettings($pdo, $_POST));
            break;
        case 'reset_system_settings':
            echo json_encode(resetSystemSettings($pdo, $_POST['category'] ?? null));
            break;
        case 'get_notifications':
            echo json_encode(getNotifications($pdo));
            break;
        case 'mark_notification_read':
            echo json_encode(markNotificationRead($pdo, $_POST));
            break;
        case 'export_data':
            echo json_encode(exportData($pdo, $_POST));
            break;
        case 'get_customer_details':
            echo json_encode(getCustomerDetails($pdo, $_POST['customer_id']));
            break;
        case 'get_account_details':
            echo json_encode(getAccountDetails($pdo, $_POST['account_id']));
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit();
}

// Database functions
function getDashboardStats($pdo) {
    $stats = [];
    
    // Total Customers
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND status = 'active'");
    $stats['total_customers'] = $stmt->fetch()['count'];
    
    // Total Accounts
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM accounts WHERE status = 'active'");
    $stats['total_accounts'] = $stmt->fetch()['count'];
    
    // Total Balance
    $stmt = $pdo->query("SELECT SUM(balance) as total FROM accounts WHERE status = 'active'");
    $stats['total_balance'] = $stmt->fetch()['total'] ?? 0;
    
    // Pending Loans
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM loans WHERE status = 'pending'");
    $stats['pending_loans'] = $stmt->fetch()['count'];
    
    // Pending Registrations
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM s_pending_registrations WHERE status = 'pending'");
    $stats['pending_registrations'] = $stmt->fetch()['count'];
    
    // Total Employees
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role IN ('admin', 'employee') AND status = 'active'");
    $stats['total_employees'] = $stmt->fetch()['count'];
    
    // Recent Transactions
    $stmt = $pdo->query("SELECT t.*, u.first_name, u.last_name, a.account_number 
                         FROM transactions t 
                         JOIN accounts a ON t.account_id = a.id 
                         JOIN users u ON a.user_id = u.id 
                         ORDER BY t.created_at DESC LIMIT 5");
    $stats['recent_transactions'] = $stmt->fetchAll();
    
    return $stats;
}

function getCustomers($pdo) {
    $stmt = $pdo->query("SELECT u.*, COUNT(a.id) as account_count 
                         FROM users u 
                         LEFT JOIN accounts a ON u.id = a.user_id 
                         WHERE u.role = 'customer' 
                         GROUP BY u.id");
    return $stmt->fetchAll();
}

function addCustomer($pdo, $data) {
    try {
        $pdo->beginTransaction();
        
        // Generate unique username
        $base_username = strtolower($data['first_name'] . $data['last_name']);
        $username = $base_username;
        $counter = 1;
        
        while (true) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if (!$stmt->fetch()) break;
            $username = $base_username . $counter;
            $counter++;
        }
        
        // Hash password
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role, first_name, last_name, phone, address, city, nid_number, date_of_birth, status) 
                              VALUES (?, ?, ?, 'customer', ?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$username, $hashed_password, $data['email'], $data['first_name'], $data['last_name'], $data['phone'], $data['address'], $data['city'], $data['nid_number'], $data['date_of_birth']]);
        
        $user_id = $pdo->lastInsertId();
        
        // Create account
        $account_number = 'LB' . date('Y') . str_pad($user_id, 8, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("INSERT INTO accounts (account_number, user_id, account_type_id, branch_id, balance, opened_date) 
                              VALUES (?, ?, ?, ?, ?, CURDATE())");
        $stmt->execute([$account_number, $user_id, $data['account_type_id'], $data['branch_id'], $data['initial_deposit']]);
        
        $account_id = $pdo->lastInsertId();
        
        // Create initial transaction
        $transaction_id = 'TXN' . date('YmdHis');
        $stmt = $pdo->prepare("INSERT INTO transactions (transaction_id, account_id, type, amount, description, status) 
                              VALUES (?, ?, 'deposit', ?, 'Initial deposit - Account opening', 'completed')");
        $stmt->execute([$transaction_id, $account_id, $data['initial_deposit']]);
        
        $pdo->commit();
        
        // Log the action
        logAction("CUSTOMER_CREATED", "Created customer: " . $data['first_name'] . " " . $data['last_name']);
        
        return ['success' => true, 'message' => 'Customer added successfully'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error adding customer: ' . $e->getMessage()];
    }
}

function updateCustomer($pdo, $data) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, city = ?, nid_number = ?, date_of_birth = ?, status = ? WHERE id = ?");
        $stmt->execute([$data['first_name'], $data['last_name'], $data['email'], $data['phone'], $data['address'], $data['city'], $data['nid_number'], $data['date_of_birth'], $data['status'], $data['customer_id']]);
        
        logAction("CUSTOMER_UPDATED", "Updated customer ID: " . $data['customer_id']);
        
        return ['success' => true, 'message' => 'Customer updated successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error updating customer: ' . $e->getMessage()];
    }
}

function deleteCustomer($pdo, $data) {
    try {
        // Check if customer has accounts
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM accounts WHERE user_id = ?");
        $stmt->execute([$data['customer_id']]);
        $account_count = $stmt->fetch()['count'];
        
        if ($account_count > 0) {
            return ['success' => false, 'message' => 'Cannot delete customer with active accounts'];
        }
        
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'customer'");
        $stmt->execute([$data['customer_id']]);
        
        logAction("CUSTOMER_DELETED", "Deleted customer ID: " . $data['customer_id']);
        
        return ['success' => true, 'message' => 'Customer deleted successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error deleting customer: ' . $e->getMessage()];
    }
}

function getCustomerDetails($pdo, $customer_id) {
    $stmt = $pdo->prepare("SELECT u.*, 
                          (SELECT COUNT(*) FROM accounts WHERE user_id = u.id) as account_count,
                          (SELECT SUM(balance) FROM accounts WHERE user_id = u.id) as total_balance
                          FROM users u WHERE u.id = ? AND u.role = 'customer'");
    $stmt->execute([$customer_id]);
    return $stmt->fetch();
}

function getAccounts($pdo) {
    $stmt = $pdo->query("SELECT a.*, u.first_name, u.last_name, at.name as account_type, b.name as branch_name 
                         FROM accounts a 
                         JOIN users u ON a.user_id = u.id 
                         JOIN account_types at ON a.account_type_id = at.id 
                         JOIN branches b ON a.branch_id = b.id");
    return $stmt->fetchAll();
}

function createAccount($pdo, $data) {
    try {
        $account_number = 'LB' . date('Y') . str_pad($data['user_id'], 8, '0', STR_PAD_LEFT) . rand(100, 999);
        
        $stmt = $pdo->prepare("INSERT INTO accounts (account_number, user_id, account_type_id, branch_id, balance, opened_date) 
                              VALUES (?, ?, ?, ?, ?, CURDATE())");
        $stmt->execute([$account_number, $data['user_id'], $data['account_type_id'], $data['branch_id'], $data['initial_deposit']]);
        
        $account_id = $pdo->lastInsertId();
        
        // Create initial transaction
        $transaction_id = 'TXN' . date('YmdHis');
        $stmt = $pdo->prepare("INSERT INTO transactions (transaction_id, account_id, type, amount, description, status) 
                              VALUES (?, ?, 'deposit', ?, 'Initial deposit - Account opening', 'completed')");
        $stmt->execute([$transaction_id, $account_id, $data['initial_deposit']]);
        
        logAction("ACCOUNT_CREATED", "Created account: " . $account_number);
        
        return ['success' => true, 'message' => 'Account created successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error creating account: ' . $e->getMessage()];
    }
}

function getAccountDetails($pdo, $account_id) {
    $stmt = $pdo->prepare("SELECT a.*, u.first_name, u.last_name, u.email, at.name as account_type, b.name as branch_name 
                          FROM accounts a 
                          JOIN users u ON a.user_id = u.id 
                          JOIN account_types at ON a.account_type_id = at.id 
                          JOIN branches b ON a.branch_id = b.id 
                          WHERE a.id = ?");
    $stmt->execute([$account_id]);
    return $stmt->fetch();
}

function getTransactions($pdo, $filters = []) {
    $sql = "SELECT t.*, u.first_name, u.last_name, a.account_number 
            FROM transactions t 
            JOIN accounts a ON t.account_id = a.id 
            JOIN users u ON a.user_id = u.id 
            WHERE 1=1";
    
    $params = [];
    
    if (!empty($filters['type']) && $filters['type'] != 'All Types') {
        $sql .= " AND t.type = ?";
        $params[] = strtolower($filters['type']);
    }
    
    if (!empty($filters['date_from'])) {
        $sql .= " AND DATE(t.created_at) >= ?";
        $params[] = $filters['date_from'];
    }
    
    if (!empty($filters['date_to'])) {
        $sql .= " AND DATE(t.created_at) <= ?";
        $params[] = $filters['date_to'];
    }
    
    $sql .= " ORDER BY t.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function addTransaction($pdo, $data) {
    try {
        $pdo->beginTransaction();
        
        // Get account details
        $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
        $stmt->execute([$data['account_id']]);
        $account = $stmt->fetch();
        
        if (!$account) {
            throw new Exception("Account not found");
        }
        
        $transaction_id = 'TXN' . date('YmdHis');
        $amount = $data['amount'];
        
        // Update account balance based on transaction type
        if ($data['type'] == 'deposit') {
            $new_balance = $account['balance'] + $amount;
        } else if ($data['type'] == 'withdrawal') {
            if ($account['balance'] < $amount) {
                throw new Exception("Insufficient balance");
            }
            $new_balance = $account['balance'] - $amount;
        } else {
            $new_balance = $account['balance'];
        }
        
        // Update account balance
        $stmt = $pdo->prepare("UPDATE accounts SET balance = ? WHERE id = ?");
        $stmt->execute([$new_balance, $data['account_id']]);
        
        // Create transaction
        $stmt = $pdo->prepare("INSERT INTO transactions (transaction_id, account_id, type, amount, description, status) 
                              VALUES (?, ?, ?, ?, ?, 'completed')");
        $stmt->execute([$transaction_id, $data['account_id'], $data['type'], $amount, $data['description']]);
        
        $pdo->commit();
        
        logAction("TRANSACTION_CREATED", "Created " . $data['type'] . " transaction: " . $amount);
        
        return ['success' => true, 'message' => 'Transaction completed successfully'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error processing transaction: ' . $e->getMessage()];
    }
}

function getLoans($pdo) {
    $stmt = $pdo->query("SELECT l.*, u.first_name, u.last_name 
                         FROM loans l 
                         JOIN users u ON l.user_id = u.id 
                         ORDER BY l.applied_date DESC");
    return $stmt->fetchAll();
}

function addLoan($pdo, $data) {
    try {
        $loan_number = 'LN' . date('Y') . str_pad($data['user_id'], 8, '0', STR_PAD_LEFT) . rand(100, 999);
        
        // Calculate monthly payment
        $monthly_rate = $data['interest_rate'] / 100 / 12;
        $monthly_payment = $data['amount'] * $monthly_rate * pow(1 + $monthly_rate, $data['term_months']) / (pow(1 + $monthly_rate, $data['term_months']) - 1);
        
        $stmt = $pdo->prepare("INSERT INTO loans (loan_number, user_id, loan_type, amount, interest_rate, term_months, remaining_balance, monthly_payment, status, applied_date) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', CURDATE())");
        $stmt->execute([$loan_number, $data['user_id'], $data['loan_type'], $data['amount'], $data['interest_rate'], $data['term_months'], $data['amount'], $monthly_payment]);
        
        logAction("LOAN_CREATED", "Created loan application: " . $loan_number);
        
        return ['success' => true, 'message' => 'Loan application submitted successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error creating loan: ' . $e->getMessage()];
    }
}

function approveLoan($pdo, $data) {
    try {
        $stmt = $pdo->prepare("UPDATE loans SET status = 'approved', approved_date = CURDATE(), next_payment_date = DATE_ADD(CURDATE(), INTERVAL 1 MONTH) WHERE id = ?");
        $stmt->execute([$data['loan_id']]);
        
        logAction("LOAN_APPROVED", "Approved loan ID: " . $data['loan_id']);
        
        return ['success' => true, 'message' => 'Loan approved successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error approving loan: ' . $e->getMessage()];
    }
}

function rejectLoan($pdo, $data) {
    try {
        $stmt = $pdo->prepare("UPDATE loans SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$data['loan_id']]);
        
        logAction("LOAN_REJECTED", "Rejected loan ID: " . $data['loan_id']);
        
        return ['success' => true, 'message' => 'Loan rejected successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error rejecting loan: ' . $e->getMessage()];
    }
}

function getEmployees($pdo) {
    $stmt = $pdo->query("SELECT * FROM users WHERE role IN ('admin', 'employee') ORDER BY first_name, last_name");
    return $stmt->fetchAll();
}

function addEmployee($pdo, $data) {
    try {
        // Generate unique username
        $base_username = strtolower($data['first_name'] . $data['last_name']);
        $username = $base_username;
        $counter = 1;
        
        while (true) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if (!$stmt->fetch()) break;
            $username = $base_username . $counter;
            $counter++;
        }
        
        // Hash password
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role, first_name, last_name, phone, address, city, nid_number, date_of_birth, status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$username, $hashed_password, $data['email'], $data['role'], $data['first_name'], $data['last_name'], $data['phone'], $data['address'], $data['city'], $data['nid_number'], $data['date_of_birth']]);
        
        logAction("EMPLOYEE_CREATED", "Created employee: " . $data['first_name'] . " " . $data['last_name']);
        
        return ['success' => true, 'message' => 'Employee added successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error adding employee: ' . $e->getMessage()];
    }
}

function updateEmployee($pdo, $data) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, city = ?, nid_number = ?, date_of_birth = ?, role = ?, status = ? WHERE id = ?");
        $stmt->execute([$data['first_name'], $data['last_name'], $data['email'], $data['phone'], $data['address'], $data['city'], $data['nid_number'], $data['date_of_birth'], $data['role'], $data['status'], $data['employee_id']]);
        
        logAction("EMPLOYEE_UPDATED", "Updated employee ID: " . $data['employee_id']);
        
        return ['success' => true, 'message' => 'Employee updated successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error updating employee: ' . $e->getMessage()];
    }
}

function deleteEmployee($pdo, $data) {
    try {
        // Cannot delete yourself
        if ($data['employee_id'] == $_SESSION['user_id']) {
            return ['success' => false, 'message' => 'You cannot delete your own account'];
        }
        
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role IN ('admin', 'employee')");
        $stmt->execute([$data['employee_id']]);
        
        logAction("EMPLOYEE_DELETED", "Deleted employee ID: " . $data['employee_id']);
        
        return ['success' => true, 'message' => 'Employee deleted successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error deleting employee: ' . $e->getMessage()];
    }
}

function updateAccountStatus($pdo, $data) {
    try {
        $stmt = $pdo->prepare("UPDATE accounts SET status = ? WHERE id = ?");
        $stmt->execute([$data['status'], $data['account_id']]);
        
        logAction("ACCOUNT_STATUS_UPDATED", "Updated account ID " . $data['account_id'] . " to " . $data['status']);
        
        return ['success' => true, 'message' => 'Account status updated successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error updating account status: ' . $e->getMessage()];
    }
}

function updateCustomerStatus($pdo, $data) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$data['status'], $data['customer_id']]);
        
        logAction("CUSTOMER_STATUS_UPDATED", "Updated customer ID " . $data['customer_id'] . " to " . $data['status']);
        
        return ['success' => true, 'message' => 'Customer status updated successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error updating customer status: ' . $e->getMessage()];
    }
}

function getBranches($pdo) {
    $stmt = $pdo->query("SELECT b.*, 
                         (SELECT COUNT(*) FROM accounts WHERE branch_id = b.id) as account_count,
                         (SELECT COUNT(*) FROM users WHERE role IN ('admin', 'employee') AND status = 'active') as employee_count
                         FROM branches b");
    return $stmt->fetchAll();
}

function addBranch($pdo, $data) {
    try {
        $stmt = $pdo->prepare("INSERT INTO branches (name, address, phone, email, manager_name, opening_hours, services, facilities) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['address'], $data['phone'], $data['email'], $data['manager_name'], $data['opening_hours'], $data['services'], $data['facilities']]);
        
        logAction("BRANCH_CREATED", "Created branch: " . $data['name']);
        
        return ['success' => true, 'message' => 'Branch added successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error adding branch: ' . $e->getMessage()];
    }
}

function updateBranch($pdo, $data) {
    try {
        $stmt = $pdo->prepare("UPDATE branches SET name = ?, address = ?, phone = ?, email = ?, manager_name = ?, opening_hours = ?, services = ?, facilities = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['address'], $data['phone'], $data['email'], $data['manager_name'], $data['opening_hours'], $data['services'], $data['facilities'], $data['branch_id']]);
        
        logAction("BRANCH_UPDATED", "Updated branch ID: " . $data['branch_id']);
        
        return ['success' => true, 'message' => 'Branch updated successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error updating branch: ' . $e->getMessage()];
    }
}

function deleteBranch($pdo, $data) {
    try {
        // Check if branch has accounts
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM accounts WHERE branch_id = ?");
        $stmt->execute([$data['branch_id']]);
        $account_count = $stmt->fetch()['count'];
        
        if ($account_count > 0) {
            return ['success' => false, 'message' => 'Cannot delete branch with associated accounts'];
        }
        
        $stmt = $pdo->prepare("DELETE FROM branches WHERE id = ?");
        $stmt->execute([$data['branch_id']]);
        
        logAction("BRANCH_DELETED", "Deleted branch ID: " . $data['branch_id']);
        
        return ['success' => true, 'message' => 'Branch deleted successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error deleting branch: ' . $e->getMessage()];
    }
}

function getSystemLogs($pdo, $filters = []) {
    $sql = "SELECT sl.*, u.first_name, u.last_name 
            FROM s_system_logs sl 
            LEFT JOIN users u ON sl.user_id = u.id 
            WHERE 1=1";
    
    $params = [];
    
    if (!empty($filters['date_from'])) {
        $sql .= " AND DATE(sl.created_at) >= ?";
        $params[] = $filters['date_from'];
    }
    
    if (!empty($filters['date_to'])) {
        $sql .= " AND DATE(sl.created_at) <= ?";
        $params[] = $filters['date_to'];
    }
    
    if (!empty($filters['user_id'])) {
        $sql .= " AND sl.user_id = ?";
        $params[] = $filters['user_id'];
    }
    
    $sql .= " ORDER BY sl.created_at DESC LIMIT 100";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getPendingRegistrations($pdo) {
    $stmt = $pdo->query("SELECT pr.*, b.name as branch_name, at.name as account_type_name 
                         FROM s_pending_registrations pr 
                         JOIN branches b ON pr.branch_id = b.id 
                         JOIN account_types at ON pr.account_type_id = at.id 
                         WHERE pr.status = 'pending'");
    return $stmt->fetchAll();
}

function approveRegistration($pdo, $data) {
    try {
        $pdo->beginTransaction();
        
        // Get registration data
        $stmt = $pdo->prepare("SELECT * FROM s_pending_registrations WHERE id = ?");
        $stmt->execute([$data['registration_id']]);
        $registration = $stmt->fetch();
        
        if (!$registration) {
            throw new Exception("Registration not found");
        }
        
        // Create user
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role, first_name, last_name, phone, address, city, nid_number, date_of_birth, status) 
                              VALUES (?, ?, ?, 'customer', ?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$registration['username'], $registration['password'], $registration['email'], $registration['first_name'], $registration['last_name'], $registration['phone'], $registration['address'], $registration['city'], $registration['nid_number'], $registration['date_of_birth']]);
        
        $user_id = $pdo->lastInsertId();
        
        // Create account
        $account_number = 'LB' . date('Y') . str_pad($user_id, 8, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("INSERT INTO accounts (account_number, user_id, account_type_id, branch_id, balance, opened_date) 
                              VALUES (?, ?, ?, ?, ?, CURDATE())");
        $stmt->execute([$account_number, $user_id, $registration['account_type_id'], $registration['branch_id'], $registration['initial_deposit']]);
        
        $account_id = $pdo->lastInsertId();
        
        // Create initial transaction
        $transaction_id = 'TXN' . date('YmdHis');
        $stmt = $pdo->prepare("INSERT INTO transactions (transaction_id, account_id, type, amount, description, status) 
                              VALUES (?, ?, 'deposit', ?, 'Initial deposit - Account opening', 'completed')");
        $stmt->execute([$transaction_id, $account_id, $registration['initial_deposit']]);
        
        // Update registration status
        $stmt = $pdo->prepare("UPDATE s_pending_registrations SET status = 'approved', approved_by = ?, approval_date = NOW() WHERE id = ?");
        $stmt->execute([$_SESSION['user_id'], $data['registration_id']]);
        
        $pdo->commit();
        
        logAction("REGISTRATION_APPROVED", "Approved registration ID: " . $data['registration_id']);
        
        return ['success' => true, 'message' => 'Registration approved successfully'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error approving registration: ' . $e->getMessage()];
    }
}

function rejectRegistration($pdo, $data) {
    try {
        $stmt = $pdo->prepare("UPDATE s_pending_registrations SET status = 'rejected', approved_by = ?, approval_date = NOW(), rejection_reason = ? WHERE id = ?");
        $stmt->execute([$_SESSION['user_id'], $data['rejection_reason'], $data['registration_id']]);
        
        logAction("REGISTRATION_REJECTED", "Rejected registration ID: " . $data['registration_id']);
        
        return ['success' => true, 'message' => 'Registration rejected successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error rejecting registration: ' . $e->getMessage()];
    }
}

// System Settings Functions
function getSystemSettings($pdo, $category = null) {
    $sql = "SELECT * FROM system_settings WHERE 1=1";
    $params = [];
    
    if ($category) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    
    $sql .= " ORDER BY category, setting_key";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $settings = $stmt->fetchAll();
    
    // Convert to associative array by key
    $result = [];
    foreach ($settings as $setting) {
        // Convert value based on type
        switch ($setting['setting_type']) {
            case 'number':
                $result[$setting['setting_key']] = floatval($setting['setting_value']);
                break;
            case 'boolean':
                $result[$setting['setting_key']] = (bool)$setting['setting_value'];
                break;
            case 'json':
                $result[$setting['setting_key']] = json_decode($setting['setting_value'], true);
                break;
            default:
                $result[$setting['setting_key']] = $setting['setting_value'];
        }
    }
    
    return $result;
}

function updateSystemSettings($pdo, $data) {
    try {
        $pdo->beginTransaction();
        
        foreach ($data as $key => $value) {
            if (strpos($key, 'setting_') === 0) {
                $setting_key = substr($key, 8); // Remove 'setting_' prefix
                
                // Get current setting type
                $stmt = $pdo->prepare("SELECT setting_type FROM system_settings WHERE setting_key = ?");
                $stmt->execute([$setting_key]);
                $setting = $stmt->fetch();
                
                if ($setting) {
                    $stmt = $pdo->prepare("UPDATE system_settings SET setting_value = ?, updated_by = ?, updated_at = NOW() WHERE setting_key = ?");
                    $stmt->execute([$value, $_SESSION['user_id'], $setting_key]);
                } else {
                    // Insert new setting if it doesn't exist
                    $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_type, updated_by) VALUES (?, ?, 'string', ?)");
                    $stmt->execute([$setting_key, $value, $_SESSION['user_id']]);
                }
            }
        }
        
        $pdo->commit();
        
        logAction("SYSTEM_SETTINGS_UPDATED", "Updated system settings");
        
        return ['success' => true, 'message' => 'System settings updated successfully'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error updating system settings: ' . $e->getMessage()];
    }
}

function resetSystemSettings($pdo, $category = null) {
    try {
        // For now, we'll reset to hardcoded defaults
        // In a real application, you'd have a default_value column
        $defaults = [
            'bank_name' => 'LOSERS BANK',
            'bank_currency' => 'BDT',
            'default_interest_rate' => '8.5',
            'transaction_fee' => '0.5',
            'min_balance' => '500',
            'max_withdrawal' => '50000',
            'session_timeout' => '30',
            'max_login_attempts' => '5',
            'password_expiry' => '90',
            'two_factor_auth' => '1'
        ];
        
        $pdo->beginTransaction();
        foreach ($defaults as $key => $value) {
            $stmt = $pdo->prepare("UPDATE system_settings SET setting_value = ?, updated_by = ? WHERE setting_key = ?");
            $stmt->execute([$value, $_SESSION['user_id'], $key]);
        }
        $pdo->commit();
        
        logAction("SYSTEM_SETTINGS_RESET", "Reset system settings");
        
        return ['success' => true, 'message' => 'System settings reset to defaults'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error resetting system settings: ' . $e->getMessage()];
    }
}

// Notification Functions
function getNotifications($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? OR user_id IS NULL ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetchAll();
}

function markNotificationRead($pdo, $data) {
    try {
        if (isset($data['notification_id'])) {
            $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
            $stmt->execute([$data['notification_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
        }
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Export Functions
function exportData($pdo, $data) {
    try {
        $type = $data['type'] ?? 'customers';
        $filename = $type . '_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        switch ($type) {
            case 'customers':
                $stmt = $pdo->query("SELECT id, first_name, last_name, email, phone, city, status, created_at FROM users WHERE role = 'customer'");
                break;
            case 'transactions':
                $stmt = $pdo->query("SELECT t.transaction_id, a.account_number, u.first_name, u.last_name, t.type, t.amount, t.status, t.created_at 
                                   FROM transactions t 
                                   JOIN accounts a ON t.account_id = a.id 
                                   JOIN users u ON a.user_id = u.id");
                break;
            case 'loans':
                $stmt = $pdo->query("SELECT l.loan_number, u.first_name, u.last_name, l.loan_type, l.amount, l.interest_rate, l.status, l.applied_date 
                                   FROM loans l 
                                   JOIN users u ON l.user_id = u.id");
                break;
            default:
                return ['success' => false, 'message' => 'Invalid export type'];
        }
        
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($data)) {
            return ['success' => false, 'message' => 'No data to export'];
        }
        
        logAction("DATA_EXPORTED", "Exported $type data");
        
        return [
            'success' => true, 
            'message' => 'Data exported successfully',
            'filename' => $filename,
            'data' => $data
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error exporting data: ' . $e->getMessage()];
    }
}

// Helper function for logging
function logAction($action, $description) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO s_system_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $action, $description, $_SERVER['REMOTE_ADDR']]);
}

// Get initial data for the page
$dashboard_stats = getDashboardStats($pdo);
$customers = getCustomers($pdo);
$accounts = getAccounts($pdo);
$transactions = getTransactions($pdo);
$loans = getLoans($pdo);
$employees = getEmployees($pdo);
$branches = getBranches($pdo);
$pending_registrations = getPendingRegistrations($pdo);
$system_settings = getSystemSettings($pdo);
$notifications = getNotifications($pdo);

// Get additional data for dropdowns
$account_types = $pdo->query("SELECT * FROM account_types")->fetchAll();
$branches_list = $pdo->query("SELECT * FROM branches")->fetchAll();

// Calculate unread notifications count
$unread_notifications = array_filter($notifications, function($notification) {
    return !$notification['is_read'];
});
$unread_count = count($unread_notifications);
?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOSERS BANK - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #34495e;
            --accent: #3498db;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
            --info: #1abc9c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --sidebar-width: 260px;
            --header-height: 70px;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        .dark-mode {
            --primary: #34495e;
            --secondary: #2c3e50;
            --light: #2c3e50;
            --dark: #ecf0f1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            transition: var(--transition);
            overflow-x: hidden;
        }

        body.dark-mode {
            background-color: #1a2530;
            color: #ecf0f1;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(to bottom, var(--primary), var(--secondary));
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: var(--transition);
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            height: var(--header-height);
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary);
            font-weight: bold;
            font-size: 18px;
        }

        .logo h1 {
            font-size: 20px;
            font-weight: 700;
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
        }

        .menu-section {
            padding: 0 20px;
            margin-top: 20px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.6);
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
            border-left: 4px solid transparent;
            cursor: pointer;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 4px solid var(--accent);
        }

        .sidebar-menu i {
            margin-right: 15px;
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        .menu-badge {
            margin-left: auto;
            background-color: var(--danger);
            color: white;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 12px;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: var(--transition);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: white;
            padding: 0 25px;
            height: var(--header-height);
            box-shadow: var(--box-shadow);
            position: sticky;
            top: 0;
            z-index: 100;
            transition: var(--transition);
        }

        body.dark-mode .header {
            background-color: var(--primary);
        }

        .header-left h2 {
            color: var(--primary);
            font-weight: 600;
        }

        body.dark-mode .header-left h2 {
            color: white;
        }

        .header-right {
            display: flex;
            align-items: center;
        }

        .theme-toggle {
            margin-right: 20px;
            cursor: pointer;
            font-size: 20px;
            color: var(--dark);
        }

        body.dark-mode .theme-toggle {
            color: white;
        }

        .notification-icon {
            position: relative;
            margin-right: 25px;
            font-size: 20px;
            color: var(--dark);
            cursor: pointer;
        }

        body.dark-mode .notification-icon {
            color: white;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-profile {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--info));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }

        /* Content Area */
        .content {
            padding: 25px;
        }

        /* Section Styles */
        .section {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .section.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
        }

        body.dark-mode .section-title {
            color: white;
        }

        .section-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border-radius: var(--border-radius);
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background-color: var(--accent);
            color: white;
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-warning {
            background-color: var(--warning);
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        /* Table Styles */
        .table-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 30px;
        }

        body.dark-mode .table-container {
            background-color: var(--primary);
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        body.dark-mode th, body.dark-mode td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: var(--dark);
        }

        body.dark-mode th {
            background-color: rgba(255, 255, 255, 0.05);
            color: white;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        body.dark-mode tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-active {
            background-color: rgba(46, 204, 113, 0.2);
            color: var(--success);
        }

        .status-pending {
            background-color: rgba(243, 156, 18, 0.2);
            color: var(--warning);
        }

        .status-suspended {
            background-color: rgba(231, 76, 60, 0.2);
            color: var(--danger);
        }

        /* Stats Cards */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            transition: var(--transition);
            border-top: 4px solid var(--accent);
        }

        body.dark-mode .stat-card {
            background-color: var(--primary);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-title {
            font-size: 14px;
            color: var(--dark);
            font-weight: 500;
        }

        body.dark-mode .stat-title {
            color: rgba(255, 255, 255, 0.7);
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .customers .stat-icon {
            background-color: var(--accent);
        }

        .accounts .stat-icon {
            background-color: var(--info);
        }

        .balance .stat-icon {
            background-color: var(--success);
        }

        .loans .stat-icon {
            background-color: var(--warning);
        }

        .deposits .stat-icon {
            background-color: var(--danger);
        }

        .tickets .stat-icon {
            background-color: #9b59b6;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-change {
            font-size: 12px;
            display: flex;
            align-items: center;
        }

        .positive {
            color: var(--success);
        }

        .negative {
            color: var(--danger);
        }

        /* Charts Section */
        .charts-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            transition: var(--transition);
        }

        body.dark-mode .chart-container {
            background-color: var(--primary);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
        }

        body.dark-mode .chart-title {
            color: white;
        }

        .chart-actions {
            display: flex;
            gap: 10px;
        }

        .chart-actions select {
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            background-color: white;
        }

        body.dark-mode .chart-actions select {
            background-color: var(--secondary);
            border-color: var(--secondary);
            color: white;
        }

        .chart-wrapper {
            height: 300px;
            position: relative;
        }

        /* Form Styles */
        .form-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            margin-bottom: 30px;
        }

        body.dark-mode .form-container {
            background-color: var(--primary);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        body.dark-mode .form-label {
            color: white;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border-radius: var(--border-radius);
            border: 1px solid #ddd;
            background-color: white;
            transition: var(--transition);
        }

        body.dark-mode .form-control {
            background-color: var(--secondary);
            border-color: var(--secondary);
            color: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--dark);
            cursor: pointer;
            margin-right: 15px;
        }

        body.dark-mode .mobile-menu-toggle {
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .sidebar {
                transform: translateX(-100%);
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
            
            .charts-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 992px) {
            .stats-cards {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .section-actions {
                width: 100%;
                justify-content: flex-start;
            }
        }

        @media (max-width: 768px) {
            .stats-cards {
                grid-template-columns: 1fr;
            }
            
            .content {
                padding: 15px;
            }
            
            .header {
                padding: 0 15px;
            }
            
            .user-profile .user-name {
                display: none;
            }
            
            th, td {
                padding: 10px;
                font-size: 14px;
            }
        }

        @media (max-width: 576px) {
            .btn {
                padding: 8px 15px;
                font-size: 14px;
            }
            
            .section-title {
                font-size: 20px;
            }
            
            .chart-wrapper {
                height: 250px;
            }
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 500px;
            box-shadow: var(--box-shadow);
            max-height: 90vh;
            overflow-y: auto;
        }

        body.dark-mode .modal-content {
            background-color: var(--primary);
            color: white;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        body.dark-mode .close:hover {
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .action-buttons .btn {
            padding: 5px 10px;
            font-size: 12px;
        }

        /* Notification Styles */
        .badge-success { background-color: var(--success); }
        .badge-warning { background-color: var(--warning); }
        .badge-danger { background-color: var(--danger); }
        .badge-info { background-color: var(--info); }

        .notification-item {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .notification-item:hover {
            background-color: #e9ecef !important;
        }

        .notification-item.unread {
            font-weight: bold;
        }

        /* Custom Modal Styles */
        .custom-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .custom-modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 800px;
            box-shadow: var(--box-shadow);
            max-height: 85vh;
            overflow-y: auto;
            position: relative;
        }

        body.dark-mode .custom-modal-content {
            background-color: var(--primary);
            color: white;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="logo">
                <div class="logo-icon">LB</div>
                <h1>LOSERS BANK</h1>
            </div>
            <ul class="sidebar-menu">
                <div class="menu-section">Main</div>
                <li><a class="menu-link active" data-section="dashboard"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                
                <div class="menu-section">Management</div>
                <li><a class="menu-link" data-section="customers"><i class="fas fa-users"></i> <span>Customers</span> <span class="menu-badge" id="customers-badge"><?php echo count($customers); ?></span></a></li>
                <li><a class="menu-link" data-section="accounts"><i class="fas fa-university"></i> <span>Accounts</span></a></li>
                <li><a class="menu-link" data-section="transactions"><i class="fas fa-exchange-alt"></i> <span>Transactions</span></a></li>
                <li><a class="menu-link" data-section="loans"><i class="fas fa-hand-holding-usd"></i> <span>Loans</span> <span class="menu-badge" id="loans-badge"><?php echo count(array_filter($loans, function($loan) { return $loan['status'] == 'pending'; })); ?></span></a></li>
                <li><a class="menu-link" data-section="branches"><i class="fas fa-code-branch"></i> <span>Branches</span></a></li>
                
                <div class="menu-section">Administration</div>
                <li><a class="menu-link" data-section="employees"><i class="fas fa-user-tie"></i> <span>Employees</span></a></li>
                <li><a class="menu-link" data-section="registrations"><i class="fas fa-user-plus"></i> <span>Registrations</span> <span class="menu-badge" id="registrations-badge"><?php echo count($pending_registrations); ?></span></a></li>
                
                <div class="menu-section">Security</div>
                <li><a class="menu-link" data-section="activity-logs"><i class="fas fa-clipboard-list"></i> <span>Activity Logs</span></a></li>
                <li><a class="menu-link" data-section="system-settings"><i class="fas fa-cogs"></i> <span>System Settings</span></a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-left">
                    <button class="mobile-menu-toggle" id="mobileMenuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 id="pageTitle">Admin Dashboard</h2>
                </div>
                <div class="header-right">
                    <div class="theme-toggle" id="themeToggle">
                        <i class="fas fa-moon"></i>
                    </div>
                    <div class="notification-icon" onclick="showNotifications()">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" style="display: <?php echo $unread_count > 0 ? 'flex' : 'none'; ?>;"><?php echo $unread_count; ?></span>
                    </div>
                    <div class="user-profile">
                        <div class="profile-pic"><?php echo strtoupper(substr($current_user['first_name'], 0, 1)); ?></div>
                        <div class="user-name"><?php echo $current_user['first_name'] . ' ' . $current_user['last_name']; ?></div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content">
                <!-- Dashboard Section -->
                <div class="section active" id="dashboard">
                    <div class="section-header">
                        <div class="section-title">Dashboard Overview</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" id="refreshDashboard"><i class="fas fa-sync-alt"></i> Refresh Data</button>
                            <button class="btn btn-success" onclick="exportReport()"><i class="fas fa-download"></i> Export Report</button>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="stats-cards">
                        <div class="stat-card customers">
                            <div class="stat-header">
                                <div class="stat-title">Total Customers</div>
                                <div class="stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="stat-value" id="total-customers"><?php echo $dashboard_stats['total_customers']; ?></div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> Active
                            </div>
                        </div>
                        
                        <div class="stat-card accounts">
                            <div class="stat-header">
                                <div class="stat-title">Total Accounts</div>
                                <div class="stat-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                            </div>
                            <div class="stat-value" id="total-accounts"><?php echo $dashboard_stats['total_accounts']; ?></div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> Active
                            </div>
                        </div>
                        
                        <div class="stat-card balance">
                            <div class="stat-header">
                                <div class="stat-title">Total Balance</div>
                                <div class="stat-icon">
                                    <i class="fas fa-taka-sign"></i>
                                </div>
                            </div>
                            <div class="stat-value" id="total-balance">৳ <?php echo number_format($dashboard_stats['total_balance'], 2); ?></div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> Current
                            </div>
                        </div>
                        
                        <div class="stat-card loans">
                            <div class="stat-header">
                                <div class="stat-title">Pending Loans</div>
                                <div class="stat-icon">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                            </div>
                            <div class="stat-value" id="pending-loans"><?php echo $dashboard_stats['pending_loans']; ?></div>
                            <div class="stat-change negative">
                                <i class="fas fa-clock"></i> Awaiting Approval
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-title">Total Employees</div>
                                <div class="stat-icon" style="background-color: #9b59b6;">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                            </div>
                            <div class="stat-value" id="total-employees"><?php echo $dashboard_stats['total_employees']; ?></div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> Active
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-title">Pending Registrations</div>
                                <div class="stat-icon" style="background-color: #f39c12;">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                            </div>
                            <div class="stat-value" id="pending-registrations"><?php echo $dashboard_stats['pending_registrations']; ?></div>
                            <div class="stat-change negative">
                                <i class="fas fa-clock"></i> Awaiting Approval
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="charts-row">
                        <div class="chart-container">
                            <div class="chart-header">
                                <div class="chart-title">Monthly Transactions</div>
                                <div class="chart-actions">
                                    <select id="chart-period">
                                        <option>Last 6 Months</option>
                                        <option>Last Year</option>
                                        <option>Last 2 Years</option>
                                    </select>
                                </div>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="transactionChart"></canvas>
                            </div>
                        </div>
                        
                        <div class="chart-container">
                            <div class="chart-header">
                                <div class="chart-title">Income vs Expenses</div>
                                <div class="chart-actions">
                                    <select id="income-expense-period">
                                        <option>This Month</option>
                                        <option>Last Month</option>
                                        <option>This Quarter</option>
                                    </select>
                                </div>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="incomeExpenseChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="table-container">
                        <div class="chart-header">
                            <div class="chart-title">Recent Transactions</div>
                            <button class="btn btn-primary" onclick="showSection('transactions')">View All</button>
                        </div>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-transactions-body">
                                    <?php foreach ($dashboard_stats['recent_transactions'] as $transaction): ?>
                                    <tr>
                                        <td>#<?php echo $transaction['transaction_id']; ?></td>
                                        <td><?php echo $transaction['first_name'] . ' ' . $transaction['last_name']; ?></td>
                                        <td><?php echo ucfirst($transaction['type']); ?></td>
                                        <td>৳ <?php echo number_format($transaction['amount'], 2); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($transaction['created_at'])); ?></td>
                                        <td><span class="status status-active"><?php echo ucfirst($transaction['status']); ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Customers Section -->
                <div class="section" id="customers">
                    <div class="section-header">
                        <div class="section-title">Customer Management</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="showAddCustomerModal()"><i class="fas fa-plus"></i> Add Customer</button>
                            <button class="btn btn-success" onclick="exportCustomers()"><i class="fas fa-file-export"></i> Export</button>
                            <button class="btn btn-warning" onclick="refreshCustomers()"><i class="fas fa-sync-alt"></i> Refresh</button>
                        </div>
                    </div>

                    <div class="table-container">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Customer ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Accounts</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="customers-body">
                                    <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td>#CUS-<?php echo $customer['id']; ?></td>
                                        <td><?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?></td>
                                        <td><?php echo $customer['email']; ?></td>
                                        <td><?php echo $customer['phone']; ?></td>
                                        <td><?php echo $customer['account_count']; ?></td>
                                        <td>
                                            <span class="status status-<?php echo $customer['status'] == 'active' ? 'active' : 'pending'; ?>">
                                                <?php echo ucfirst($customer['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-primary" onclick="viewCustomer(<?php echo $customer['id']; ?>)">View</button>
                                                <button class="btn btn-success" onclick="editCustomer(<?php echo $customer['id']; ?>)">Edit</button>
                                                <button class="btn btn-danger" onclick="deleteCustomer(<?php echo $customer['id']; ?>)">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Accounts Section -->
                <div class="section" id="accounts">
                    <div class="section-header">
                        <div class="section-title">Account Management</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="showCreateAccountModal()"><i class="fas fa-plus"></i> Create Account</button>
                            <button class="btn btn-warning" onclick="refreshAccounts()"><i class="fas fa-sync-alt"></i> Refresh</button>
                        </div>
                    </div>

                    <div class="table-container">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Account No</th>
                                        <th>Customer</th>
                                        <th>Type</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th>Open Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="accounts-body">
                                    <?php foreach ($accounts as $account): ?>
                                    <tr>
                                        <td><?php echo $account['account_number']; ?></td>
                                        <td><?php echo $account['first_name'] . ' ' . $account['last_name']; ?></td>
                                        <td><?php echo $account['account_type']; ?></td>
                                        <td>৳ <?php echo number_format($account['balance'], 2); ?></td>
                                        <td>
                                            <span class="status status-<?php echo $account['status'] == 'active' ? 'active' : ($account['status'] == 'frozen' ? 'suspended' : 'pending'); ?>">
                                                <?php echo ucfirst($account['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($account['opened_date'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-primary" onclick="viewAccount(<?php echo $account['id']; ?>)">View</button>
                                                <button class="btn btn-warning" onclick="toggleAccountStatus(<?php echo $account['id']; ?>, '<?php echo $account['status']; ?>')">
                                                    <?php echo $account['status'] == 'frozen' ? 'Unfreeze' : 'Freeze'; ?>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Transactions Section -->
                <div class="section" id="transactions">
                    <div class="section-header">
                        <div class="section-title">Transactions Management</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="showAddTransactionModal()"><i class="fas fa-plus"></i> New Transaction</button>
                            <button class="btn btn-success" onclick="exportTransactions()"><i class="fas fa-file-export"></i> Export</button>
                            <button class="btn btn-warning" onclick="refreshTransactions()"><i class="fas fa-sync-alt"></i> Refresh</button>
                        </div>
                    </div>
                    <div class="form-container">
                        <h3>Transaction Filters</h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
                            <div class="form-group">
                                <label class="form-label">Transaction Type</label>
                                <select class="form-control" id="filter-type">
                                    <option value="">All Types</option>
                                    <option value="deposit">Deposit</option>
                                    <option value="withdrawal">Withdrawal</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date From</label>
                                <input type="date" class="form-control" id="filter-date-from">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date To</label>
                                <input type="date" class="form-control" id="filter-date-to">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Amount Range</label>
                                <select class="form-control" id="filter-amount">
                                    <option value="">Any Amount</option>
                                    <option value="0-10000">Up to ৳ 10,000</option>
                                    <option value="10000-50000">৳ 10,000 - ৳ 50,000</option>
                                    <option value="50000+">Over ৳ 50,000</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-primary" style="margin-top: 15px;" onclick="applyTransactionFilters()">Apply Filters</button>
                    </div>
                    <div class="table-container">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Account</th>
                                        <th>Customer</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="transactions-body">
                                    <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td>#<?php echo $transaction['transaction_id']; ?></td>
                                        <td><?php echo $transaction['account_number']; ?></td>
                                        <td><?php echo $transaction['first_name'] . ' ' . $transaction['last_name']; ?></td>
                                        <td><?php echo ucfirst($transaction['type']); ?></td>
                                        <td>৳ <?php echo number_format($transaction['amount'], 2); ?></td>
                                        <td><?php echo date('M j, Y H:i', strtotime($transaction['created_at'])); ?></td>
                                        <td><span class="status status-active"><?php echo ucfirst($transaction['status']); ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Loans Section -->
                <div class="section" id="loans">
                    <div class="section-header">
                        <div class="section-title">Loan Management</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="showAddLoanModal()"><i class="fas fa-plus"></i> New Loan</button>
                            <button class="btn btn-success" onclick="approveSelectedLoans()"><i class="fas fa-check"></i> Approve Selected</button>
                            <button class="btn btn-danger" onclick="rejectSelectedLoans()"><i class="fas fa-times"></i> Reject Selected</button>
                            <button class="btn btn-warning" onclick="refreshLoans()"><i class="fas fa-sync-alt"></i> Refresh</button>
                        </div>
                    </div>
                    <div class="table-container">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all-loans"></th>
                                        <th>Loan Number</th>
                                        <th>Customer</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Interest Rate</th>
                                        <th>Term</th>
                                        <th>Status</th>
                                        <th>Applied Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="loans-body">
                                    <?php foreach ($loans as $loan): ?>
                                    <tr>
                                        <td><input type="checkbox" class="loan-checkbox" value="<?php echo $loan['id']; ?>" <?php echo $loan['status'] != 'pending' ? 'disabled' : ''; ?>></td>
                                        <td><?php echo $loan['loan_number']; ?></td>
                                        <td><?php echo $loan['first_name'] . ' ' . $loan['last_name']; ?></td>
                                        <td><?php echo ucfirst($loan['loan_type']); ?></td>
                                        <td>৳ <?php echo number_format($loan['amount'], 2); ?></td>
                                        <td><?php echo $loan['interest_rate']; ?>%</td>
                                        <td><?php echo $loan['term_months']; ?> months</td>
                                        <td>
                                            <span class="status status-<?php echo $loan['status'] == 'approved' ? 'active' : ($loan['status'] == 'pending' ? 'pending' : 'suspended'); ?>">
                                                <?php echo ucfirst($loan['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($loan['applied_date'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <?php if ($loan['status'] == 'pending'): ?>
                                                <button class="btn btn-success" onclick="approveLoan(<?php echo $loan['id']; ?>)">Approve</button>
                                                <button class="btn btn-danger" onclick="rejectLoan(<?php echo $loan['id']; ?>)">Reject</button>
                                                <?php endif; ?>
                                                <button class="btn btn-primary" onclick="viewLoan(<?php echo $loan['id']; ?>)">View</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Branches Section -->
                <div class="section" id="branches">
                    <div class="section-header">
                        <div class="section-title">Branch Management</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="showAddBranchModal()"><i class="fas fa-plus"></i> Add Branch</button>
                            <button class="btn btn-warning" onclick="refreshBranches()"><i class="fas fa-sync-alt"></i> Refresh</button>
                        </div>
                    </div>
                    <div class="table-container">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Branch ID</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Manager</th>
                                        <th>Accounts</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="branches-body">
                                    <?php foreach ($branches as $branch): ?>
                                    <tr>
                                        <td>#BR-<?php echo $branch['id']; ?></td>
                                        <td><?php echo $branch['name']; ?></td>
                                        <td><?php echo $branch['address']; ?></td>
                                        <td><?php echo $branch['phone']; ?></td>
                                        <td><?php echo $branch['email']; ?></td>
                                        <td><?php echo $branch['manager_name']; ?></td>
                                        <td><?php echo $branch['account_count']; ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-primary" onclick="viewBranch(<?php echo $branch['id']; ?>)">View</button>
                                                <button class="btn btn-success" onclick="editBranch(<?php echo $branch['id']; ?>)">Edit</button>
                                                <button class="btn btn-danger" onclick="deleteBranch(<?php echo $branch['id']; ?>)">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Employees Section -->
                <div class="section" id="employees">
                    <div class="section-header">
                        <div class="section-title">Employee Management</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="showAddEmployeeModal()"><i class="fas fa-plus"></i> Add Employee</button>
                            <button class="btn btn-warning" onclick="refreshEmployees()"><i class="fas fa-sync-alt"></i> Refresh</button>
                        </div>
                    </div>
                    <div class="table-container">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Join Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="employees-body">
                                    <?php foreach ($employees as $employee): ?>
                                    <tr>
                                        <td>#EMP-<?php echo $employee['id']; ?></td>
                                        <td><?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?></td>
                                        <td><?php echo $employee['email']; ?></td>
                                        <td><?php echo $employee['phone']; ?></td>
                                        <td><?php echo ucfirst($employee['role']); ?></td>
                                        <td>
                                            <span class="status status-<?php echo $employee['status'] == 'active' ? 'active' : 'pending'; ?>">
                                                <?php echo ucfirst($employee['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($employee['created_at'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-primary" onclick="viewEmployee(<?php echo $employee['id']; ?>)">View</button>
                                                <button class="btn btn-success" onclick="editEmployee(<?php echo $employee['id']; ?>)">Edit</button>
                                                <button class="btn btn-danger" onclick="deleteEmployee(<?php echo $employee['id']; ?>)">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Registrations Section -->
                <div class="section" id="registrations">
                    <div class="section-header">
                        <div class="section-title">Pending Registrations</div>
                        <div class="section-actions">
                            <button class="btn btn-warning" onclick="refreshRegistrations()"><i class="fas fa-sync-alt"></i> Refresh</button>
                        </div>
                    </div>
                    <div class="table-container">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Registration ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Account Type</th>
                                        <th>Branch</th>
                                        <th>Initial Deposit</th>
                                        <th>Applied Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="registrations-body">
                                    <?php foreach ($pending_registrations as $registration): ?>
                                    <tr>
                                        <td>#REG-<?php echo $registration['id']; ?></td>
                                        <td><?php echo $registration['first_name'] . ' ' . $registration['last_name']; ?></td>
                                        <td><?php echo $registration['email']; ?></td>
                                        <td><?php echo $registration['phone']; ?></td>
                                        <td><?php echo $registration['account_type_name']; ?></td>
                                        <td><?php echo $registration['branch_name']; ?></td>
                                        <td>৳ <?php echo number_format($registration['initial_deposit'], 2); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($registration['created_at'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-success" onclick="approveRegistration(<?php echo $registration['id']; ?>)">Approve</button>
                                                <button class="btn btn-danger" onclick="rejectRegistration(<?php echo $registration['id']; ?>)">Reject</button>
                                                <button class="btn btn-primary" onclick="viewRegistration(<?php echo $registration['id']; ?>)">View</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Activity Logs Section -->
                <div class="section" id="activity-logs">
                    <div class="section-header">
                        <div class="section-title">Activity Logs</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="exportLogs()"><i class="fas fa-download"></i> Export Logs</button>
                            <button class="btn btn-danger" onclick="clearOldLogs()"><i class="fas fa-trash"></i> Clear Old Logs</button>
                            <button class="btn btn-warning" onclick="refreshLogs()"><i class="fas fa-sync-alt"></i> Refresh</button>
                        </div>
                    </div>
                    <div class="form-container">
                        <h3>Filter Logs</h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
                            <div class="form-group">
                                <label class="form-label">Date From</label>
                                <input type="date" class="form-control" id="log-date-from">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date To</label>
                                <input type="date" class="form-control" id="log-date-to">
                            </div>
                            <div class="form-group">
                                <label class="form-label">User</label>
                                <select class="form-control" id="log-user">
                                    <option value="">All Users</option>
                                    <?php foreach ($employees as $employee): ?>
                                    <option value="<?php echo $employee['id']; ?>"><?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-primary" style="margin-top: 15px;" onclick="applyLogFilters()">Apply Filters</button>
                    </div>
                    <div class="table-container">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>IP Address</th>
                                        <th>Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody id="activity-logs-body">
                                    <?php
                                    $logs = getSystemLogs($pdo);
                                    foreach ($logs as $log): ?>
                                    <tr>
                                        <td>#LOG-<?php echo $log['id']; ?></td>
                                        <td><?php echo $log['first_name'] ? $log['first_name'] . ' ' . $log['last_name'] : 'System'; ?></td>
                                        <td><?php echo $log['action']; ?></td>
                                        <td><?php echo $log['description']; ?></td>
                                        <td><?php echo $log['ip_address']; ?></td>
                                        <td><?php echo date('M j, Y H:i:s', strtotime($log['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- System Settings Section -->
                <div class="section" id="system-settings">
                    <div class="section-header">
                        <div class="section-title">System Settings</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="saveSystemSettings()"><i class="fas fa-save"></i> Save All Settings</button>
                            <button class="btn btn-warning" onclick="resetSystemSettings()"><i class="fas fa-undo"></i> Reset to Default</button>
                            <button class="btn btn-success" onclick="refreshSystemSettings()"><i class="fas fa-sync-alt"></i> Refresh</button>
                        </div>
                    </div>

                    <div class="form-container">
                        <h3><i class="fas fa-cog"></i> General Settings</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="setting_bank_name" data-type="string" value="<?php echo $system_settings['bank_name'] ?? 'LOSERS BANK'; ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Currency</label>
                                <select class="form-control" id="setting_bank_currency" data-type="string">
                                    <option value="BDT" <?php echo ($system_settings['bank_currency'] ?? 'BDT') == 'BDT' ? 'selected' : ''; ?>>Bangladeshi Taka (৳)</option>
                                    <option value="USD" <?php echo ($system_settings['bank_currency'] ?? 'BDT') == 'USD' ? 'selected' : ''; ?>>US Dollar ($)</option>
                                    <option value="EUR" <?php echo ($system_settings['bank_currency'] ?? 'BDT') == 'EUR' ? 'selected' : ''; ?>>Euro (€)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Default Interest Rate (%)</label>
                                <input type="number" class="form-control" id="setting_default_interest_rate" data-type="number" step="0.1" value="<?php echo $system_settings['default_interest_rate'] ?? '8.5'; ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Transaction Fee (%)</label>
                                <input type="number" class="form-control" id="setting_transaction_fee" data-type="number" step="0.1" value="<?php echo $system_settings['transaction_fee'] ?? '0.5'; ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Minimum Account Balance</label>
                                <input type="number" class="form-control" id="setting_min_balance" data-type="number" step="0.01" value="<?php echo $system_settings['min_balance'] ?? '500'; ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Maximum Daily Withdrawal</label>
                                <input type="number" class="form-control" id="setting_max_withdrawal" data-type="number" step="0.01" value="<?php echo $system_settings['max_withdrawal'] ?? '50000'; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-container">
                        <h3><i class="fas fa-shield-alt"></i> Security Settings</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">Session Timeout (minutes)</label>
                                <input type="number" class="form-control" id="setting_session_timeout" data-type="number" value="<?php echo $system_settings['session_timeout'] ?? '30'; ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Max Login Attempts</label>
                                <input type="number" class="form-control" id="setting_max_login_attempts" data-type="number" value="<?php echo $system_settings['max_login_attempts'] ?? '5'; ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Password Expiry (days)</label>
                                <input type="number" class="form-control" id="setting_password_expiry" data-type="number" value="<?php echo $system_settings['password_expiry'] ?? '90'; ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Enable Two-Factor Authentication</label>
                                <select class="form-control" id="setting_two_factor_auth" data-type="boolean">
                                    <option value="1" <?php echo ($system_settings['two_factor_auth'] ?? 1) ? 'selected' : ''; ?>>Enabled</option>
                                    <option value="0" <?php echo !($system_settings['two_factor_auth'] ?? 1) ? 'selected' : ''; ?>>Disabled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div id="addCustomerModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addCustomerModal')">&times;</span>
            <h2>Add New Customer</h2>
            <form id="addCustomerForm">
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="first_name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="last_name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-control" name="phone" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" name="city" required>
                </div>
                <div class="form-group">
                    <label class="form-label">NID Number</label>
                    <input type="text" class="form-control" name="nid_number" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="date_of_birth" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Account Type</label>
                    <select class="form-control" name="account_type_id" required>
                        <?php foreach ($account_types as $type): ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Branch</label>
                    <select class="form-control" name="branch_id" required>
                        <?php foreach ($branches_list as $branch): ?>
                        <option value="<?php echo $branch['id']; ?>"><?php echo $branch['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Initial Deposit</label>
                    <input type="number" class="form-control" name="initial_deposit" step="0.01" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Customer</button>
            </form>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div id="editCustomerModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editCustomerModal')">&times;</span>
            <h2>Edit Customer</h2>
            <form id="editCustomerForm">
                <input type="hidden" name="customer_id" id="edit_customer_id">
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="first_name" id="edit_first_name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="last_name" id="edit_last_name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="edit_email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-control" name="phone" id="edit_phone" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" id="edit_address" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" name="city" id="edit_city" required>
                </div>
                <div class="form-group">
                    <label class="form-label">NID Number</label>
                    <input type="text" class="form-control" name="nid_number" id="edit_nid_number" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="date_of_birth" id="edit_date_of_birth" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select class="form-control" name="status" id="edit_status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Customer</button>
            </form>
        </div>
    </div>

    <!-- Add Transaction Modal -->
    <div id="addTransactionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addTransactionModal')">&times;</span>
            <h2>New Transaction</h2>
            <form id="addTransactionForm">
                <div class="form-group">
                    <label class="form-label">Account</label>
                    <select class="form-control" name="account_id" required>
                        <?php foreach ($accounts as $account): ?>
                        <option value="<?php echo $account['id']; ?>"><?php echo $account['account_number'] . ' - ' . $account['first_name'] . ' ' . $account['last_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Transaction Type</label>
                    <select class="form-control" name="type" required>
                        <option value="deposit">Deposit</option>
                        <option value="withdrawal">Withdrawal</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Amount</label>
                    <input type="number" class="form-control" name="amount" step="0.01" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Process Transaction</button>
            </form>
        </div>
    </div>

    <!-- Add Loan Modal -->
    <div id="addLoanModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addLoanModal')">&times;</span>
            <h2>New Loan Application</h2>
            <form id="addLoanForm">
                <div class="form-group">
                    <label class="form-label">Customer</label>
                    <select class="form-control" name="user_id" required>
                        <?php foreach ($customers as $customer): ?>
                        <option value="<?php echo $customer['id']; ?>"><?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Loan Type</label>
                    <select class="form-control" name="loan_type" required>
                        <option value="personal">Personal Loan</option>
                        <option value="home">Home Loan</option>
                        <option value="car">Car Loan</option>
                        <option value="business">Business Loan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Loan Amount</label>
                    <input type="number" class="form-control" name="amount" step="0.01" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Interest Rate (%)</label>
                    <input type="number" class="form-control" name="interest_rate" step="0.01" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Term (Months)</label>
                    <input type="number" class="form-control" name="term_months" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit Loan Application</button>
            </form>
        </div>
    </div>

    <!-- Create Account Modal -->
    <div id="createAccountModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('createAccountModal')">&times;</span>
            <h2>Create New Account</h2>
            <form id="createAccountForm">
                <div class="form-group">
                    <label class="form-label">Customer</label>
                    <select class="form-control" name="user_id" required>
                        <?php foreach ($customers as $customer): ?>
                        <option value="<?php echo $customer['id']; ?>"><?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Account Type</label>
                    <select class="form-control" name="account_type_id" required>
                        <?php foreach ($account_types as $type): ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Branch</label>
                    <select class="form-control" name="branch_id" required>
                        <?php foreach ($branches_list as $branch): ?>
                        <option value="<?php echo $branch['id']; ?>"><?php echo $branch['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Initial Deposit</label>
                    <input type="number" class="form-control" name="initial_deposit" step="0.01" required>
                </div>
                <button type="submit" class="btn btn-primary">Create Account</button>
            </form>
        </div>
    </div>

    <!-- Add Employee Modal -->
    <div id="addEmployeeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addEmployeeModal')">&times;</span>
            <h2>Add New Employee</h2>
            <form id="addEmployeeForm">
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="first_name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="last_name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-control" name="phone" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select class="form-control" name="role" required>
                        <option value="employee">Employee</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" name="city" required>
                </div>
                <div class="form-group">
                    <label class="form-label">NID Number</label>
                    <input type="text" class="form-control" name="nid_number" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="date_of_birth" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Employee</button>
            </form>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div id="editEmployeeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editEmployeeModal')">&times;</span>
            <h2>Edit Employee</h2>
            <form id="editEmployeeForm">
                <input type="hidden" name="employee_id" id="edit_employee_id">
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="first_name" id="edit_emp_first_name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="last_name" id="edit_emp_last_name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="edit_emp_email" required>
                </div>
                                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-control" name="phone" id="edit_emp_phone" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select class="form-control" name="role" id="edit_emp_role" required>
                        <option value="employee">Employee</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" id="edit_emp_address" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" name="city" id="edit_emp_city" required>
                </div>
                <div class="form-group">
                    <label class="form-label">NID Number</label>
                    <input type="text" class="form-control" name="nid_number" id="edit_emp_nid_number" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="date_of_birth" id="edit_emp_date_of_birth" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select class="form-control" name="status" id="edit_emp_status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Employee</button>
            </form>
        </div>
    </div>

    <!-- Add Branch Modal -->
    <div id="addBranchModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addBranchModal')">&times;</span>
            <h2>Add New Branch</h2>
            <form id="addBranchForm">
                <div class="form-group">
                    <label class="form-label">Branch Name</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-control" name="phone" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Manager Name</label>
                    <input type="text" class="form-control" name="manager_name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Opening Hours</label>
                    <input type="text" class="form-control" name="opening_hours" placeholder="e.g., 9:00 AM - 5:00 PM" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Services</label>
                    <textarea class="form-control" name="services" placeholder="List of services offered" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Facilities</label>
                    <textarea class="form-control" name="facilities" placeholder="List of facilities available" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Add Branch</button>
            </form>
        </div>
    </div>

    <!-- Edit Branch Modal -->
    <div id="editBranchModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editBranchModal')">&times;</span>
            <h2>Edit Branch</h2>
            <form id="editBranchForm">
                <input type="hidden" name="branch_id" id="edit_branch_id">
                <div class="form-group">
                    <label class="form-label">Branch Name</label>
                    <input type="text" class="form-control" name="name" id="edit_branch_name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" id="edit_branch_address" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-control" name="phone" id="edit_branch_phone" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="edit_branch_email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Manager Name</label>
                    <input type="text" class="form-control" name="manager_name" id="edit_branch_manager" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Opening Hours</label>
                    <input type="text" class="form-control" name="opening_hours" id="edit_branch_hours" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Services</label>
                    <textarea class="form-control" name="services" id="edit_branch_services" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Facilities</label>
                    <textarea class="form-control" name="facilities" id="edit_branch_facilities" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Branch</button>
            </form>
        </div>
    </div>

    <!-- Notifications Modal -->
    <div id="notificationsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('notificationsModal')">&times;</span>
            <h2>Notifications</h2>
            <div id="notifications-list">
                <?php foreach ($notifications as $notification): ?>
                <div class="notification-item <?php echo !$notification['is_read'] ? 'unread' : ''; ?>" style="padding: 10px; border-bottom: 1px solid #eee;">
                    <div style="font-weight: <?php echo !$notification['is_read'] ? 'bold' : 'normal'; ?>;">
                        <?php echo $notification['message']; ?>
                    </div>
                    <div style="font-size: 12px; color: #666; margin-top: 5px;">
                        <?php echo date('M j, Y H:i', strtotime($notification['created_at'])); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="btn btn-primary" style="margin-top: 15px; width: 100%;" onclick="markAllNotificationsRead()">Mark All as Read</button>
        </div>
    </div>

    <!-- Reject Registration Modal -->
    <div id="rejectRegistrationModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('rejectRegistrationModal')">&times;</span>
            <h2>Reject Registration</h2>
            <form id="rejectRegistrationForm">
                <input type="hidden" name="registration_id" id="reject_registration_id">
                <div class="form-group">
                    <label class="form-label">Rejection Reason</label>
                    <textarea class="form-control" name="rejection_reason" required placeholder="Please provide a reason for rejection"></textarea>
                </div>
                <button type="submit" class="btn btn-danger">Reject Registration</button>
            </form>
        </div>
    </div>

    <script>
        // Global variables
        let currentSection = 'dashboard';
        let charts = {};

        // Initialize the dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            setupEventListeners();
            loadDashboardStats();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Menu navigation
            document.querySelectorAll('.menu-link').forEach(link => {
                link.addEventListener('click', function() {
                    const section = this.getAttribute('data-section');
                    showSection(section);
                });
            });

            // Mobile menu toggle
            document.getElementById('mobileMenuToggle').addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('active');
            });

            // Theme toggle
            document.getElementById('themeToggle').addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                const icon = this.querySelector('i');
                if (document.body.classList.contains('dark-mode')) {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                } else {
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                }
            });

            // Form submissions
            document.getElementById('addCustomerForm').addEventListener('submit', handleAddCustomer);
            document.getElementById('editCustomerForm').addEventListener('submit', handleEditCustomer);
            document.getElementById('addTransactionForm').addEventListener('submit', handleAddTransaction);
            document.getElementById('addLoanForm').addEventListener('submit', handleAddLoan);
            document.getElementById('createAccountForm').addEventListener('submit', handleCreateAccount);
            document.getElementById('addEmployeeForm').addEventListener('submit', handleAddEmployee);
            document.getElementById('editEmployeeForm').addEventListener('submit', handleEditEmployee);
            document.getElementById('addBranchForm').addEventListener('submit', handleAddBranch);
            document.getElementById('editBranchForm').addEventListener('submit', handleEditBranch);
            document.getElementById('rejectRegistrationForm').addEventListener('submit', handleRejectRegistration);

            // Select all loans checkbox
            document.getElementById('select-all-loans').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.loan-checkbox');
                checkboxes.forEach(checkbox => {
                    if (!checkbox.disabled) {
                        checkbox.checked = this.checked;
                    }
                });
            });

            // Refresh dashboard button
            document.getElementById('refreshDashboard').addEventListener('click', loadDashboardStats);
        }

        // Show section function
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Remove active class from all menu items
            document.querySelectorAll('.menu-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById(sectionId).classList.add('active');
            
            // Add active class to clicked menu item
            document.querySelector(`[data-section="${sectionId}"]`).classList.add('active');
            
            // Update page title
            const pageTitle = document.getElementById('pageTitle');
            const sectionTitle = document.querySelector(`#${sectionId} .section-title`).textContent;
            pageTitle.textContent = sectionTitle + ' - LOSERS BANK';
            
            // Update current section
            currentSection = sectionId;
            
            // Close mobile menu if open
            document.getElementById('sidebar').classList.remove('active');
        }

        // Modal functions
        function showModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // Initialize charts
        function initializeCharts() {
            // Transaction Chart
            const transactionCtx = document.getElementById('transactionChart').getContext('2d');
            charts.transactionChart = new Chart(transactionCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Transactions',
                        data: [120, 150, 180, 90, 200, 160],
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderWidth: 2,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Income vs Expense Chart
            const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
            charts.incomeExpenseChart = new Chart(incomeExpenseCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Income', 'Expenses'],
                    datasets: [{
                        data: [75, 25],
                        backgroundColor: ['#2ecc71', '#e74c3c'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // Load dashboard stats
        function loadDashboardStats() {
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_dashboard_stats'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateDashboardStats(data);
                }
            })
            .catch(error => {
                console.error('Error loading dashboard stats:', error);
            });
        }

        // Update dashboard stats
        function updateDashboardStats(stats) {
            document.getElementById('total-customers').textContent = stats.total_customers;
            document.getElementById('total-accounts').textContent = stats.total_accounts;
            document.getElementById('total-balance').textContent = '৳ ' + stats.total_balance.toLocaleString('en-US', {minimumFractionDigits: 2});
            document.getElementById('pending-loans').textContent = stats.pending_loans;
            document.getElementById('total-employees').textContent = stats.total_employees;
            document.getElementById('pending-registrations').textContent = stats.pending_registrations;

            // Update recent transactions
            const tbody = document.getElementById('recent-transactions-body');
            tbody.innerHTML = '';
            stats.recent_transactions.forEach(transaction => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>#${transaction.transaction_id}</td>
                    <td>${transaction.first_name} ${transaction.last_name}</td>
                    <td>${transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1)}</td>
                    <td>৳ ${transaction.amount.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                    <td>${new Date(transaction.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</td>
                    <td><span class="status status-active">${transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)}</span></td>
                `;
                tbody.appendChild(row);
            });
        }

        // Customer Management Functions
        function showAddCustomerModal() {
            showModal('addCustomerModal');
        }

        function handleAddCustomer(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add_customer');

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Customer added successfully!');
                    closeModal('addCustomerModal');
                    this.reset();
                    refreshCustomers();
                    loadDashboardStats();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding customer');
            });
        }

        function editCustomer(customerId) {
            // Fetch customer data and populate edit form
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_customer_details&customer_id=${customerId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data) {
                    document.getElementById('edit_customer_id').value = customerId;
                    document.getElementById('edit_first_name').value = data.first_name;
                    document.getElementById('edit_last_name').value = data.last_name;
                    document.getElementById('edit_email').value = data.email;
                    document.getElementById('edit_phone').value = data.phone;
                    document.getElementById('edit_address').value = data.address;
                    document.getElementById('edit_city').value = data.city;
                    document.getElementById('edit_nid_number').value = data.nid_number;
                    document.getElementById('edit_date_of_birth').value = data.date_of_birth;
                    document.getElementById('edit_status').value = data.status;
                    showModal('editCustomerModal');
                }
            });
        }

        function handleEditCustomer(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update_customer');

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Customer updated successfully!');
                    closeModal('editCustomerModal');
                    refreshCustomers();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating customer');
            });
        }

        function deleteCustomer(customerId) {
            if (confirm('Are you sure you want to delete this customer?')) {
                fetch('admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_customer&customer_id=${customerId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Customer deleted successfully!');
                        refreshCustomers();
                        loadDashboardStats();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting customer');
                });
            }
        }

        function viewCustomer(customerId) {
            // Implement customer view functionality
            alert('View customer: ' + customerId);
        }

        function refreshCustomers() {
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_customers'
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('customers-body');
                tbody.innerHTML = '';
                data.forEach(customer => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>#CUS-${customer.id}</td>
                        <td>${customer.first_name} ${customer.last_name}</td>
                        <td>${customer.email}</td>
                        <td>${customer.phone}</td>
                        <td>${customer.account_count}</td>
                        <td>
                            <span class="status status-${customer.status === 'active' ? 'active' : 'pending'}">
                                ${customer.status.charAt(0).toUpperCase() + customer.status.slice(1)}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-primary" onclick="viewCustomer(${customer.id})">View</button>
                                <button class="btn btn-success" onclick="editCustomer(${customer.id})">Edit</button>
                                <button class="btn btn-danger" onclick="deleteCustomer(${customer.id})">Delete</button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            });
        }

        // Account Management Functions
        function showCreateAccountModal() {
            showModal('createAccountModal');
        }

        function handleCreateAccount(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'create_account');

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Account created successfully!');
                    closeModal('createAccountModal');
                    this.reset();
                    refreshAccounts();
                    loadDashboardStats();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating account');
            });
        }

        function viewAccount(accountId) {
            // Implement account view functionality
            alert('View account: ' + accountId);
        }

        function toggleAccountStatus(accountId, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'frozen' : 'active';
            if (confirm(`Are you sure you want to ${newStatus === 'frozen' ? 'freeze' : 'unfreeze'} this account?`)) {
                fetch('admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update_account_status&account_id=${accountId}&status=${newStatus}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Account ${newStatus === 'frozen' ? 'frozen' : 'unfrozen'} successfully!`);
                        refreshAccounts();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating account status');
                });
            }
        }

        function refreshAccounts() {
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_accounts'
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('accounts-body');
                tbody.innerHTML = '';
                data.forEach(account => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${account.account_number}</td>
                        <td>${account.first_name} ${account.last_name}</td>
                        <td>${account.account_type}</td>
                        <td>৳ ${parseFloat(account.balance).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        <td>
                            <span class="status status-${account.status === 'active' ? 'active' : (account.status === 'frozen' ? 'suspended' : 'pending')}">
                                ${account.status.charAt(0).toUpperCase() + account.status.slice(1)}
                            </span>
                        </td>
                        <td>${new Date(account.opened_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-primary" onclick="viewAccount(${account.id})">View</button>
                                <button class="btn btn-warning" onclick="toggleAccountStatus(${account.id}, '${account.status}')">
                                    ${account.status === 'frozen' ? 'Unfreeze' : 'Freeze'}
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            });
        }

        // Transaction Management Functions
        function showAddTransactionModal() {
            showModal('addTransactionModal');
        }

        function handleAddTransaction(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add_transaction');

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Transaction processed successfully!');
                    closeModal('addTransactionModal');
                    this.reset();
                    refreshTransactions();
                    loadDashboardStats();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error processing transaction');
            });
        }

        function applyTransactionFilters() {
            const type = document.getElementById('filter-type').value;
            const dateFrom = document.getElementById('filter-date-from').value;
            const dateTo = document.getElementById('filter-date-to').value;

            let formData = new FormData();
            formData.append('action', 'get_transactions');
            if (type) formData.append('type', type);
            if (dateFrom) formData.append('date_from', dateFrom);
            if (dateTo) formData.append('date_to', dateTo);

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('transactions-body');
                tbody.innerHTML = '';
                data.forEach(transaction => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>#${transaction.transaction_id}</td>
                        <td>${transaction.account_number}</td>
                        <td>${transaction.first_name} ${transaction.last_name}</td>
                        <td>${transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1)}</td>
                        <td>৳ ${transaction.amount.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        <td>${new Date(transaction.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'})}</td>
                        <td><span class="status status-active">${transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)}</span></td>
                    `;
                    tbody.appendChild(row);
                });
            });
        }

        function refreshTransactions() {
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_transactions'
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('transactions-body');
                tbody.innerHTML = '';
                data.forEach(transaction => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>#${transaction.transaction_id}</td>
                        <td>${transaction.account_number}</td>
                        <td>${transaction.first_name} ${transaction.last_name}</td>
                        <td>${transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1)}</td>
                        <td>৳ ${transaction.amount.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        <td>${new Date(transaction.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'})}</td>
                        <td><span class="status status-active">${transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)}</span></td>
                    `;
                    tbody.appendChild(row);
                });
            });
        }

        // Loan Management Functions
        function showAddLoanModal() {
            showModal('addLoanModal');
        }

        function handleAddLoan(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add_loan');

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Loan application submitted successfully!');
                    closeModal('addLoanModal');
                    this.reset();
                    refreshLoans();
                    loadDashboardStats();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error submitting loan application');
            });
        }

        function approveLoan(loanId) {
            if (confirm('Are you sure you want to approve this loan?')) {
                fetch('admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=approve_loan&loan_id=${loanId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Loan approved successfully!');
                        refreshLoans();
                        loadDashboardStats();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error approving loan');
                });
            }
        }

        function rejectLoan(loanId) {
            if (confirm('Are you sure you want to reject this loan?')) {
                fetch('admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=reject_loan&loan_id=${loanId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Loan rejected successfully!');
                        refreshLoans();
                        loadDashboardStats();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error rejecting loan');
                });
            }
        }

        function approveSelectedLoans() {
            const selectedLoans = Array.from(document.querySelectorAll('.loan-checkbox:checked')).map(cb => cb.value);
            if (selectedLoans.length === 0) {
                alert('Please select at least one loan to approve.');
                return;
            }

            if (confirm(`Are you sure you want to approve ${selectedLoans.length} loan(s)?`)) {
                selectedLoans.forEach(loanId => {
                    fetch('admin.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=approve_loan&loan_id=${loanId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            console.error('Error approving loan:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                });

                setTimeout(() => {
                    alert('Selected loans approved successfully!');
                    refreshLoans();
                    loadDashboardStats();
                }, 1000);
            }
        }

        function rejectSelectedLoans() {
            const selectedLoans = Array.from(document.querySelectorAll('.loan-checkbox:checked')).map(cb => cb.value);
            if (selectedLoans.length === 0) {
                alert('Please select at least one loan to reject.');
                return;
            }

            if (confirm(`Are you sure you want to reject ${selectedLoans.length} loan(s)?`)) {
                selectedLoans.forEach(loanId => {
                    fetch('admin.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=reject_loan&loan_id=${loanId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            console.error('Error rejecting loan:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                });

                setTimeout(() => {
                    alert('Selected loans rejected successfully!');
                    refreshLoans();
                    loadDashboardStats();
                }, 1000);
            }
        }

        function viewLoan(loanId) {
            // Implement loan view functionality
            alert('View loan: ' + loanId);
        }

        function refreshLoans() {
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_loans'
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('loans-body');
                tbody.innerHTML = '';
                data.forEach(loan => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><input type="checkbox" class="loan-checkbox" value="${loan.id}" ${loan.status !== 'pending' ? 'disabled' : ''}></td>
                        <td>${loan.loan_number}</td>
                        <td>${loan.first_name} ${loan.last_name}</td>
                        <td>${loan.loan_type.charAt(0).toUpperCase() + loan.loan_type.slice(1)}</td>
                        <td>৳ ${loan.amount.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        <td>${loan.interest_rate}%</td>
                        <td>${loan.term_months} months</td>
                        <td>
                            <span class="status status-${loan.status === 'approved' ? 'active' : (loan.status === 'pending' ? 'pending' : 'suspended')}">
                                ${loan.status.charAt(0).toUpperCase() + loan.status.slice(1)}
                            </span>
                        </td>
                        <td>${new Date(loan.applied_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</td>
                        <td>
                            <div class="action-buttons">
                                ${loan.status === 'pending' ? `
                                <button class="btn btn-success" onclick="approveLoan(${loan.id})">Approve</button>
                                <button class="btn btn-danger" onclick="rejectLoan(${loan.id})">Reject</button>
                                ` : ''}
                                <button class="btn btn-primary" onclick="viewLoan(${loan.id})">View</button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            });
        }

        // Employee Management Functions
        function showAddEmployeeModal() {
            showModal('addEmployeeModal');
        }

        function handleAddEmployee(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add_employee');

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Employee added successfully!');
                    closeModal('addEmployeeModal');
                    this.reset();
                    refreshEmployees();
                    loadDashboardStats();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding employee');
            });
        }

        function editEmployee(employeeId) {
            // Fetch employee data and populate edit form
            // This would typically make an AJAX call to get employee details
            // For now, we'll just show the modal
            document.getElementById('edit_employee_id').value = employeeId;
            // Populate other fields with actual data from server
            showModal('editEmployeeModal');
        }

        function handleEditEmployee(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update_employee');

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Employee updated successfully!');
                    closeModal('editEmployeeModal');
                    refreshEmployees();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating employee');
            });
        }

        function deleteEmployee(employeeId) {
            if (confirm('Are you sure you want to delete this employee?')) {
                fetch('admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_employee&employee_id=${employeeId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Employee deleted successfully!');
                        refreshEmployees();
                        loadDashboardStats();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting employee');
                });
            }
        }

        function viewEmployee(employeeId) {
            // Implement employee view functionality
            alert('View employee: ' + employeeId);
        }

        function refreshEmployees() {
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_employees'
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('employees-body');
                tbody.innerHTML = '';
                data.forEach(employee => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>#EMP-${employee.id}</td>
                        <td>${employee.first_name} ${employee.last_name}</td>
                        <td>${employee.email}</td>
                        <td>${employee.phone}</td>
                        <td>${employee.role.charAt(0).toUpperCase() + employee.role.slice(1)}</td>
                        <td>
                            <span class="status status-${employee.status === 'active' ? 'active' : 'pending'}">
                                ${employee.status.charAt(0).toUpperCase() + employee.status.slice(1)}
                            </span>
                        </td>
                        <td>${new Date(employee.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-primary" onclick="viewEmployee(${employee.id})">View</button>
                                <button class="btn btn-success" onclick="editEmployee(${employee.id})">Edit</button>
                                <button class="btn btn-danger" onclick="deleteEmployee(${employee.id})">Delete</button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            });
        }

        // Branch Management Functions
        function showAddBranchModal() {
            showModal('addBranchModal');
        }

        function handleAddBranch(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add_branch');

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Branch added successfully!');
                    closeModal('addBranchModal');
                    this.reset();
                    refreshBranches();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding branch');
            });
        }

        function editBranch(branchId) {
            // Fetch branch data and populate edit form
            // This would typically make an AJAX call to get branch details
            document.getElementById('edit_branch_id').value = branchId;
            // Populate other fields with actual data from server
            showModal('editBranchModal');
        }

        function handleEditBranch(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update_branch');

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Branch updated successfully!');
                    closeModal('editBranchModal');
                    refreshBranches();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating branch');
            });
        }

        function deleteBranch(branchId) {
            if (confirm('Are you sure you want to delete this branch?')) {
                fetch('admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_branch&branch_id=${branchId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Branch deleted successfully!');
                        refreshBranches();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting branch');
                });
            }
        }

        function viewBranch(branchId) {
            // Implement branch view functionality
            alert('View branch: ' + branchId);
        }

        function refreshBranches() {
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_branches'
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('branches-body');
                tbody.innerHTML = '';
                data.forEach(branch => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>#BR-${branch.id}</td>
                        <td>${branch.name}</td>
                        <td>${branch.address}</td>
                        <td>${branch.phone}</td>
                        <td>${branch.email}</td>
                        <td>${branch.manager_name}</td>
                        <td>${branch.account_count}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-primary" onclick="viewBranch(${branch.id})">View</button>
                                <button class="btn btn-success" onclick="editBranch(${branch.id})">Edit</button>
                                <button class="btn btn-danger" onclick="deleteBranch(${branch.id})">Delete</button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            });
        }

        // Registration Management Functions
        function approveRegistration(registrationId) {
            if (confirm('Are you sure you want to approve this registration?')) {
                fetch('admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=approve_registration&registration_id=${registrationId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Registration approved successfully!');
                        refreshRegistrations();
                        loadDashboardStats();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error approving registration');
                });
            }
        }

        function rejectRegistration(registrationId) {
            document.getElementById('reject_registration_id').value = registrationId;
            showModal('rejectRegistrationModal');
        }

        function handleRejectRegistration(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'reject_registration');

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Registration rejected successfully!');
                    closeModal('rejectRegistrationModal');
                    this.reset();
                    refreshRegistrations();
                    loadDashboardStats();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error rejecting registration');
            });
        }

        function viewRegistration(registrationId) {
            // Implement registration view functionality
            alert('View registration: ' + registrationId);
        }

        function refreshRegistrations() {
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_pending_registrations'
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('registrations-body');
                tbody.innerHTML = '';
                data.forEach(registration => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>#REG-${registration.id}</td>
                        <td>${registration.first_name} ${registration.last_name}</td>
                        <td>${registration.email}</td>
                        <td>${registration.phone}</td>
                        <td>${registration.account_type_name}</td>
                        <td>${registration.branch_name}</td>
                        <td>৳ ${parseFloat(registration.initial_deposit).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        <td>${new Date(registration.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-success" onclick="approveRegistration(${registration.id})">Approve</button>
                                <button class="btn btn-danger" onclick="rejectRegistration(${registration.id})">Reject</button>
                                <button class="btn btn-primary" onclick="viewRegistration(${registration.id})">View</button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            });
        }

        // Activity Logs Functions
        function applyLogFilters() {
            const dateFrom = document.getElementById('log-date-from').value;
            const dateTo = document.getElementById('log-date-to').value;
            const userId = document.getElementById('log-user').value;

            let formData = new FormData();
            formData.append('action', 'get_system_logs');
            if (dateFrom) formData.append('date_from', dateFrom);
            if (dateTo) formData.append('date_to', dateTo);
            if (userId) formData.append('user_id', userId);

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('activity-logs-body');
                tbody.innerHTML = '';
                data.forEach(log => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>#LOG-${log.id}</td>
                        <td>${log.first_name ? log.first_name + ' ' + log.last_name : 'System'}</td>
                        <td>${log.action}</td>
                        <td>${log.description}</td>
                        <td>${log.ip_address}</td>
                        <td>${new Date(log.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit'})}</td>
                    `;
                    tbody.appendChild(row);
                });
            });
        }

        function exportLogs() {
            // Implement export logs functionality
            alert('Exporting logs...');
        }

        function clearOldLogs() {
            if (confirm('Are you sure you want to clear logs older than 30 days?')) {
                // Implement clear old logs functionality
                alert('Old logs cleared successfully!');
                refreshLogs();
            }
        }

        function refreshLogs() {
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_system_logs'
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('activity-logs-body');
                tbody.innerHTML = '';
                data.forEach(log => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>#LOG-${log.id}</td>
                        <td>${log.first_name ? log.first_name + ' ' + log.last_name : 'System'}</td>
                        <td>${log.action}</td>
                        <td>${log.description}</td>
                        <td>${log.ip_address}</td>
                        <td>${new Date(log.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit'})}</td>
                    `;
                    tbody.appendChild(row);
                });
            });
        }

        // System Settings Functions
        function saveSystemSettings() {
            const settings = {};
            const inputs = document.querySelectorAll('#system-settings input, #system-settings select');
            
            inputs.forEach(input => {
                if (input.id.startsWith('setting_')) {
                    settings[input.id] = input.value;
                }
            });

            const formData = new FormData();
            formData.append('action', 'update_system_settings');
            
            for (const [key, value] of Object.entries(settings)) {
                formData.append(key, value);
            }

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('System settings saved successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving system settings');
            });
        }

        function resetSystemSettings() {
            if (confirm('Are you sure you want to reset all system settings to default values?')) {
                fetch('admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=reset_system_settings'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('System settings reset to defaults!');
                        refreshSystemSettings();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error resetting system settings');
                });
            }
        }

        function refreshSystemSettings() {
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_system_settings'
            })
            .then(response => response.json())
            .then(data => {
                // Update form fields with current settings
                for (const [key, value] of Object.entries(data)) {
                    const input = document.getElementById(`setting_${key}`);
                    if (input) {
                        input.value = value;
                    }
                }
            });
        }

        // Notification Functions
        function showNotifications() {
            showModal('notificationsModal');
        }

        function markAllNotificationsRead() {
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=mark_notification_read'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI to show all notifications as read
                    document.querySelectorAll('.notification-item').forEach(item => {
                        item.classList.remove('unread');
                        item.style.fontWeight = 'normal';
                    });
                    document.querySelector('.notification-badge').style.display = 'none';
                    closeModal('notificationsModal');
                }
            });
        }

        // Export Functions
        function exportReport() {
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=export_data&type=dashboard'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Create and download CSV file
                    downloadCSV(data.data, data.filename);
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        function exportCustomers() {
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=export_data&type=customers'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    downloadCSV(data.data, data.filename);
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        function exportTransactions() {
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=export_data&type=transactions'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    downloadCSV(data.data, data.filename);
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        function downloadCSV(data, filename) {
            if (!data || data.length === 0) {
                alert('No data to export');
                return;
            }

            const headers = Object.keys(data[0]);
            const csvContent = [
                headers.join(','),
                ...data.map(row => headers.map(header => {
                    const value = row[header];
                    return typeof value === 'string' && value.includes(',') ? `"${value}"` : value;
                }).join(','))
            ].join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>
                   