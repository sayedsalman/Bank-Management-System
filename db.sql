

-- Users table (for login system)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('admin', 'employee', 'customer') NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    nid_number VARCHAR(20),
    date_of_birth DATE,
    status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Branches table
CREATE TABLE branches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    manager_name VARCHAR(100),
    opening_hours TEXT,
    services TEXT,
    facilities TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Account types table
CREATE TABLE account_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    min_balance DECIMAL(15,2) DEFAULT 0,
    interest_rate DECIMAL(5,2) DEFAULT 0
);

-- Accounts table
CREATE TABLE accounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    account_number VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    account_type_id INT NOT NULL,
    branch_id INT NOT NULL,
    balance DECIMAL(15,2) DEFAULT 0,
    status ENUM('active', 'inactive', 'frozen', 'closed') DEFAULT 'active',
    opened_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (account_type_id) REFERENCES account_types(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id)
);

-- Transactions table
CREATE TABLE transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transaction_id VARCHAR(20) UNIQUE NOT NULL,
    account_id INT NOT NULL,
    type ENUM('deposit', 'withdrawal', 'transfer', 'payment') NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    description TEXT,
    recipient_account VARCHAR(20),
    recipient_name VARCHAR(100),
    status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id)
);

-- Loans table
CREATE TABLE loans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    loan_number VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    loan_type ENUM('home', 'car', 'personal', 'business') NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    interest_rate DECIMAL(5,2) NOT NULL,
    term_months INT NOT NULL,
    remaining_balance DECIMAL(15,2) NOT NULL,
    monthly_payment DECIMAL(15,2) NOT NULL,
    status ENUM('pending', 'approved', 'active', 'paid', 'defaulted') DEFAULT 'pending',
    applied_date DATE NOT NULL,
    approved_date DATE,
    next_payment_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Savings goals table
CREATE TABLE savings_goals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    target_amount DECIMAL(15,2) NOT NULL,
    current_amount DECIMAL(15,2) DEFAULT 0,
    target_date DATE NOT NULL,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Notifications table
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'danger') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert default data
INSERT INTO users (username, password, email, role, first_name, last_name, phone, address, city, nid_number, date_of_birth, status) VALUES
('salman', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@losersbank.com', 'admin', 'Sayed', 'Salman', '+880 17XX-XXXXXX', 'PCIU Branch, Chattogram', 'Chattogram', '1234567890123', '1990-01-01', 'active'),
('sayedm_salman', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sayed@example.com', 'customer', 'Sayed', 'Salman', '+880 1712-345678', '123 Main Street, Chattogram', 'Chattogram', '1234567890124', '1995-05-15', 'active'),
('rahim', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'rahim@losersbank.com', 'employee', 'Rahim', 'Sheikh', '+880 18XX-XXXXXX', 'Employee Quarters, Chattogram', 'Chattogram', '1234567890125', '1985-03-20', 'active');

INSERT INTO account_types (name, description, min_balance, interest_rate) VALUES
('Savings', 'Regular savings account', 1000, 4.5),
('Current', 'Current account for business transactions', 5000, 2.0),
('Fixed Deposit', 'Fixed deposit account with higher interest', 10000, 7.5),
('Student', 'Student account with zero balance requirement', 0, 3.0),
('Senior Citizen', 'Account for senior citizens with higher interest', 1000, 6.0);

INSERT INTO branches (name, address, phone, email, manager_name, opening_hours, services, facilities) VALUES
('Agrabad Main Branch', 'Agrabad Commercial Area, Chattogram 4100, Bangladesh', '+880 31-625841', 'agrabad@losersbank.com', 'Mr. Abdul Karim', 'Sunday-Thursday: 9:00 AM - 5:00 PM, Friday: 9:00 AM - 12:30 PM, 2:30 PM - 5:00 PM, Saturday: 9:00 AM - 2:00 PM', 'Personal Banking,Business Banking,Loan Services,Foreign Exchange,ATM Services,Online Banking,Credit Cards,Investment Services', 'ATM, Security Vault, Disabled Access, Customer Lounge, Business Center'),
('Khulsi Branch', 'Khulshi Residential Area, Chattogram 4225, Bangladesh', '+880 31-671234', 'khulsi@losersbank.com', 'Ms. Fatema Begum', 'Sunday-Thursday: 9:00 AM - 4:00 PM, Friday: 9:00 AM - 12:00 PM, 2:30 PM - 4:00 PM, Saturday: 9:00 AM - 1:00 PM', 'Personal Banking,Loan Services,ATM Services,Online Banking,Credit Cards,Savings Accounts', 'ATM, Disabled Access, Customer Lounge'),
('GEC Circle Branch', 'GEC Circle, Chattogram', '+880 31-743219', 'gec@losersbank.com', 'Mr. Rahim Uddin', 'Sunday-Thursday: 9:00 AM - 5:00 PM, Friday: 9:00 AM - 12:30 PM, 2:30 PM - 5:00 PM, Saturday: 9:00 AM - 2:00 PM', 'Personal Banking,Business Banking,Loan Services,Foreign Exchange,ATM Services', 'ATM, Security Vault, Customer Lounge');


-- Additional tables starting with 's' as requested

-- User registrations waiting for approval
CREATE TABLE s_pending_registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    nid_number VARCHAR(20) NOT NULL,
    date_of_birth DATE NOT NULL,
    branch_id INT NOT NULL,
    account_type_id INT NOT NULL,
    initial_deposit DECIMAL(15,2) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by INT NULL,
    approval_date TIMESTAMP NULL,
    rejection_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (account_type_id) REFERENCES account_types(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);

-- Support tickets system
CREATE TABLE s_support_tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_number VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
    assigned_to INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);

-- System logs
CREATE TABLE s_system_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Employee tasks
CREATE TABLE s_employee_tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    assigned_to INT NOT NULL,
    assigned_by INT NOT NULL,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    due_date DATE,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    FOREIGN KEY (assigned_by) REFERENCES users(id)
);

