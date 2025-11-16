<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOSERS BANK - Employee Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* All original CSS styles from employee.php */
        :root {
            --primary: #2980b9;
            --secondary: #3498db;
            --accent: #1abc9c;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --info: #9b59b6;
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

        .employee-info {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .employee-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--info));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 20px;
            margin: 0 auto 10px;
            border: 3px solid rgba(255, 255, 255, 0.2);
        }

        .employee-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .employee-role {
            font-size: 12px;
            opacity: 0.8;
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

        .btn-info {
            background-color: var(--info);
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
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

        .transactions .stat-icon {
            background-color: var(--info);
        }

        .loans .stat-icon {
            background-color: var(--warning);
        }

        .accounts .stat-icon {
            background-color: var(--success);
        }

        .alerts .stat-icon {
            background-color: var(--danger);
        }

        .attendance .stat-icon {
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

        .status-completed {
            background-color: rgba(46, 204, 113, 0.2);
            color: var(--success);
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

        /* Grid Layouts */
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        /* Card Styles */
        .card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            transition: var(--transition);
        }

        body.dark-mode .card {
            background-color: var(--primary);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        body.dark-mode .card-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary);
        }

        body.dark-mode .card-title {
            color: white;
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

        /* Utility Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mb-20 { margin-bottom: 20px; }
        .mt-20 { margin-top: 20px; }
        .p-20 { padding: 20px; }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: white;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 500px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        body.dark-mode .modal-content {
            background: var(--primary);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        body.dark-mode .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        
        body.dark-mode .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--dark);
        }
        
        body.dark-mode .close-modal {
            color: white;
        }
        
        .success-message, .error-message {
            padding: 15px;
            margin: 15px 0;
            border-radius: var(--border-radius);
            font-weight: 500;
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
            
            .grid-2, .grid-3 {
                grid-template-columns: 1fr;
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
            
            .section-actions {
                flex-direction: column;
                width: 100%;
            }
            
            .section-actions .btn {
                width: 100%;
                justify-content: center;
            }
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
            
            <div class="employee-info">
                <div class="employee-avatar"><?php echo substr($_SESSION['first_name'], 0, 1) . substr($_SESSION['last_name'], 0, 1); ?></div>
                <div class="employee-name"><?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></div>
                <div class="employee-role">Senior Banking Officer</div>
            </div>
            
            <ul class="sidebar-menu">
                <div class="menu-section">Main</div>
                <li><a class="menu-link active" data-section="dashboard"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                
                <div class="menu-section">Customer Management</div>
                <li><a class="menu-link" data-section="registrations"><i class="fas fa-user-plus"></i> <span>Registrations</span> <span class="menu-badge"><?php echo $stats['pending_registrations']; ?></span></a></li>
                <li><a class="menu-link" data-section="customers"><i class="fas fa-users"></i> <span>Customers</span></a></li>
                <li><a class="menu-link" data-section="accounts"><i class="fas fa-university"></i> <span>Accounts</span></a></li>
                
                <div class="menu-section">Transactions & Loans</div>
                <li><a class="menu-link" data-section="transactions"><i class="fas fa-exchange-alt"></i> <span>Transactions</span></a></li>
                <li><a class="menu-link" data-section="loans"><i class="fas fa-hand-holding-usd"></i> <span>Loans</span> <span class="menu-badge"><?php echo $stats['pending_loans']; ?></span></a></li>
                <li><a class="menu-link" data-section="deposits"><i class="fas fa-piggy-bank"></i> <span>Deposits</span></a></li>
                
                <div class="menu-section">Reports & Tools</div>
                <li><a class="menu-link" data-section="reports"><i class="fas fa-chart-bar"></i> <span>Reports</span></a></li>
                <li><a class="menu-link" data-section="tasks"><i class="fas fa-tasks"></i> <span>Tasks</span> <span class="menu-badge"><?php echo $stats['pending_tasks']; ?></span></a></li>
                
                <div class="menu-section">Communication</div>
                <li><a class="menu-link" data-section="communication"><i class="fas fa-comments"></i> <span>Messages</span></a></li>
                
                <div class="menu-section">Settings</div>
                <li><a class="menu-link" data-section="profile"><i class="fas fa-user-cog"></i> <span>Profile</span></a></li>
                <li><a class="menu-link" data-section="security"><i class="fas fa-shield-alt"></i> <span>Security</span></a></li>
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
                    <h2 id="pageTitle">Employee Dashboard</h2>
                </div>
                <div class="header-right">
                    <div class="theme-toggle" id="themeToggle">
                        <i class="fas fa-moon"></i>
                    </div>
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge"><?php echo $stats['pending_registrations'] + $stats['pending_loans'] + $stats['pending_tasks']; ?></span>
                    </div>
                    <div class="user-profile">
                        <div class="profile-pic"><?php echo substr($_SESSION['first_name'], 0, 1) . substr($_SESSION['last_name'], 0, 1); ?></div>
                        <div class="user-name"><?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content">
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

                <!-- Dashboard Section -->
                <div class="section active" id="dashboard">
                    <div class="section-header">
                        <div class="section-title">Dashboard Overview</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="refreshData()"><i class="fas fa-sync-alt"></i> Refresh Data</button>
                            <button class="btn btn-success" onclick="exportReport()"><i class="fas fa-download"></i> Export Report</button>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="stats-cards">
                        <div class="stat-card customers">
                            <div class="stat-header">
                                <div class="stat-title">Assigned Customers</div>
                                <div class="stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="stat-value"><?php echo $stats['assigned_customers']; ?></div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 12% from last month
                            </div>
                        </div>
                        
                        <div class="stat-card transactions">
                            <div class="stat-header">
                                <div class="stat-title">Today's Transactions</div>
                                <div class="stat-icon">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                            </div>
                            <div class="stat-value">BDT <?php echo number_format($stats['total_transactions_today'], 2); ?></div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 8.5% from yesterday
                            </div>
                        </div>
                        
                        <div class="stat-card loans">
                            <div class="stat-header">
                                <div class="stat-title">Pending Loans</div>
                                <div class="stat-icon">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                            </div>
                            <div class="stat-value"><?php echo $stats['pending_loans']; ?></div>
                            <div class="stat-change negative">
                                <i class="fas fa-arrow-up"></i> 3 new applications
                            </div>
                        </div>
                        
                        <div class="stat-card accounts">
                            <div class="stat-header">
                                <div class="stat-title">Pending Registrations</div>
                                <div class="stat-icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                            </div>
                            <div class="stat-value"><?php echo $stats['pending_registrations']; ?></div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-down"></i> 2 approved today
                            </div>
                        </div>
                        
                        <div class="stat-card alerts">
                            <div class="stat-header">
                                <div class="stat-title">Pending Tasks</div>
                                <div class="stat-icon">
                                    <i class="fas fa-tasks"></i>
                                </div>
                            </div>
                            <div class="stat-value"><?php echo $stats['pending_tasks']; ?></div>
                            <div class="stat-change negative">
                                <i class="fas fa-arrow-up"></i> 1 new task
                            </div>
                        </div>
                        
                        <div class="stat-card attendance">
                            <div class="stat-header">
                                <div class="stat-title">Total Balance</div>
                                <div class="stat-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                            <div class="stat-value">BDT <?php echo number_format($stats['total_balance'], 2); ?></div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 5% growth
                            </div>
                        </div>
                    </div>

                    <!-- Charts & Upcoming Tasks -->
                    <div class="charts-row">
                        <div class="chart-container">
                            <div class="chart-header">
                                <div class="chart-title">Daily Transaction Trend</div>
                                <div class="chart-actions">
                                    <select id="chartPeriod">
                                        <option>Last 7 Days</option>
                                        <option>Last 30 Days</option>
                                        <option>This Month</option>
                                    </select>
                                </div>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="transactionChart"></canvas>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Upcoming Tasks</div>
                                <button class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;" onclick="viewAllTasks()">View All</button>
                            </div>
                            <div class="card-content">
                                <?php foreach(array_slice($employee_tasks, 0, 3) as $task): ?>
                                <div class="mb-20">
                                    <div><strong><?php echo date('h:i A', strtotime($task['due_date'])); ?></strong> - <?php echo $task['title']; ?></div>
                                    <div style="font-size: 12px; color: var(--dark);">Priority: <?php echo ucfirst($task['priority']); ?> • Status: <?php echo ucfirst($task['status']); ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="table-container">
                        <div class="chart-header">
                            <div class="chart-title">Recent Transactions</div>
                            <button class="btn btn-primary" onclick="viewAllTransactions()">View All</button>
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
                                <tbody>
                                    <?php foreach ($recent_transactions as $transaction): ?>
                                    <tr>
                                        <td><?php echo $transaction['transaction_id']; ?></td>
                                        <td><?php echo $transaction['first_name'] . ' ' . $transaction['last_name']; ?></td>
                                        <td><?php echo ucfirst($transaction['type']); ?></td>
                                        <td>BDT <?php echo number_format($transaction['amount'], 2); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($transaction['transaction_date'])); ?></td>
                                        <td><span class="status status-<?php echo $transaction['status'] === 'completed' ? 'completed' : 'pending'; ?>"><?php echo ucfirst($transaction['status']); ?></span></td>
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
                            <button class="btn btn-success" onclick="exportRegistrations()"><i class="fas fa-file-export"></i> Export</button>
                        </div>
                    </div>

                    <div class="table-container">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Account Type</th>
                                        <th>Initial Deposit</th>
                                        <th>Applied Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pending_registrations as $registration): ?>
                                    <tr>
                                        <td>#REG-<?php echo str_pad($registration['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                        <td><?php echo $registration['first_name'] . ' ' . $registration['last_name']; ?></td>
                                        <td><?php echo $registration['username']; ?></td>
                                        <td><?php echo $registration['email']; ?></td>
                                        <td><?php echo $registration['phone']; ?></td>
                                        <td><?php echo $registration['account_type_name']; ?></td>
                                        <td>BDT <?php echo number_format($registration['initial_deposit'], 2); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($registration['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-success" onclick="approveRegistration(<?php echo $registration['id']; ?>)">Approve</button>
                                            <button class="btn btn-danger" onclick="rejectRegistration(<?php echo $registration['id']; ?>)">Reject</button>
                                            <button class="btn btn-primary" onclick="viewRegistration(<?php echo $registration['id']; ?>)">Details</button>
                                        </td>
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
                        </div>
                    </div>

                    <div class="form-container mb-20">
                        <div class="form-group">
                            <label class="form-label">Search Customers</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="text" id="customerSearch" class="form-control" placeholder="Search by name, email, or phone">
                                <button class="btn btn-primary" onclick="searchCustomers()">Search</button>
                            </div>
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
                                <tbody>
                                    <?php foreach ($all_customers as $customer): ?>
                                    <tr>
                                        <td>#CUS-<?php echo str_pad($customer['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                        <td><?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?></td>
                                        <td><?php echo $customer['email']; ?></td>
                                        <td><?php echo $customer['phone']; ?></td>
                                        <td><?php echo $customer['account_count']; ?></td>
                                        <td><span class="status status-<?php echo $customer['status']; ?>"><?php echo ucfirst($customer['status']); ?></span></td>
                                        <td>
                                            <button class="btn btn-primary" onclick="viewCustomer(<?php echo $customer['id']; ?>)">View</button>
                                            <button class="btn btn-success" onclick="editCustomer(<?php echo $customer['id']; ?>)">Edit</button>
                                            <?php if($customer['status'] == 'active'): ?>
                                            <button class="btn btn-warning" onclick="updateCustomerStatus(<?php echo $customer['id']; ?>, 'inactive')">Deactivate</button>
                                            <?php else: ?>
                                            <button class="btn btn-success" onclick="updateCustomerStatus(<?php echo $customer['id']; ?>, 'active')">Activate</button>
                                            <?php endif; ?>
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
                            <button class="btn btn-primary" onclick="showCreateAccountModal()"><i class="fas fa-plus"></i> Open Account</button>
                            <button class="btn btn-success" onclick="refreshData()"><i class="fas fa-sync-alt"></i> Refresh</button>
                        </div>
                    </div>

                    <div class="grid-3">
                        <?php
                        $savings_count = $pdo->query("SELECT COUNT(*) as count FROM accounts a JOIN account_types at ON a.account_type_id = at.id WHERE at.name LIKE '%savings%' AND a.status = 'active'")->fetch()['count'];
                        $current_count = $pdo->query("SELECT COUNT(*) as count FROM accounts a JOIN account_types at ON a.account_type_id = at.id WHERE at.name LIKE '%current%' AND a.status = 'active'")->fetch()['count'];
                        $fixed_count = $pdo->query("SELECT COUNT(*) as count FROM accounts a JOIN account_types at ON a.account_type_id = at.id WHERE at.name LIKE '%fixed%' AND a.status = 'active'")->fetch()['count'];
                        ?>
                        <div class="card text-center">
                            <div class="card-header">
                                <div class="card-title">Savings Accounts</div>
                            </div>
                            <div class="stat-value"><?php echo $savings_count; ?></div>
                            <div class="stat-title">Active Accounts</div>
                        </div>
                        
                        <div class="card text-center">
                            <div class="card-header">
                                <div class="card-title">Current Accounts</div>
                            </div>
                            <div class="stat-value"><?php echo $current_count; ?></div>
                            <div class="stat-title">Active Accounts</div>
                        </div>
                        
                        <div class="card text-center">
                            <div class="card-header">
                                <div class="card-title">Fixed Deposits</div>
                            </div>
                            <div class="stat-value"><?php echo $fixed_count; ?></div>
                            <div class="stat-title">Active Deposits</div>
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
                                <tbody>
                                    <?php foreach ($all_accounts as $account): ?>
                                    <tr>
                                        <td><?php echo $account['account_number']; ?></td>
                                        <td><?php echo $account['first_name'] . ' ' . $account['last_name']; ?></td>
                                        <td><?php echo $account['account_type']; ?></td>
                                        <td>BDT <?php echo number_format($account['balance'], 2); ?></td>
                                        <td><span class="status status-<?php echo $account['status']; ?>"><?php echo ucfirst($account['status']); ?></span></td>
                                        <td><?php echo date('M j, Y', strtotime($account['opened_date'])); ?></td>
                                        <td>
                                            <button class="btn btn-primary" onclick="viewAccount(<?php echo $account['id']; ?>)">View</button>
                                            <?php if($account['status'] == 'active'): ?>
                                            <button class="btn btn-danger" onclick="updateAccountStatus(<?php echo $account['id']; ?>, 'frozen')">Freeze</button>
                                            <?php elseif($account['status'] == 'frozen'): ?>
                                            <button class="btn btn-success" onclick="updateAccountStatus(<?php echo $account['id']; ?>, 'active')">Unfreeze</button>
                                            <?php endif; ?>
                                            <button class="btn btn-warning" onclick="closeAccount(<?php echo $account['id']; ?>)">Close</button>
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
                        <div class="section-title">Transaction Management</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="showCreateTransactionModal()"><i class="fas fa-plus"></i> New Transaction</button>
                            <button class="btn btn-success" onclick="exportTransactions()"><i class="fas fa-file-export"></i> Export</button>
                        </div>
                    </div>
                    
                    <div class="grid-2">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Pending Large Transactions</div>
                            </div>
                            <div class="card-content">
                                <?php
                                $large_transactions = $pdo->query("
                                    SELECT t.*, a.account_number, u.first_name, u.last_name 
                                    FROM transactions t 
                                    JOIN accounts a ON t.account_id = a.id 
                                    JOIN users u ON a.user_id = u.id 
                                    WHERE t.amount > 100000 AND t.status = 'pending'
                                    ORDER BY t.amount DESC 
                                    LIMIT 2
                                ")->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach($large_transactions as $transaction):
                                ?>
                                <div class="mb-20">
                                    <div><strong>BDT <?php echo number_format($transaction['amount'], 2); ?></strong> - <?php echo ucfirst($transaction['type']); ?></div>
                                    <div style="font-size: 12px; color: var(--dark);"><?php echo $transaction['first_name'] . ' ' . $transaction['last_name']; ?> • <?php echo date('M j, Y g:i A', strtotime($transaction['created_at'])); ?></div>
                                    <div class="mt-20">
                                        <button class="btn btn-success" style="padding: 5px 10px; font-size: 12px;" onclick="approveTransaction(<?php echo $transaction['id']; ?>)">Approve</button>
                                        <button class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="rejectTransaction(<?php echo $transaction['id']; ?>)">Reject</button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Fraud Detection Alerts</div>
                            </div>
                            <div class="card-content">
                                <div class="mb-20">
                                    <div><strong>Unusual Activity</strong> - Multiple Failed Logins</div>
                                    <div style="font-size: 12px; color: var(--dark);">Account #7231 • Today, 09:20 AM</div>
                                    <div class="mt-20">
                                        <button class="btn btn-warning" style="padding: 5px 10px; font-size: 12px;">Investigate</button>
                                    </div>
                                </div>
                                <div>
                                    <div><strong>Suspicious Transfer</strong> - New Recipient</div>
                                    <div style="font-size: 12px; color: var(--dark);">Account #4880 • Today, 10:45 AM</div>
                                    <div class="mt-20">
                                        <button class="btn btn-warning" style="padding: 5px 10px; font-size: 12px;">Investigate</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-container">
                        <h3>Transaction Filters</h3>
                        <form id="transactionFilters" method="GET">
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
                                <div class="form-group">
                                    <label class="form-label">Transaction Type</label>
                                    <select class="form-control" name="type">
                                        <option value="">All Types</option>
                                        <option value="deposit">Deposit</option>
                                        <option value="withdrawal">Withdrawal</option>
                                        <option value="transfer">Transfer</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Date From</label>
                                    <input type="date" class="form-control" name="date_from">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Date To</label>
                                    <input type="date" class="form-control" name="date_to">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Amount Range</label>
                                    <select class="form-control" name="amount_range">
                                        <option value="">Any Amount</option>
                                        <option value="0-10000">Up to BDT 10,000</option>
                                        <option value="10000-50000">BDT 10,000 - BDT 50,000</option>
                                        <option value="50000+">Over BDT 50,000</option>
                                    </select>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" style="margin-top: 15px;" onclick="applyTransactionFilters()">Apply Filters</button>
                        </form>
                    </div>
                    
                    <div class="table-container">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Account</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($all_transactions as $transaction): ?>
                                    <tr>
                                        <td><?php echo $transaction['transaction_id']; ?></td>
                                        <td><?php echo $transaction['account_number']; ?></td>
                                        <td><?php echo ucfirst($transaction['type']); ?></td>
                                        <td>BDT <?php echo number_format($transaction['amount'], 2); ?></td>
                                        <td><?php echo $transaction['description']; ?></td>
                                        <td><?php echo date('M j, Y g:i A', strtotime($transaction['transaction_date'])); ?></td>
                                        <td><span class="status status-<?php echo $transaction['status']; ?>"><?php echo ucfirst($transaction['status']); ?></span></td>
                                        <td>
                                            <button class="btn btn-primary" onclick="viewTransaction(<?php echo $transaction['id']; ?>)">View</button>
                                            <?php if($transaction['status'] == 'pending'): ?>
                                            <button class="btn btn-success" onclick="approveTransaction(<?php echo $transaction['id']; ?>)">Approve</button>
                                            <button class="btn btn-danger" onclick="rejectTransaction(<?php echo $transaction['id']; ?>)">Reject</button>
                                            <?php endif; ?>
                                        </td>
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
                            <button class="btn btn-primary" onclick="showCreateLoanModal()"><i class="fas fa-plus"></i> New Loan</button>
                            <button class="btn btn-success" onclick="approveSelectedLoans()"><i class="fas fa-check"></i> Approve Selected</button>
                        </div>
                    </div>
                    
                    <div class="grid-2">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Loan Applications</div>
                            </div>
                            <div class="card-content">
                                <?php foreach(array_slice($pending_loans, 0, 2) as $loan): ?>
                                <div class="mb-20">
                                    <div><strong><?php echo ucfirst($loan['loan_type']); ?> Loan</strong> - BDT <?php echo number_format($loan['amount'], 2); ?></div>
                                    <div style="font-size: 12px; color: var(--dark);"><?php echo $loan['first_name'] . ' ' . $loan['last_name']; ?> • Applied: <?php echo date('M j, Y', strtotime($loan['applied_date'])); ?></div>
                                    <div class="mt-20">
                                        <button class="btn btn-success" style="padding: 5px 10px; font-size: 12px;" onclick="approveLoan(<?php echo $loan['id']; ?>)">Approve</button>
                                        <button class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="rejectLoan(<?php echo $loan['id']; ?>)">Reject</button>
                                        <button class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;" onclick="viewLoan(<?php echo $loan['id']; ?>)">Details</button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">EMI Calculator</div>
                            </div>
                            <div class="card-content">
                                <div class="form-group">
                                    <label class="form-label">Loan Amount (BDT)</label>
                                    <input type="number" class="form-control" id="loanAmount" value="1000000">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Interest Rate (%)</label>
                                    <input type="number" class="form-control" id="interestRate" value="8.5" step="0.1">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Loan Term (Years)</label>
                                    <input type="number" class="form-control" id="loanTerm" value="5">
                                </div>
                                <button class="btn btn-primary" onclick="calculateEMI()">Calculate EMI</button>
                                <div class="mt-20" id="emiResult">
                                    <!-- EMI results will be displayed here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAllLoans"></th>
                                        <th>Loan Number</th>
                                        <th>Customer</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Term</th>
                                        <th>Interest Rate</th>
                                        <th>Applied Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($all_loans as $loan): ?>
                                    <tr>
                                        <td><input type="checkbox" class="loan-checkbox" value="<?php echo $loan['id']; ?>"></td>
                                        <td><?php echo $loan['loan_number']; ?></td>
                                        <td><?php echo $loan['first_name'] . ' ' . $loan['last_name']; ?></td>
                                        <td><?php echo ucfirst($loan['loan_type']); ?></td>
                                        <td>BDT <?php echo number_format($loan['amount'], 2); ?></td>
                                        <td><?php echo $loan['term_months']; ?> months</td>
                                        <td><?php echo $loan['interest_rate']; ?>%</td>
                                        <td><?php echo date('M j, Y', strtotime($loan['applied_date'])); ?></td>
                                        <td><span class="status status-<?php echo $loan['status']; ?>"><?php echo ucfirst($loan['status']); ?></span></td>
                                        <td>
                                            <button class="btn btn-primary" onclick="viewLoan(<?php echo $loan['id']; ?>)">View</button>
                                            <?php if($loan['status'] == 'pending'): ?>
                                            <button class="btn btn-success" onclick="approveLoan(<?php echo $loan['id']; ?>)">Approve</button>
                                            <button class="btn btn-danger" onclick="rejectLoan(<?php echo $loan['id']; ?>)">Reject</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Deposits Section -->
                <div class="section" id="deposits">
                    <div class="section-header">
                        <div class="section-title">Deposits & Investments</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="showCreateDepositModal()"><i class="fas fa-plus"></i> New FD/RD</button>
                            <button class="btn btn-success" onclick="exportDeposits()"><i class="fas fa-file-export"></i> Export</button>
                        </div>
                    </div>
                    <!-- Deposit content would go here -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Fixed Deposit Summary</div>
                        </div>
                        <div class="card-content">
                            <p>This section would display fixed deposit accounts and their details.</p>
                            <p>In a complete implementation, you would see:</p>
                            <ul>
                                <li>Fixed deposit accounts with maturity dates</li>
                                <li>Recurring deposit accounts</li>
                                <li>Investment products</li>
                                <li>Interest calculations</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Reports Section -->
                <div class="section" id="reports">
                    <div class="section-header">
                        <div class="section-title">Reports & Analytics</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="generateReport()"><i class="fas fa-download"></i> Download Report</button>
                            <button class="btn btn-success" onclick="showAnalytics()"><i class="fas fa-chart-line"></i> Generate Analytics</button>
                        </div>
                    </div>
                    
                    <div class="grid-3">
                        <div class="card text-center">
                            <div class="card-header">
                                <div class="card-title">Customer Report</div>
                            </div>
                            <div class="card-content">
                                <p>Generate customer activity and account reports</p>
                                <button class="btn btn-primary" onclick="generateCustomerReport()">Generate</button>
                            </div>
                        </div>
                        
                        <div class="card text-center">
                            <div class="card-header">
                                <div class="card-title">Transaction Report</div>
                            </div>
                            <div class="card-content">
                                <p>Generate transaction history and analysis</p>
                                <button class="btn btn-primary" onclick="generateTransactionReport()">Generate</button>
                            </div>
                        </div>
                        
                        <div class="card text-center">
                            <div class="card-header">
                                <div class="card-title">Loan Report</div>
                            </div>
                            <div class="card-content">
                                <p>Generate loan portfolio and performance reports</p>
                                <button class="btn btn-primary" onclick="generateLoanReport()">Generate</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tasks Section -->
                <div class="section" id="tasks">
                    <div class="section-header">
                        <div class="section-title">Task Management</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="showCreateTaskModal()"><i class="fas fa-plus"></i> New Task</button>
                            <button class="btn btn-success" onclick="markSelectedTasksComplete()"><i class="fas fa-check"></i> Mark Complete</button>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAllTasks"></th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Priority</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($employee_tasks as $task): ?>
                                    <tr>
                                        <td><input type="checkbox" class="task-checkbox" value="<?php echo $task['id']; ?>"></td>
                                        <td><?php echo $task['title']; ?></td>
                                        <td><?php echo $task['description']; ?></td>
                                        <td><span class="status status-<?php echo $task['priority']; ?>"><?php echo ucfirst($task['priority']); ?></span></td>
                                        <td><?php echo date('M j, Y', strtotime($task['due_date'])); ?></td>
                                        <td><span class="status status-<?php echo $task['status']; ?>"><?php echo ucfirst($task['status']); ?></span></td>
                                        <td>
                                            <button class="btn btn-primary" onclick="viewTask(<?php echo $task['id']; ?>)">View</button>
                                            <?php if($task['status'] != 'completed'): ?>
                                            <button class="btn btn-success" onclick="updateTaskStatus(<?php echo $task['id']; ?>, 'completed')">Complete</button>
                                            <?php endif; ?>
                                            <button class="btn btn-danger" onclick="deleteTask(<?php echo $task['id']; ?>)">Delete</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Communication Section -->
                <div class="section" id="communication">
                    <div class="section-header">
                        <div class="section-title">Internal Communication</div>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="showSendMessageModal()"><i class="fas fa-plus"></i> New Message</button>
                        </div>
                    </div>
                    
                    <div class="grid-2">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Send Message</div>
                            </div>
                            <div class="card-content">
                                <form id="messageForm">
                                    <div class="form-group">
                                        <label class="form-label">Recipient</label>
                                        <select class="form-control" id="messageRecipient" required>
                                            <option value="">Select Recipient</option>
                                            <?php foreach($all_employees as $employee): ?>
                                            <option value="<?php echo $employee['id']; ?>"><?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Subject</label>
                                        <input type="text" class="form-control" id="messageSubject" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Message</label>
                                        <textarea class="form-control" id="messageContent" rows="4" required></textarea>
                                    </div>
                                    <button type="button" class="btn btn-primary" onclick="sendMessage()">Send Message</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Recent Messages</div>
                            </div>
                            <div class="card-content">
                                <?php
                                $recent_messages = $pdo->query("
                                    SELECT n.*, u.first_name, u.last_name 
                                    FROM notifications n 
                                    JOIN users u ON n.user_id = u.id 
                                    WHERE n.user_id = $employee_id 
                                    ORDER BY n.created_at DESC 
                                    LIMIT 5
                                ")->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach($recent_messages as $message):
                                ?>
                                <div class="mb-20">
                                    <div><strong><?php echo $message['title']; ?></strong></div>
                                    <div style="font-size: 12px; color: var(--dark);">From: <?php echo $message['first_name'] . ' ' . $message['last_name']; ?> • <?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?></div>
                                    <div style="margin-top: 5px;"><?php echo substr($message['message'], 0, 100); ?>...</div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Section -->
                <div class="section" id="profile">
                    <div class="section-header">
                        <div class="section-title">Profile & Settings</div>
                    </div>
                    
                    <div class="form-container">
                        <form id="profileForm" method="POST">
                            <input type="hidden" name="action" value="update_profile">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="first_name" value="<?php echo $_SESSION['first_name']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="last_name" value="<?php echo $_SESSION['last_name']; ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?php echo $_SESSION['email']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" value="<?php echo $_SESSION['phone'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="3"><?php echo $_SESSION['address'] ?? ''; ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>

                <!-- Security Section -->
                <div class="section" id="security">
                    <div class="section-header">
                        <div class="section-title">Security Settings</div>
                    </div>
                    
                    <div class="form-container">
                        <form id="securityForm" method="POST">
                            <input type="hidden" name="action" value="update_security">
                            <div class="form-group">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" required minlength="6">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" name="confirm_password" required minlength="6">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div class="modal" id="rejectModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Reject Registration</h3>
                <button class="close-modal" onclick="closeModal('rejectModal')">&times;</button>
            </div>
            <form id="rejectForm" method="POST">
                <input type="hidden" name="action" value="reject_registration">
                <input type="hidden" name="registration_id" id="rejectRegistrationId">
                <div class="form-group">
                    <label for="rejectionReason">Reason for Rejection</label>
                    <textarea id="rejectionReason" name="reason" class="form-control" rows="4" required placeholder="Please provide a reason for rejection..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" onclick="closeModal('rejectModal')">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Account Modal -->
    <div class="modal" id="createAccountModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Create New Account</h3>
                <button class="close-modal" onclick="closeModal('createAccountModal')">&times;</button>
            </div>
            <form id="createAccountForm" method="POST">
                <input type="hidden" name="action" value="create_account">
                <div class="form-group">
                    <label class="form-label">Customer</label>
                    <select class="form-control" name="user_id" required>
                        <option value="">Select Customer</option>
                        <?php foreach($all_customers as $customer): ?>
                        <option value="<?php echo $customer['id']; ?>"><?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?> (<?php echo $customer['email']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Account Type</label>
                    <select class="form-control" name="account_type" required>
                        <option value="">Select Account Type</option>
                        <?php foreach($account_types as $type): ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Branch</label>
                    <select class="form-control" name="branch_id" required>
                        <option value="">Select Branch</option>
                        <?php foreach($branches as $branch): ?>
                        <option value="<?php echo $branch['id']; ?>"><?php echo $branch['name']; ?> (<?php echo $branch['city']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Initial Deposit (BDT)</label>
                    <input type="number" class="form-control" name="initial_deposit" min="0" step="0.01" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" onclick="closeModal('createAccountModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Transaction Modal -->
    <div class="modal" id="createTransactionModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Create New Transaction</h3>
                <button class="close-modal" onclick="closeModal('createTransactionModal')">&times;</button>
            </div>
            <form id="createTransactionForm" method="POST">
                <input type="hidden" name="action" value="create_transaction">
                <div class="form-group">
                    <label class="form-label">Account</label>
                    <select class="form-control" name="account_id" required>
                        <option value="">Select Account</option>
                        <?php foreach($all_accounts as $account): ?>
                        <option value="<?php echo $account['id']; ?>"><?php echo $account['account_number']; ?> - <?php echo $account['first_name'] . ' ' . $account['last_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Transaction Type</label>
                    <select class="form-control" name="type" required>
                        <option value="">Select Type</option>
                        <option value="deposit">Deposit</option>
                        <option value="withdrawal">Withdrawal</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Amount (BDT)</label>
                    <input type="number" class="form-control" name="amount" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" onclick="closeModal('createTransactionModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Transaction</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Loan Modal -->
    <div class="modal" id="createLoanModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Create New Loan</h3>
                <button class="close-modal" onclick="closeModal('createLoanModal')">&times;</button>
            </div>
            <form id="createLoanForm" method="POST">
                <input type="hidden" name="action" value="create_loan">
                <div class="form-group">
                    <label class="form-label">Customer</label>
                    <select class="form-control" name="user_id" required>
                        <option value="">Select Customer</option>
                        <?php foreach($all_customers as $customer): ?>
                        <option value="<?php echo $customer['id']; ?>"><?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?> (<?php echo $customer['email']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Loan Type</label>
                    <select class="form-control" name="loan_type" required>
                        <option value="">Select Loan Type</option>
                        <option value="home">Home Loan</option>
                        <option value="car">Car Loan</option>
                        <option value="personal">Personal Loan</option>
                        <option value="business">Business Loan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Loan Amount (BDT)</label>
                    <input type="number" class="form-control" name="amount" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Interest Rate (%)</label>
                    <input type="number" class="form-control" name="interest_rate" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Loan Term (Months)</label>
                    <input type="number" class="form-control" name="term_months" min="1" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" onclick="closeModal('createLoanModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Loan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Task Modal -->
    <div class="modal" id="createTaskModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Create New Task</h3>
                <button class="close-modal" onclick="closeModal('createTaskModal')">&times;</button>
            </div>
            <form id="createTaskForm" method="POST">
                <input type="hidden" name="action" value="create_task">
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Assign To</label>
                    <select class="form-control" name="assigned_to" required>
                        <option value="">Select Employee</option>
                        <?php foreach($all_employees as $employee): ?>
                        <option value="<?php echo $employee['id']; ?>"><?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Priority</label>
                    <select class="form-control" name="priority" required>
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Due Date</label>
                    <input type="date" class="form-control" name="due_date" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" onclick="closeModal('createTaskModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // JavaScript for all interactive functionality
        
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

        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get the target section
                const targetSection = this.getAttribute('data-section');
                
                // Update active menu item
                menuLinks.forEach(item => item.classList.remove('active'));
                this.classList.add('active');
                
                // Show target section
                sections.forEach(section => {
                    section.classList.remove('active');
                    if (section.id === targetSection) {
                        section.classList.add('active');
                    }
                });
                
                // Update page title
                const menuText = this.querySelector('span').textContent;
                pageTitle.textContent = menuText + " - Employee Dashboard";
                
                // Close sidebar on mobile after selection
                if (window.innerWidth <= 1200) {
                    sidebar.classList.remove('active');
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

        // Add interactivity to buttons
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    // Add a small animation to buttons when clicked
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
        });

        // Registration Functions
        function approveRegistration(registrationId) {
            if (confirm('Are you sure you want to approve this registration?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'approve_registration';
                form.appendChild(actionInput);
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'registration_id';
                idInput.value = registrationId;
                form.appendChild(idInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function rejectRegistration(registrationId) {
            document.getElementById('rejectRegistrationId').value = registrationId;
            document.getElementById('rejectModal').style.display = 'flex';
        }
        
        // Loan Functions
        function approveLoan(loanId) {
            if (confirm('Are you sure you want to approve this loan?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'update_loan_status';
                form.appendChild(actionInput);
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'loan_id';
                idInput.value = loanId;
                form.appendChild(idInput);
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = 'approved';
                form.appendChild(statusInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function rejectLoan(loanId) {
            if (confirm('Are you sure you want to reject this loan?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'update_loan_status';
                form.appendChild(actionInput);
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'loan_id';
                idInput.value = loanId;
                form.appendChild(idInput);
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = 'rejected';
                form.appendChild(statusInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function approveSelectedLoans() {
            const selectedLoans = Array.from(document.querySelectorAll('.loan-checkbox:checked')).map(cb => cb.value);
            if (selectedLoans.length === 0) {
                alert('Please select at least one loan to approve.');
                return;
            }
            
            if (confirm(`Are you sure you want to approve ${selectedLoans.length} selected loans?`)) {
                selectedLoans.forEach(loanId => {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '';
                    
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'update_loan_status';
                    form.appendChild(actionInput);
                    
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'loan_id';
                    idInput.value = loanId;
                    form.appendChild(idInput);
                    
                    const statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'status';
                    statusInput.value = 'approved';
                    form.appendChild(statusInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                });
            }
        }
        
        // Account Functions
        function updateAccountStatus(accountId, status) {
            if (confirm(`Are you sure you want to ${status} this account?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'update_account_status';
                form.appendChild(actionInput);
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'account_id';
                idInput.value = accountId;
                form.appendChild(idInput);
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = status;
                form.appendChild(statusInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function closeAccount(accountId) {
            if (confirm('Are you sure you want to close this account? This action cannot be undone.')) {
                updateAccountStatus(accountId, 'closed');
            }
        }
        
        // Customer Functions
        function updateCustomerStatus(customerId, status) {
            if (confirm(`Are you sure you want to ${status} this customer?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'update_customer_status';
                form.appendChild(actionInput);
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'customer_id';
                idInput.value = customerId;
                form.appendChild(idInput);
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = status;
                form.appendChild(statusInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Transaction Functions
        function approveTransaction(transactionId) {
            if (confirm('Are you sure you want to approve this transaction?')) {
                // In a real implementation, this would update the transaction status
                alert('Transaction approved! In a real implementation, this would update the database.');
            }
        }
        
        function rejectTransaction(transactionId) {
            if (confirm('Are you sure you want to reject this transaction?')) {
                // In a real implementation, this would update the transaction status
                alert('Transaction rejected! In a real implementation, this would update the database.');
            }
        }
        
        // Task Functions
        function updateTaskStatus(taskId, status) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'update_task_status';
            form.appendChild(actionInput);
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'task_id';
            idInput.value = taskId;
            form.appendChild(idInput);
            
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            form.appendChild(statusInput);
            
            document.body.appendChild(form);
            form.submit();
        }
        
        function markSelectedTasksComplete() {
            const selectedTasks = Array.from(document.querySelectorAll('.task-checkbox:checked')).map(cb => cb.value);
            if (selectedTasks.length === 0) {
                alert('Please select at least one task to mark as complete.');
                return;
            }
            
            if (confirm(`Are you sure you want to mark ${selectedTasks.length} selected tasks as complete?`)) {
                selectedTasks.forEach(taskId => {
                    updateTaskStatus(taskId, 'completed');
                });
            }
        }
        
        function deleteTask(taskId) {
            if (confirm('Are you sure you want to delete this task?')) {
                // In a real implementation, this would delete the task from the database
                alert('Task deleted! In a real implementation, this would remove it from the database.');
            }
        }
        
        // Modal Functions
        function showCreateAccountModal() {
            document.getElementById('createAccountModal').style.display = 'flex';
        }
        
        function showCreateTransactionModal() {
            document.getElementById('createTransactionModal').style.display = 'flex';
        }
        
        function showCreateLoanModal() {
            document.getElementById('createLoanModal').style.display = 'flex';
        }
        
        function showCreateTaskModal() {
            document.getElementById('createTaskModal').style.display = 'flex';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                e.target.style.display = 'none';
            }
        });
        
        // Utility Functions
        function refreshData() {
            location.reload();
        }
        
        function exportReport() {
            alert('Exporting report... This would generate a PDF/Excel file in a real application.');
            // In real implementation, this would redirect to an export script
            window.open('export_reports.php?type=dashboard', '_blank');
        }
        
        function exportRegistrations() {
            window.open('export_reports.php?type=registrations', '_blank');
        }
        
        function exportCustomers() {
            window.open('export_reports.php?type=customers', '_blank');
        }
        
        function exportTransactions() {
            window.open('export_reports.php?type=transactions', '_blank');
        }
        
        function exportDeposits() {
            window.open('export_reports.php?type=deposits', '_blank');
        }
        
        function viewAllTransactions() {
            // Navigate to transactions section
            document.querySelector('.menu-link[data-section="transactions"]').click();
        }
        
        function viewAllTasks() {
            // Navigate to tasks section
            document.querySelector('.menu-link[data-section="tasks"]').click();
        }
        
        // EMI Calculator
        function calculateEMI() {
            const amount = parseFloat(document.getElementById('loanAmount').value);
            const rate = parseFloat(document.getElementById('interestRate').value);
            const term = parseInt(document.getElementById('loanTerm').value) * 12; // Convert years to months
            
            if (isNaN(amount) || isNaN(rate) || isNaN(term)) {
                alert('Please enter valid numbers for all fields.');
                return;
            }
            
            const monthlyRate = rate / 100 / 12;
            const emi = (amount * monthlyRate * Math.pow(1 + monthlyRate, term)) / (Math.pow(1 + monthlyRate, term) - 1);
            const totalPayment = emi * term;
            const totalInterest = totalPayment - amount;
            
            document.getElementById('emiResult').innerHTML = `
                <div><strong>Monthly EMI:</strong> BDT ${emi.toFixed(2)}</div>
                <div><strong>Total Interest:</strong> BDT ${totalInterest.toFixed(2)}</div>
                <div><strong>Total Payment:</strong> BDT ${totalPayment.toFixed(2)}</div>
            `;
        }
        
        // Select all loans checkbox
        document.getElementById('selectAllLoans').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.loan-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
        
        // Select all tasks checkbox
        document.getElementById('selectAllTasks').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.task-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
        
        // Search customers
        function searchCustomers() {
            const searchTerm = document.getElementById('customerSearch').value.toLowerCase();
            const rows = document.querySelectorAll('#customers table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        // Apply transaction filters
        function applyTransactionFilters() {
            alert('Filters applied! In a real implementation, this would filter the transaction table.');
        }
        
        // Send message
        function sendMessage() {
            const recipient = document.getElementById('messageRecipient').value;
            const subject = document.getElementById('messageSubject').value;
            const content = document.getElementById('messageContent').value;
            
            if (!recipient || !subject || !content) {
                alert('Please fill in all fields.');
                return;
            }
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'send_message';
            form.appendChild(actionInput);
            
            const recipientInput = document.createElement('input');
            recipientInput.type = 'hidden';
            recipientInput.name = 'recipient_id';
            recipientInput.value = recipient;
            form.appendChild(recipientInput);
            
            const subjectInput = document.createElement('input');
            subjectInput.type = 'hidden';
            subjectInput.name = 'subject';
            subjectInput.value = subject;
            form.appendChild(subjectInput);
            
            const messageInput = document.createElement('input');
            messageInput.type = 'hidden';
            messageInput.name = 'message';
            messageInput.value = content;
            form.appendChild(messageInput);
            
            document.body.appendChild(form);
            form.submit();
        }
        
        // Report generation functions
        function generateCustomerReport() {
            alert('Generating customer report... This would create a PDF/Excel file in a real application.');
        }
        
        function generateTransactionReport() {
            alert('Generating transaction report... This would create a PDF/Excel file in a real application.');
        }
        
        function generateLoanReport() {
            alert('Generating loan report... This would create a PDF/Excel file in a real application.');
        }
        
        // Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Transaction Chart
            const transactionCtx = document.getElementById('transactionChart').getContext('2d');
            const transactionChart = new Chart(transactionCtx, {
                type: 'line',
                data: {
                    labels: ['9 AM', '10 AM', '11 AM', '12 PM', '1 PM', '2 PM', '3 PM'],
                    datasets: [{
                        label: 'Deposits',
                        data: [45000, 52000, 48000, 61000, 55000, 72000, 68000],
                        borderColor: '#2ecc71',
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
                        tension: 0.3,
                        fill: true
                    }, {
                        label: 'Withdrawals',
                        data: [38000, 42000, 35000, 48000, 52000, 45000, 39000],
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
                                    return 'BDT' + value/1000 + 'k';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>