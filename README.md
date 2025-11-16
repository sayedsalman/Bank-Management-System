🏦 Bank Management System

🔗 Live Demo: https://salman.rfnhsc.com/bank

A complete Bank Management System built using PHP, MySQL, HTML, CSS, JS, SASS, GSAP, and Three.js.
This project provides a secure, interactive, and visually rich banking experience for customers, employees, and administrators.

🧩 Main Features
Section	Features
🏦 Dashboard Overview	Displays total balance, recent transactions, quick transfers, and savings summaries.
💳 Accounts	Shows all user accounts (Savings, Current, Fixed Deposit, etc.) with details — account number, type, balance, and interest rate.
💰 Loans & Schemes	View current loans, EMIs, and savings schemes. Apply for new loans or view status.
🔄 Transactions	View all transactions with date/type filters and downloadable statements.
📲 Make Transactions	- Send money (own/other accounts)
- Mobile top-up
- Utility bill payments
🔔 Notifications	Receive transaction alerts, offers, and loan reminders.
👤 Profile	Manage user info, photo, email, phone, address, and KYC status. Change password securely.
⚙️ Settings	Manage 2FA, alerts, and security preferences.
🚪 Logout	Ends session securely with CSRF protection.
🏦 Core Purpose of the Public Index Page

The public-facing index page of this bank website is designed to:
✅ Build trust and credibility
✅ Communicate services clearly
✅ Make key actions easy and secure (login, register, apply)
✅ Reflect a strong brand identity

🔹 1. Header Section

✅ Bank logo (linked to homepage)

✅ Navigation Menu:

Home

Personal Banking

Business/Corporate Banking

Loans & Credit Cards

Digital/Internet Banking

About Us

Contact / Branch Locator

✅ Login buttons (Customer, Corporate)

✅ Register / Forgot Password links

✅ Optional: Search bar & language switcher

🔹 2. Hero Section

Eye-catching banner with GSAP/Three.js animation

Tagline: “Your trusted partner in financial growth”

Call-to-Action buttons:

“Open an Account”

“Apply for a Loan”

“Learn More”

🔹 3. Quick Access / Action Panel

Fast access icons for:

💳 Account Opening

💰 Loans / Credit Cards

📱 Mobile Banking

🔐 Internet Banking

🏧 ATM / Branch Locator

🔹 4. Featured Products & Services

Highlight core offerings:

Savings Accounts

Fixed Deposits

Personal / Home / Car Loans

Credit Cards

Investment / Insurance Plans

SME Banking

Each includes short info + Apply/Learn More button.

🔹 5. Exchange & Interest Rates

Auto-updated via backend/API:

Foreign exchange rates

Deposit & loan interest rates

🔹 6. Announcements & News

Dynamic area for:

Bank notices

Policy updates

Press releases

Job circulars

CSR news

🔹 7. Security Notices

Show essential safety tips:

Phishing alerts

Online security tips

Fraud warnings

Privacy & policy links

🔹 8. Testimonials / Trust Indicators

(Optional but valuable)

Customer satisfaction quotes

Ratings and reviews

🎯 Admin Dashboard Overview

The Admin Panel is the control center of the entire banking system.

Main Goals

Monitor and manage all bank activities

Ensure security and accuracy of data

Generate insights (deposits, loans, transactions, users)

Manage employees, customers, and website content

🧩 Core Sections of the Admin Dashboard
Section	Purpose
📊 Dashboard	View total users, deposits, withdrawals, and activities.
👥 Customers	Add, edit, and verify customer profiles (KYC).
🧑‍💼 Employees	Manage staff details, roles, and permissions.
💰 Loans & Schemes	Approve, reject, and monitor loan applications.
🔄 Transactions	View and track all bank transactions.
📈 Reports	Export reports (CSV, PDF).
⚙️ Settings	Configure bank preferences and rates.
🔐 Logout	Secure session termination.
🗂️ Project File Structure
/Bank-Management-System
│
├── admin/              → Admin dashboard & management panel  
├── config/             → Database configuration (config.php)  
├── customer/           → Customer dashboard & functions  
├── db/                 → Database SQL dump  
├── employee/           → Employee section & HR management  
├── export_reports/     → Report generation & export features  
├── index.php           → Public home page  
├── locator.php         → Branch & ATM locator  
├── login.php           → Login portal  
├── register.php        → User registration  
└── assets/             → (Optional) CSS, JS, SASS, images  

🧠 Technologies Used

Frontend: HTML5, CSS3, JavaScript (ES6+), SASS

Animation: GSAP, Three.js

Backend: PHP (Core + MySQLi/PDO)

Database: MySQL

Reports: Export (PDF/CSV)

Security: Session management, CSRF tokens, password hashing

⚙️ Installation & Setup

Clone or Download the project files.

Import the SQL file from /db into your MySQL server.

Update database credentials in config/config.php.

Run locally via XAMPP / WAMP / LAMP server.

Open in browser:

http://localhost/Bank-Management-System/


Or visit the live version:
👉 https://salman.rfnhsc.com/bank