-- Audit trail
CREATE TABLE s_audit_trail (
    id INT PRIMARY KEY AUTO_INCREMENT,
    table_name VARCHAR(50) NOT NULL,
    record_id INT NOT NULL,
    action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    old_values JSON,
    new_values JSON,
    changed_by INT NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (changed_by) REFERENCES users(id)
);

-- Insert some sample pending registrations
INSERT INTO s_pending_registrations (username, password, email, first_name, last_name, phone, address, city, nid_number, date_of_birth, branch_id, account_type_id, initial_deposit) VALUES
('niloy', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'niloy@example.com', 'Niloy', 'Das', '+880 1711-223344', '123 Street, Dhaka', 'Dhaka', '1990123456789', '1990-05-15', 1, 1, 5000.00),
('nafis', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'nafis@example.com', 'Nafis', 'Khan', '+880 1811-334455', '456 Road, Chattogram', 'Chattogram', '1991123456789', '1991-08-20', 2, 2, 10000.00);


-- System Settings table
CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    category VARCHAR(50) DEFAULT 'general',
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description, category) VALUES
-- General Settings
('bank_name', 'LOSERS BANK', 'string', 'Official name of the bank', 'general'),
('bank_currency', 'BDT', 'string', 'Default currency for the bank', 'general'),
('bank_timezone', 'Asia/Dhaka', 'string', 'Default timezone', 'general'),
('bank_contact_email', 'info@losersbank.com', 'string', 'Primary contact email', 'general'),
('bank_contact_phone', '+880 31-XXXXXXX', 'string', 'Primary contact phone', 'general'),

-- Account Settings
('min_account_balance', '500', 'number', 'Minimum required account balance', 'accounts'),
('max_daily_withdrawal', '50000', 'number', 'Maximum daily withdrawal limit', 'accounts'),
('max_daily_transfer', '100000', 'number', 'Maximum daily transfer limit', 'accounts'),
('account_opening_fee', '100', 'number', 'Fee for opening new account', 'accounts'),
('monthly_maintenance_fee', '50', 'number', 'Monthly account maintenance fee', 'accounts'),

-- Interest Rates
('savings_interest_rate', '4.5', 'number', 'Default savings account interest rate', 'interest'),
('current_interest_rate', '2.0', 'number', 'Default current account interest rate', 'interest'),
('fd_interest_rate', '7.5', 'number', 'Default fixed deposit interest rate', 'interest'),
('student_interest_rate', '3.0', 'number', 'Default student account interest rate', 'interest'),
('senior_interest_rate', '6.0', 'number', 'Default senior citizen interest rate', 'interest'),

-- Loan Settings
('personal_loan_interest', '12.5', 'number', 'Personal loan interest rate', 'loans'),
('home_loan_interest', '8.5', 'number', 'Home loan interest rate', 'loans'),
('car_loan_interest', '9.5', 'number', 'Car loan interest rate', 'loans'),
('business_loan_interest', '11.0', 'number', 'Business loan interest rate', 'loans'),
('min_loan_amount', '5000', 'number', 'Minimum loan amount', 'loans'),
('max_loan_amount', '5000000', 'number', 'Maximum loan amount', 'loans'),

-- Transaction Settings
('transaction_fee_percentage', '0.5', 'number', 'Percentage fee for transactions', 'transactions'),
('min_transaction_fee', '10', 'number', 'Minimum transaction fee', 'transactions'),
('max_transaction_fee', '500', 'number', 'Maximum transaction fee', 'transactions'),
('interbank_transfer_fee', '25', 'number', 'Fixed fee for interbank transfers', 'transactions'),

-- Security Settings
('session_timeout', '30', 'number', 'Session timeout in minutes', 'security'),
('max_login_attempts', '5', 'number', 'Maximum allowed login attempts', 'security'),
('password_expiry_days', '90', 'number', 'Password expiry in days', 'security'),
('enable_2fa', '1', 'boolean', 'Enable two-factor authentication', 'security'),
('enable_login_alerts', '1', 'boolean', 'Enable login notification alerts', 'security'),
('auto_logout_enabled', '1', 'boolean', 'Enable automatic logout', 'security'),

-- Notification Settings
('email_notifications', '1', 'boolean', 'Enable email notifications', 'notifications'),
('sms_notifications', '1', 'boolean', 'Enable SMS notifications', 'notifications'),
('push_notifications', '1', 'boolean', 'Enable push notifications', 'notifications'),
('low_balance_alert', '1000', 'number', 'Low balance alert threshold', 'notifications'),
('large_transaction_alert', '50000', 'number', 'Large transaction alert threshold', 'notifications'),

-- System Maintenance
('maintenance_mode', '0', 'boolean', 'Enable maintenance mode', 'maintenance'),
('maintenance_message', 'System is under maintenance. We will be back shortly.', 'string', 'Maintenance mode message', 'maintenance'),
('api_rate_limit', '100', 'number', 'API rate limit per minute', 'maintenance'),
('backup_frequency', 'daily', 'string', 'Automatic backup frequency', 'maintenance'),

-- UI/UX Settings
('theme_mode', 'light', 'string', 'Default theme mode (light/dark)', 'ui'),
('results_per_page', '25', 'number', 'Default number of results per page', 'ui'),
('auto_refresh_interval', '30', 'number', 'Auto-refresh interval in seconds', 'ui');