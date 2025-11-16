

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
                
                <div class="menu-section">Administration</div>
                <li><a class="menu-link" data-section="employees"><i class="fas fa-user-tie"></i> <span>Employees</span></a></li>
                
                <div class="menu-section">Security</div>
                <li><a class="menu-link" data-section="activity-logs"><i class="fas fa-clipboard-list"></i> <span>Activity Logs</span></a></li>
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
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">7</span>
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
                            <button class="btn btn-success"><i class="fas fa-download"></i> Export Report</button>
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
                            <button class="btn btn-success"><i class="fas fa-file-export"></i> Export</button>
                            <button class="btn btn-danger"><i class="fas fa-filter"></i> Filter</button>
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
                                            <button class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;" onclick="viewCustomer(<?php echo $customer['id']; ?>)">View</button>
                                            <button class="btn btn-success" style="padding: 5px 10px; font-size: 12px;" onclick="editCustomer(<?php echo $customer['id']; ?>)">Edit</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

         
                <div class="section" id="accounts">
                    <div class="section-header">
                        <div class="section-title">Account Management</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="showCreateAccountModal()"><i class="fas fa-plus"></i> Create Account</button>
                            <button class="btn btn-success" onclick="refreshAccounts()"><i class="fas fa-sync-alt"></i> Refresh</button>
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
                                            <button class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;" onclick="viewAccount(<?php echo $account['id']; ?>)">View</button>
                                            <button class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="freezeAccount(<?php echo $account['id']; ?>)">
                                                <?php echo $account['status'] == 'frozen' ? 'Unfreeze' : 'Freeze'; ?>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="section" id="transactions">
                    <div class="section-header">
                        <div class="section-title">Transactions Management</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="showAddTransactionModal()"><i class="fas fa-plus"></i> New Transaction</button>
                            <button class="btn btn-success"><i class="fas fa-file-export"></i> Export</button>
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
                                            <?php if ($loan['status'] == 'pending'): ?>
                                            <button class="btn btn-success" style="padding: 5px 10px; font-size: 12px;" onclick="approveLoan(<?php echo $loan['id']; ?>)">Approve</button>
                                            <?php endif; ?>
                                            <button class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;" onclick="viewLoan(<?php echo $loan['id']; ?>)">View</button>
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
                            <button class="btn btn-success"><i class="fas fa-sync-alt"></i> Refresh</button>
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
                                            <button class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">View</button>
                                            <button class="btn btn-success" style="padding: 5px 10px; font-size: 12px;">Edit</button>
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
                            <button class="btn btn-primary"><i class="fas fa-download"></i> Export Logs</button>
                            <button class="btn btn-danger"><i class="fas fa-trash"></i> Clear Old Logs</button>
                        </div>
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
                                    $logs = $pdo->query("SELECT sl.*, u.first_name, u.last_name 
                                                       FROM s_system_logs sl 
                                                       LEFT JOIN users u ON sl.user_id = u.id 
                                                       ORDER BY sl.created_at DESC LIMIT 50")->fetchAll();
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
                        <?php foreach ($branches as $branch): ?>
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
                        <?php foreach ($branches as $branch): ?>
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

    <script>
        // Theme Toggle
        const themeToggle = document.getElementById('themeToggle');
        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const icon = themeToggle.querySelector('i');
            if (document.body.classList.contains('dark-mode')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });

        // Mobile Menu Toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const sidebar = document.getElementById('sidebar');
        
        mobileMenuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });

        // Section Navigation
        const menuLinks = document.querySelectorAll('.menu-link');
        const sections = document.querySelectorAll('.section');
        const pageTitle = document.getElementById('pageTitle');

        function showSection(sectionId) {
            // Update active menu item
            menuLinks.forEach(item => item.classList.remove('active'));
            document.querySelector(`[data-section="${sectionId}"]`).classList.add('active');
            
            // Show target section
            sections.forEach(section => {
                section.classList.remove('active');
                if (section.id === sectionId) {
                    section.classList.add('active');
                }
            });
            
            // Update page title
            const menuText = document.querySelector(`[data-section="${sectionId}"] span`).textContent;
            pageTitle.textContent = menuText;
            
            // Close sidebar on mobile after selection
            if (window.innerWidth <= 1200) {
                sidebar.classList.remove('active');
            }
        }

        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetSection = this.getAttribute('data-section');
                showSection(targetSection);
            });
        });

        // Modal Functions
        function showAddCustomerModal() {
            document.getElementById('addCustomerModal').style.display = 'block';
        }

        function showAddTransactionModal() {
            document.getElementById('addTransactionModal').style.display = 'block';
        }

        function showAddLoanModal() {
            document.getElementById('addLoanModal').style.display = 'block';
        }

        function showCreateAccountModal() {
            document.getElementById('createAccountModal').style.display = 'block';
        }

        function showAddEmployeeModal() {
            document.getElementById('addEmployeeModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.getElementsByClassName('modal');
            for (let modal of modals) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        }

        // Form Submissions
        document.getElementById('addCustomerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add_customer');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal('addCustomerModal');
                    this.reset();
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        });

        document.getElementById('addTransactionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add_transaction');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal('addTransactionModal');
                    this.reset();
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        });

        document.getElementById('addLoanForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add_loan');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal('addLoanModal');
                    this.reset();
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        });

        document.getElementById('createAccountForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'create_account');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal('createAccountModal');
                    this.reset();
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        });

        document.getElementById('addEmployeeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add_employee');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal('addEmployeeModal');
                    this.reset();
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        });

        // Other Functions
        function refreshDashboard() {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_dashboard_stats'
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('total-customers').textContent = data.total_customers;
                document.getElementById('total-accounts').textContent = data.total_accounts;
                document.getElementById('total-balance').textContent = '৳ ' + data.total_balance.toLocaleString('en-US', {minimumFractionDigits: 2});
                document.getElementById('pending-loans').textContent = data.pending_loans;
                
                // Update recent transactions
                const tbody = document.getElementById('recent-transactions-body');
                tbody.innerHTML = '';
                data.recent_transactions.forEach(transaction => {
                    const row = `<tr>
                        <td>#${transaction.transaction_id}</td>
                        <td>${transaction.first_name} ${transaction.last_name}</td>
                        <td>${transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1)}</td>
                        <td>৳ ${parseFloat(transaction.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        <td>${new Date(transaction.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</td>
                        <td><span class="status status-active">${transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)}</span></td>
                    </tr>`;
                    tbody.innerHTML += row;
                });
            });
        }

        document.getElementById('refreshDashboard').addEventListener('click', refreshDashboard);

        function applyTransactionFilters() {
            const type = document.getElementById('filter-type').value;
            const dateFrom = document.getElementById('filter-date-from').value;
            const dateTo = document.getElementById('filter-date-to').value;
            const amount = document.getElementById('filter-amount').value;
            
            const formData = new FormData();
            formData.append('action', 'get_transactions');
            if (type) formData.append('type', type);
            if (dateFrom) formData.append('date_from', dateFrom);
            if (dateTo) formData.append('date_to', dateTo);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('transactions-body');
                tbody.innerHTML = '';
                data.forEach(transaction => {
                    const row = `<tr>
                        <td>#${transaction.transaction_id}</td>
                        <td>${transaction.account_number}</td>
                        <td>${transaction.first_name} ${transaction.last_name}</td>
                        <td>${transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1)}</td>
                        <td>৳ ${parseFloat(transaction.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        <td>${new Date(transaction.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'})}</td>
                        <td><span class="status status-active">${transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)}</span></td>
                    </tr>`;
                    tbody.innerHTML += row;
                });
            });
        }

        function freezeAccount(accountId) {
            if (confirm('Are you sure you want to change the account status?')) {
                const formData = new FormData();
                formData.append('action', 'update_account_status');
                formData.append('account_id', accountId);
                formData.append('status', 'frozen'); // You might want to toggle between frozen/active
                
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                });
            }
        }

        function approveLoan(loanId) {
            if (confirm('Are you sure you want to approve this loan?')) {
                const formData = new FormData();
                formData.append('action', 'approve_loan');
                formData.append('loan_id', loanId);
                
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
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
                    const formData = new FormData();
                    formData.append('action', 'approve_loan');
                    formData.append('loan_id', loanId);
                    
                    fetch('', {
                        method: 'POST',
                        body: formData
                    });
                });
                
                setTimeout(() => {
                    alert('Loans approved successfully!');
                    location.reload();
                }, 1000);
            }
        }

        // Select all loans checkbox
        document.getElementById('select-all-loans').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.loan-checkbox:not(:disabled)');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Transaction Chart
            const transactionCtx = document.getElementById('transactionChart').getContext('2d');
            const transactionChart = new Chart(transactionCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Deposits',
                        data: [12000, 19000, 15000, 25000, 22000, 30000],
                        borderColor: '#2ecc71',
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
                        tension: 0.3,
                        fill: true
                    }, {
                        label: 'Withdrawals',
                        data: [10000, 15000, 12000, 18000, 16000, 22000],
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '৳' + value/1000 + 'k';
                                }
                            }
                        }
                    }
                }
            });

            // Income vs Expense Chart
            const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
            const incomeExpenseChart = new Chart(incomeExpenseCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Income', 'Expenses'],
                    datasets: [{
                        data: [65, 35],
                        backgroundColor: [
                            '#2ecc71',
                            '#e74c3c'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 1200 && 
                !sidebar.contains(e.target) && 
                !mobileMenuToggle.contains(e.target) && 
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });

        // Placeholder functions for future implementation
        function viewCustomer(customerId) {
            alert('View customer details for ID: ' + customerId);
            // Implement customer details view
        }

        function editCustomer(customerId) {
            alert('Edit customer with ID: ' + customerId);
            // Implement customer editing
        }

        function viewAccount(accountId) {
            alert('View account details for ID: ' + accountId);
            // Implement account details view
        }

        function viewLoan(loanId) {
            alert('View loan details for ID: ' + loanId);
            // Implement loan details view
        }

        function refreshAccounts() {
            location.reload();
        }
    </script>
</body>
</html>