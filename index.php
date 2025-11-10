<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Losers - Advanced Bank Management System</title>
    <link rel="icon" type="image/png" sizes="32x32" href="https://salman.rfnhsc.com/bank/uploads/losers.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://salman.rfnhsc.com/bank/uploads/losers.png">
    <link rel="shortcut icon" href="https://salman.rfnhsc.com/bank/uploads/losers.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <style>
        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        :root {
            --primary: #1a237e;
            --secondary: #283593;
            --accent: #3949ab;
            --light: #e8eaf6;
            --dark: #0d153a;
            --success: #4caf50;
            --warning: #ff9800;
            --danger: #f44336;
            --text: #333;
            --bg: #f5f7fa;
            --card-bg: white;
            --header-bg: white;
            --footer-bg: var(--dark);
            --shadow: rgba(0,0,0,0.1);
        }

        .dark-theme {
            --primary: #3949ab;
            --secondary: #283593;
            --accent: #5c6bc0;
            --light: #1e1e2d;
            --dark: #121212;
            --text: #e0e0e0;
            --bg: #121212;
            --card-bg: #1e1e2d;
            --header-bg: #1e1e2d;
            --footer-bg: #0d0d15;
            --shadow: rgba(0,0,0,0.3);
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        section {
            padding: 80px 0;
        }

        h1, h2, h3, h4 {
            margin-bottom: 20px;
            color: var(--primary);
        }

        p {
            margin-bottom: 15px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn:hover {
            background-color: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px var(--shadow);
        }

        /* Theme Toggle */
        .theme-toggle {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px var(--shadow);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            transform: scale(1.1);
        }

        /* Header Styles */
        header {
            background-color: var(--header-bg);
            box-shadow: 0 2px 10px var(--shadow);
            position: fixed;
            width: 100%;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .scrolled {
            padding: 10px 0;
            background-color: var(--header-bg);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            transition: all 0.3s ease;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
        }

        .logo span {
            color: var(--accent);
        }

        .nav-links {
            display: flex;
            list-style: none;
        }

        .nav-links li {
            margin-left: 30px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text);
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--accent);
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-links a:hover {
            color: var(--accent);
        }

        .login-btn {
            background-color: var(--accent);
            padding: 8px 20px;
            border-radius: 20px;
            margin-left: 20px;
        }

        .login-btn:hover {
            background-color: var(--primary);
        }
        
        /* FAQ Section */
        .faq {
            background-color: var(--card-bg);
            padding: 60px 0;
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            margin-bottom: 20px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px var(--shadow);
        }

        .faq-question {
            background-color: var(--light);
            padding: 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
        }

        .faq-answer {
            background-color: var(--card-bg);
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }

        .faq-answer.active {
            padding: 20px;
            max-height: 500px;
        }

        .faq-toggle {
            font-size: 20px;
            transition: transform 0.3s ease;
        }

        .faq-toggle.active {
            transform: rotate(45deg);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 180px 0 120px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,192C1248,192,1344,128,1392,96L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: center;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            color: white;
        }

        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
        }

        .hero-btns {
            margin-top: 30px;
        }

        .hero-btns .btn {
            margin: 0 10px;
        }

        .btn-outline {
            background-color: transparent;
            border: 2px solid white;
        }

        .btn-outline:hover {
            background-color: white;
            color: var(--primary);
        }

        /* Floating Elements */
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: 0;
        }

        .floating-element {
            position: absolute;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 15s infinite ease-in-out;
        }

        .floating-element:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            left: 80%;
            animation-delay: 2s;
        }

        .floating-element:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 80%;
            left: 20%;
            animation-delay: 4s;
        }

        .floating-element:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 30%;
            left: 70%;
            animation-delay: 6s;
        }

        /* Quick Access Section */
        .quick-access {
            background-color: var(--card-bg);
            padding: 60px 0;
            box-shadow: 0 5px 15px var(--shadow);
        }

        .access-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .access-card {
            background-color: var(--light);
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 15px var(--shadow);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .access-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px var(--shadow);
        }

        .access-icon {
            font-size: 40px;
            color: var(--accent);
            margin-bottom: 15px;
        }

        .access-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        /* Features Section */
        .features {
            background-color: var(--bg);
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 36px;
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: var(--accent);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background-color: var(--card-bg);
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 15px var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, var(--primary), var(--accent));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px var(--shadow);
        }

        .feature-icon {
            font-size: 48px;
            color: var(--accent);
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
        }

        /* Rates Section */
        .rates {
            background-color: var(--card-bg);
            padding: 60px 0;
        }

        .rates-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
        }

        .rate-card {
            background-color: var(--light);
            border-radius: 8px;
            padding: 25px;
            flex: 1;
            min-width: 250px;
            box-shadow: 0 5px 15px var(--shadow);
            transition: transform 0.3s ease;
        }

        .rate-card:hover {
            transform: translateY(-5px);
        }

        .rate-card h3 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--primary);
        }

        .rate-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .rate-value {
            font-weight: bold;
            color: var(--accent);
        }

        /* Team Section - Updated Layout */
        .team {
            background-color: var(--bg);
        }

        .team-grid {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }

        .supervisor-container {
            display: flex;
            justify-content: center;
            width: 100%;
            margin-bottom: 30px;
        }

        .team-members-row {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
            width: 100%;
        }

        .team-member {
            background-color: var(--card-bg);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px var(--shadow);
            transition: transform 0.3s ease;
            cursor: pointer;
            position: relative;
            width: 250px;
        }

        .team-member.supervisor {
            width: 300px;
        }

        .team-member::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, transparent, var(--primary));
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .team-member:hover::before {
            opacity: 0.1;
        }

        .team-member:hover {
            transform: translateY(-10px);
        }

        .member-img {
            height: 250px;
            background-color: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 0;
            overflow: hidden;
        }

        .member-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .member-info {
            padding: 20px;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .member-info h3 {
            margin-bottom: 5px;
        }

        .member-info p {
            color: var(--accent);
            font-weight: 500;
        }

        /* Team Details Section */
        .team-details {
            background-color: var(--card-bg);
            padding: 60px 0;
            display: none;
        }

        .team-details.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        .details-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 40px;
        }

        .details-img {
            flex: 1;
            min-width: 300px;
            height: 400px;
            background-color: var(--light);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .details-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .details-content {
            flex: 2;
            min-width: 300px;
        }

        .details-content h2 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .details-content .role {
            font-size: 18px;
            color: var(--accent);
            margin-bottom: 20px;
            font-weight: 500;
        }

        .back-btn {
            margin-top: 20px;
            background-color: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .back-btn:hover {
            background-color: var(--primary);
            color: white;
        }
        
        /* Announcements Section */
        .announcements {
            background-color: var(--card-bg);
            padding: 60px 0;
        }

        .announcement-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .announcement-item {
            background-color: var(--light);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px var(--shadow);
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .announcement-item:hover {
            transform: translateX(10px);
        }

        .announcement-date {
            font-size: 14px;
            color: var(--accent);
            margin-bottom: 10px;
        }

        /* Security Section */
        .security {
            background-color: var(--bg);
            padding: 60px 0;
        }

        .security-content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .security-icons {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 30px 0;
            flex-wrap: wrap;
        }

        .security-icon {
            font-size: 40px;
            color: var(--accent);
        }

        /* Footer */
        footer {
            background-color: var(--footer-bg);
            color: white;
            padding: 60px 0 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-column h3 {
            color: white;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .footer-column p, .footer-column a {
            color: #b0b0b0;
            margin-bottom: 10px;
            display: block;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-column a:hover {
            color: white;
        }

        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            background-color: var(--accent);
            transform: translateY(-5px);
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #b0b0b0;
            font-size: 14px;
        }

        /* New Styles for Enhanced Features */
        
        /* Slider Section */
        .slider-section {
            background-color: var(--bg);
            padding: 60px 0;
        }
        
        .offer-slider {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .slide {
            background-color: var(--card-bg);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px var(--shadow);
            margin: 0 15px;
        }
        
        .slide-content {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .slide-img {
            flex: 1;
            min-width: 300px;
            height: 300px;
            background-color: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
            color: var(--accent);
        }
        
        .slide-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .slide-text {
            flex: 1;
            min-width: 300px;
            padding: 30px;
        }
        
        .slide-text h3 {
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .slide-text .btn {
            margin-top: 20px;
        }
        
        /* Enhanced Rates Section */
        .enhanced-rates {
            background-color: var(--card-bg);
            padding: 60px 0;
        }
        
        .rates-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            flex-wrap: wrap;
        }
        
        .tab-btn {
            padding: 12px 25px;
            background: none;
            border: none;
            font-size: 16px;
            font-weight: 600;
            color: var(--text);
            cursor: pointer;
            position: relative;
        }
        
        .tab-btn.active {
            color: var(--accent);
        }
        
        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--accent);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        .rates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .rate-card-enhanced {
            background-color: var(--light);
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 5px 15px var(--shadow);
            transition: transform 0.3s ease;
        }
        
        .rate-card-enhanced:hover {
            transform: translateY(-5px);
        }
        
        .rate-card-enhanced h3 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--primary);
        }
        
        /* Live Chat */
        .live-chat {
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--success);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px var(--shadow);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .live-chat:hover {
            transform: scale(1.1);
        }
        
        .chat-window {
            position: fixed;
            bottom: 100px;
            left: 30px;
            width: 350px;
            height: 450px;
            background-color: var(--card-bg);
            border-radius: 10px;
            box-shadow: 0 10px 30px var(--shadow);
            z-index: 1000;
            display: none;
            flex-direction: column;
            overflow: hidden;
        }
        
        .chat-header {
           
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .chat-body {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
        }
        
        .chat-message {
            margin-bottom: 15px;
            padding: 10px 15px;
            border-radius: 20px;
            max-width: 80%;
        }
        
        .chat-message.bot {
            background-color: var(--light);
            border-top-left-radius: 5px;
            align-self: flex-start;
        }
        
        .chat-message.user {
            background-color: var(--accent);
            color: white;
            border-top-right-radius: 5px;
            align-self: flex-end;
            margin-left: auto;
        }
        
        .chat-footer {
            padding: 15px;
            border-top: 1px solid rgba(0,0,0,0.1);
            display: flex;
        }
        
        .chat-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 20px;
            background-color: var(--bg);
            color: var(--text);
        }
        
        .chat-send {
            background: none;
            border: none;
            color: var(--accent);
            font-size: 20px;
            margin-left: 10px;
            cursor: pointer;
        }
        
        /* Fraud Alert */
        .fraud-alert {
            background-color: var(--danger);
            color: white;
            padding: 15px 0;
            text-align: center;
            position: relative;
        }
        
        .fraud-alert p {
            margin: 0;
        }
        
        .fraud-alert .close-alert {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }
        
        /* Quick Tools */
        .quick-tools {
            background-color: var(--bg);
            padding: 60px 0;
        }
        
        .tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .tool-card {
            background-color: var(--card-bg);
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 15px var(--shadow);
            cursor: pointer;
        }
        
        .tool-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px var(--shadow);
        }
        
        .tool-icon {
            font-size: 40px;
            color: var(--accent);
            margin-bottom: 15px;
        }
        
        .tool-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        /* Banking Stickers */
        .banking-stickers {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 999;
        }
        
        .sticker {
            position: absolute;
            font-size: 24px;
            opacity: 0.1;
            animation: floatSticker 20s infinite linear;
            pointer-events: none;
        }
        
        .sticker:nth-child(1) {
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }
        
        .sticker:nth-child(2) {
            top: 20%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .sticker:nth-child(3) {
            bottom: 30%;
            left: 15%;
            animation-delay: 4s;
        }
        
        .sticker:nth-child(4) {
            bottom: 20%;
            right: 5%;
            animation-delay: 6s;
        }
        
        .sticker:nth-child(5) {
            top: 40%;
            left: 8%;
            animation-delay: 8s;
        }
        
        .sticker:nth-child(6) {
            top: 60%;
            right: 12%;
            animation-delay: 10s;
        }
        
        /* NEW STYLES FOR EXPANDABLE SECTIONS */
        
        /* Expandable Cards */
        .expandable-section {
            margin-bottom: 30px;
        }
        
        .section-card {
            background-color: var(--card-bg);
            border-radius: 8px;
            box-shadow: 0 5px 15px var(--shadow);
            overflow: hidden;
            margin-bottom: 20px;
            cursor: pointer;
        }
        
        .section-header {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--light);
            transition: background-color 0.3s ease;
        }
        
        .section-header:hover {
            background-color: rgba(0,0,0,0.05);
        }
        
        .section-header h3 {
            margin: 0;
            color: var(--primary);
        }
        
        .section-toggle {
            font-size: 20px;
            transition: transform 0.3s ease;
        }
        
        .section-toggle.active {
            transform: rotate(180deg);
        }
        
        .section-content {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease, padding 0.3s ease;
        }
        
        .section-content.active {
            padding: 20px;
            max-height: 1000px;
        }
        
        /* Announcement Details */
        .announcement-details {
            background-color: var(--light);
            border-radius: 8px;
            padding: 20px;
            margin-top: 15px;
            display: none;
        }
        
        .announcement-details.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        /* Tool Details */
        .tool-details {
            background-color: var(--light);
            border-radius: 8px;
            padding: 20px;
            margin-top: 15px;
            display: none;
        }
        
        .tool-details.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        /* Text with Image Slider */
        .text-image-slider {
            background-color: var(--card-bg);
            padding: 60px 0;
        }
        
        .text-image-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 40px;
        }
        
        .text-content {
            flex: 1;
            min-width: 300px;
        }
        
        .image-slider-container {
            flex: 1;
            min-width: 300px;
        }
        
        .image-slider {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 10px 30px var(--shadow);
        }
        
        .image-slide {
            height: 300px;
            background-color: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
            color: var(--accent);
        }
        
        .image-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(10deg);
            }
        }
        
        @keyframes floatSticker {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            25% {
                transform: translateY(-20px) rotate(5deg);
            }
            50% {
                transform: translateY(-10px) rotate(10deg);
            }
            75% {
                transform: translateY(-15px) rotate(5deg);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
            }

            .nav-links {
                margin-top: 20px;
                flex-wrap: wrap;
                justify-content: center;
            }

            .nav-links li {
                margin: 5px 10px;
            }

            .hero h1 {
                font-size: 36px;
            }

            .hero p {
                font-size: 18px;
            }

            .details-container {
                flex-direction: column;
            }
            
            .slide-content {
                flex-direction: column;
            }
            
            .chat-window {
                width: 300px;
                height: 400px;
                left: 10px;
                bottom: 80px;
            }
            
            .team-members-row {
                flex-direction: column;
                align-items: center;
            }
            
            .team-member, .team-member.supervisor {
                width: 100%;
                max-width: 300px;
            }
            
            .rates-tabs {
                flex-direction: column;
                align-items: center;
            }
            
            .tab-btn {
                width: 100%;
                text-align: center;
            }
            
            .text-image-container {
                flex-direction: column;
            }
        }
        
        @media (max-width: 480px) {
            .hero h1 {
                font-size: 28px;
            }
            
            .hero p {
                font-size: 16px;
            }
            
            .hero-btns .btn {
                display: block;
                margin: 10px auto;
                width: 80%;
            }
            
            .section-title h2 {
                font-size: 28px;
            }
            
            .access-grid, .features-grid, .tools-grid {
                grid-template-columns: 1fr;
            }
        }
        
        <style>
.developed {
  position: relative;
  color: #00e5ff;
  font-weight: bold;
  text-shadow: 0 0 10px #00e5ff;
  transition: 0.3s;
  cursor: pointer;
}

.developed:hover {
  color: #fff;
  text-shadow: 0 0 20px #00e5ff, 0 0 30px #00e5ff;
}

.developed::after {
  content: "✨ No. 1 Team in Website!";
  position: absolute;
  bottom: 130%;
  left: 50%;
  transform: translateX(-50%);
  background: #111;
  color: #00e5ff;
  padding: 8px 12px;
  border-radius: 8px;
  opacity: 0;
  white-space: nowrap;
  transition: 0.4s ease;
}

.developed:hover::after {
  opacity: 1;
  bottom: 150%;
}
</style>


    </style>
</head>
<body>
    <!-- Banking Stickers -->
    <div class="banking-stickers">
        <div class="sticker">💰</div>
        <div class="sticker">🏦</div>
        <div class="sticker">💳</div>
        <div class="sticker">📈</div>
        <div class="sticker">🔒</div>
        <div class="sticker">💵</div>
    </div>

    <!-- Theme Toggle -->
    <div class="theme-toggle" id="theme-toggle">
        <i class="fas fa-moon"></i>
    </div>

    <!-- Live Chat -->
    <div class="live-chat" id="live-chat">
        <i class="fas fa-comments"></i>
    </div>
    
    <div class="chat-window" id="chat-window">
        <div class="chat-header">
            <h3>Customer Support</h3>
            <button id="close-chat"><i class="fas fa-times"></i></button>
        </div>
        <div class="chat-body" id="chat-body">
            <div class="chat-message bot">
                Hello! How can I help you today?
            </div>
        </div>
        <div class="chat-footer">
            <input type="text" class="chat-input" id="chat-input" placeholder="Type your message...">
            <button class="chat-send" id="chat-send"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <!-- Header -->
    <header id="header">
        <div class="container">
            <nav class="navbar">
                <div class="logo">Losers<span>Bank</span></div>
                <ul class="nav-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#rates">Rates</a></li>
                    <li><a href="#team">Our Team</a></li>
                    <li><a href="#announcements">Announcements</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="login.php" class="login-btn">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="floating-elements">
            <div class="floating-element"></div>
            <div class="floating-element"></div>
            <div class="floating-element"></div>
            <div class="floating-element"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <h1>Advanced Bank Management System</h1>
                <p>Secure, efficient, and user-friendly banking solutions for the modern world. Developed by the "Losers" team with cutting-edge technology.</p>
                <div class="hero-btns">
                    <a href="#features" class="btn">Explore Features</a>
                    <a href="register.php" class="btn btn-outline">Open Account</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Access Section -->
    <section class="quick-access">
        <div class="container">
            <div class="access-grid">
                <a href="https://salman.rfnhsc.com/bank/register.php" class="access-card">
                    <div class="access-icon"><i class="fas fa-user-plus"></i></div>
                    <h3>Open Account</h3>
                </a>
                <a href="#" class="access-card">
                    <div class="access-icon"><i class="fas fa-money-check-alt"></i></div>
                    <h3>Apply for Loan</h3>
                </a>
                <a href="#" class="access-card">
                    <div class="access-icon"><i class="fas fa-mobile-alt"></i></div>
                    <h3>Mobile Banking</h3>
                </a>
                <a href="https://salman.rfnhsc.com/bank/login.php" class="access-card">
                    <div class="access-icon"><i class="fas fa-sign-in-alt"></i></div>
                    <h3>Internet Banking</h3>
                </a>
                <a href="https://salman.rfnhsc.com/bank/locator.php" class="access-card">
                    <div class="access-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <h3>Branch Locator</h3>
                </a>
                <a href="#" class="access-card">
                    <div class="access-icon"><i class="fas fa-credit-card"></i></div>
                    <h3>Credit Cards</h3>
                </a>
                <a href="#" class="access-card">
                    <div class="access-icon"><i class="fas fa-piggy-bank"></i></div>
                    <h3>Savings Plans</h3>
                </a>
                <a href="#" class="access-card">
                    <div class="access-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3>Security Center</h3>
                </a>
            </div>
        </div>
    </section>

    <!-- Slider Section -->
   <section class="slider-section">
        <div class="container">
            <div class="section-title">
                <h2>Special Offers</h2>
                <p>Check out our latest account and loan offers</p>
            </div>
            <div class="offer-slider">
                <div class="slide">
                    <div class="slide-content">
                        <div class="slide-img">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                        <div class="slide-text">
                            <h3>Premium Savings Account</h3>
                            <p>Earn up to 4.5% interest with our premium savings account. No minimum balance required and free ATM withdrawals.</p>
                            <a href="#" class="btn">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="slide">
                    <div class="slide-content">
                        <div class="slide-img">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="slide-text">
                            <h3>Home Loan Special</h3>
                            <p>Get a home loan with interest rates as low as 5.8%. Special offer for first-time home buyers with reduced processing fees.</p>
                            <a href="#" class="btn">Apply Now</a>
                        </div>
                    </div>
                </div>
                <div class="slide">
                    <div class="slide-content">
                        <div class="slide-img">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="slide-text">
                            <h3>Gold Credit Card</h3>
                            <p>Apply for our Gold Credit Card and get 5% cashback on all purchases for the first 3 months. No annual fee for the first year.</p>
                            <a href="#" class="btn">Apply Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Key Features</h2>
                <p>Our bank management system offers a comprehensive suite of features</p>
            </div>
            
            <!-- Expandable Features Section -->
            <div class="expandable-section">
                <div class="section-card">
                    <div class="section-header">
                        <h3>Account Management</h3>
                        <span class="section-toggle"><i class="fas fa-chevron-down"></i></span>
                    </div>
                    <div class="section-content">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-wallet"></i></div>
                            <h3>Account Management</h3>
                            <p>Easily create, manage, and monitor all types of bank accounts with our intuitive interface. Our system supports savings, checking, fixed deposit, and specialized accounts with customizable features.</p>
                            <p>Advanced features include automated account categorization, spending analysis, and personalized financial insights to help you make better financial decisions.</p>
                        </div>
                    </div>
                </div>
                
                <div class="section-card">
                    <div class="section-header">
                        <h3>Secure Transactions</h3>
                        <span class="section-toggle"><i class="fas fa-chevron-down"></i></span>
                    </div>
                    <div class="section-content">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                            <h3>Secure Transactions</h3>
                            <p>Advanced encryption and security protocols ensure all your transactions are completely safe. We use 256-bit SSL encryption, multi-factor authentication, and real-time fraud monitoring.</p>
                            <p>Our system includes biometric authentication options, transaction limits, and instant alerts for any suspicious activity to give you complete peace of mind.</p>
                        </div>
                    </div>
                </div>
                
                <div class="section-card">
                    <div class="section-header">
                        <h3>Financial Analytics</h3>
                        <span class="section-toggle"><i class="fas fa-chevron-down"></i></span>
                    </div>
                    <div class="section-content">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                            <h3>Financial Analytics</h3>
                            <p>Get detailed insights into your financial activities with comprehensive reporting tools. Track your spending patterns, income trends, and investment performance.</p>
                            <p>Our analytics dashboard provides visual representations of your financial health, predictive budgeting, and personalized recommendations to help you achieve your financial goals.</p>
                        </div>
                    </div>
                </div>
                
                <div class="section-card">
                    <div class="section-header">
                        <h3>Online Banking</h3>
                        <span class="section-toggle"><i class="fas fa-chevron-down"></i></span>
                    </div>
                    <div class="section-content">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-globe"></i></div>
                            <h3>Online Banking</h3>
                            <p>Access your accounts and perform transactions anytime, anywhere with our web platform. Transfer funds, pay bills, and manage your finances with just a few clicks.</p>
                            <p>Our online banking platform supports multiple currencies, scheduled transactions, and integration with popular payment gateways for a seamless banking experience.</p>
                        </div>
                    </div>
                </div>
                
                <div class="section-card">
                    <div class="section-header">
                        <h3>Mobile Integration</h3>
                        <span class="section-toggle"><i class="fas fa-chevron-down"></i></span>
                    </div>
                    <div class="section-content">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-mobile-alt"></i></div>
                            <h3>Mobile Integration</h3>
                            <p>Seamlessly manage your finances on the go with our mobile-responsive design. Our mobile app offers all the features of our web platform optimized for smartphones.</p>
                            <p>Enjoy features like mobile check deposit, QR code payments, location-based services, and push notifications for important account activities.</p>
                        </div>
                    </div>
                </div>
                
                <div class="section-card">
                    <div class="section-header">
                        <h3>Fast Processing</h3>
                        <span class="section-toggle"><i class="fas fa-chevron-down"></i></span>
                    </div>
                    <div class="section-content">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                            <h3>Fast Processing</h3>
                            <p>Experience lightning-fast transaction processing with our optimized backend systems. Our infrastructure ensures minimal latency and maximum uptime.</p>
                            <p>With our advanced processing capabilities, enjoy instant fund transfers, real-time balance updates, and quick loan approvals without unnecessary delays.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Rates Section -->
    <section class="enhanced-rates" id="rates">
        <div class="container">
            <div class="section-title">
                <h2>Current Rates</h2>
                <p>Stay updated with our latest financial rates</p>
            </div>
            
            <div class="rates-tabs">
                <button class="tab-btn active" data-tab="exchange">Exchange Rates</button>
                <button class="tab-btn" data-tab="deposit">Deposit Rates</button>
                <button class="tab-btn" data-tab="loan">Loan Rates</button>
            </div>
            
            <div class="tab-content active" id="exchange-tab">
                <div class="rates-grid">
                    <div class="rate-card-enhanced">
                        <h3>Major Currencies</h3>
                        <div class="rate-item">
                            <span>USD to EUR</span>
                            <span class="rate-value">0.92</span>
                        </div>
                        <div class="rate-item">
                            <span>USD to GBP</span>
                            <span class="rate-value">0.79</span>
                        </div>
                        <div class="rate-item">
                            <span>USD to JPY</span>
                            <span class="rate-value">149.25</span>
                        </div>
                        <div class="rate-item">
                            <span>USD to CAD</span>
                            <span class="rate-value">1.36</span>
                        </div>
                        <div class="rate-item">
                            <span>USD to AUD</span>
                            <span class="rate-value">1.56</span>
                        </div>
                    </div>
                    <div class="rate-card-enhanced">
                        <h3>Asian Currencies</h3>
                        <div class="rate-item">
                            <span>USD to INR</span>
                            <span class="rate-value">83.12</span>
                        </div>
                        <div class="rate-item">
                            <span>USD to CNY</span>
                            <span class="rate-value">7.28</span>
                        </div>
                        <div class="rate-item">
                            <span>USD to SGD</span>
                            <span class="rate-value">1.36</span>
                        </div>
                        <div class="rate-item">
                            <span>USD to MYR</span>
                            <span class="rate-value">4.69</span>
                        </div>
                        <div class="rate-item">
                            <span>USD to THB</span>
                            <span class="rate-value">36.45</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-content" id="deposit-tab">
                <div class="rates-grid">
                    <div class="rate-card-enhanced">
                        <h3>Savings Accounts</h3>
                        <div class="rate-item">
                            <span>Regular Savings</span>
                            <span class="rate-value">2.5%</span>
                        </div>
                        <div class="rate-item">
                            <span>Premium Savings</span>
                            <span class="rate-value">3.8%</span>
                        </div>
                        <div class="rate-item">
                            <span>Senior Citizen</span>
                            <span class="rate-value">4.2%</span>
                        </div>
                        <div class="rate-item">
                            <span>Children's Account</span>
                            <span class="rate-value">3.5%</span>
                        </div>
                    </div>
                    <div class="rate-card-enhanced">
                        <h3>Fixed Deposits</h3>
                        <div class="rate-item">
                            <span>3 Months</span>
                            <span class="rate-value">4.0%</span>
                        </div>
                        <div class="rate-item">
                            <span>6 Months</span>
                            <span class="rate-value">4.5%</span>
                        </div>
                        <div class="rate-item">
                            <span>1 Year</span>
                            <span class="rate-value">5.2%</span>
                        </div>
                        <div class="rate-item">
                            <span>5 Years</span>
                            <span class="rate-value">6.5%</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-content" id="loan-tab">
                <div class="rates-grid">
                    <div class="rate-card-enhanced">
                        <h3>Personal Loans</h3>
                        <div class="rate-item">
                            <span>Salary Account Holders</span>
                            <span class="rate-value">8.5%</span>
                        </div>
                        <div class="rate-item">
                            <span>Regular Customers</span>
                            <span class="rate-value">10.2%</span>
                        </div>
                        <div class="rate-item">
                            <span>Business Loan</span>
                            <span class="rate-value">9.8%</span>
                        </div>
                        <div class="rate-item">
                            <span>Education Loan</span>
                            <span class="rate-value">7.5%</span>
                        </div>
                    </div>
                    <div class="rate-card-enhanced">
                        <h3>Home & Auto Loans</h3>
                        <div class="rate-item">
                            <span>Home Loan</span>
                            <span class="rate-value">6.8%</span>
                        </div>
                        <div class="rate-item">
                            <span>Car Loan</span>
                            <span class="rate-value">7.5%</span>
                        </div>
                        <div class="rate-item">
                            <span>Two-Wheeler Loan</span>
                            <span class="rate-value">9.2%</span>
                        </div>
                        <div class="rate-item">
                            <span>Home Renovation</span>
                            <span class="rate-value">8.5%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Text with Image Slider Section -->
    <section class="text-image-slider">
        <div class="container">
            <div class="text-image-container">
                <div class="text-content">
                    <h2>Our Banking Technology</h2>
                    <p>At Losers Bank, we leverage cutting-edge technology to provide you with the most secure and efficient banking experience. Our systems are built with the latest security protocols and user-friendly interfaces.</p>
                    <p>We continuously innovate to bring you features that make managing your finances simpler, faster, and more secure than ever before.</p>
                    <a href="#" class="btn">Learn More About Our Technology</a>
                </div>
                <div class="image-slider-container">
                    <div class="image-slider">
                        <div class="image-slide">
                           <img src="https://www.opploans.com/wp-content/uploads/online-banking.jpg" alt="Losers Bank Logo" class="bank-logo">
                        </div>
                        <div class="image-slide">
                            <img src="https://adaderanaenglish.s3.amazonaws.com/1753348810-credit-card-6.jpg" alt="Losers Bank Logo" class="bank-logo"> 
                        </div>
                        <div class="image-slide">
                           <img src="https://images.ctfassets.net/8dreszsahte7/4zmPlvqjh7MGJUUY4H3aYa/e213441c2f3667f29d9029b37b8f7e6e/pexels-tima-miroshnichenko-6266283_1__1_.png" alt="Losers Bank Logo" class="bank-logo"> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Tools Section -->
    <section class="quick-tools" id="tools">
        <div class="container">
            <div class="section-title">
                <h2>Financial Tools</h2>
                <p>Use our calculators and tools for better financial planning</p>
            </div>
            <div class="tools-grid">
                <div class="tool-card" data-tool="emi">
                    <div class="tool-icon"><i class="fas fa-calculator"></i></div>
                    <h3>EMI Calculator</h3>
                </div>
                <div class="tool-card" data-tool="investment">
                    <div class="tool-icon"><i class="fas fa-chart-pie"></i></div>
                    <h3>Investment Planner</h3>
                </div>
                <div class="tool-card" data-tool="home">
                    <div class="tool-icon"><i class="fas fa-home"></i></div>
                    <h3>Home Affordability</h3>
                </div>
                <div class="tool-card" data-tool="currency">
                    <div class="tool-icon"><i class="fas fa-retweet"></i></div>
                    <h3>Currency Converter</h3>
                </div>
                <div class="tool-card" data-tool="education">
                    <div class="tool-icon"><i class="fas fa-graduation-cap"></i></div>
                    <h3>Education Planner</h3>
                </div>
                <div class="tool-card" data-tool="retirement">
                    <div class="tool-icon"><i class="fas fa-umbrella"></i></div>
                    <h3>Retirement Planner</h3>
                </div>
            </div>
            
            <!-- Tool Details -->
            <div class="tool-details" id="emi-details">
                <h3>EMI Calculator</h3>
                <p>Calculate your Equated Monthly Installment (EMI) for loans with our easy-to-use calculator. Simply enter the loan amount, interest rate, and tenure to get your monthly payment amount.</p>
                <div class="calculator-form">
                    <div class="form-group">
                        <label for="loan-amount">Loan Amount ($)</label>
                        <input type="number" id="loan-amount" placeholder="Enter loan amount">
                    </div>
                    <div class="form-group">
                        <label for="interest-rate">Interest Rate (%)</label>
                        <input type="number" id="interest-rate" placeholder="Enter interest rate">
                    </div>
                    <div class="form-group">
                        <label for="loan-tenure">Loan Tenure (months)</label>
                        <input type="number" id="loan-tenure" placeholder="Enter loan tenure">
                    </div>
                    <button class="btn" id="calculate-emi">Calculate EMI</button>
                    <div id="emi-result"></div>
                </div>
            </div>
            
            <div class="tool-details" id="investment-details">
                <h3>Investment Planner</h3>
                <p>Plan your investments with our comprehensive investment calculator. Determine how much you need to invest regularly to reach your financial goals.</p>
                <p>This tool helps you understand the power of compound interest and how regular investments can grow over time.</p>
            </div>
            
            <div class="tool-details" id="home-details">
                <h3>Home Affordability Calculator</h3>
                <p>Determine how much house you can afford based on your income, expenses, and down payment. Our calculator considers all factors to give you a realistic estimate.</p>
                <p>This tool helps you make informed decisions when planning to purchase a home.</p>
            </div>
            
            <div class="tool-details" id="currency-details">
                <h3>Currency Converter</h3>
                <p>Convert between different currencies with real-time exchange rates. Our converter supports all major world currencies and updates rates regularly.</p>
                <p>This tool is perfect for travelers, international businesses, and anyone dealing with multiple currencies.</p>
            </div>
            
            <div class="tool-details" id="education-details">
                <h3>Education Planner</h3>
                <p>Plan for education expenses with our education cost calculator. Estimate future education costs and determine how much you need to save to meet those expenses.</p>
                <p>This tool considers inflation in education costs and helps you create a savings plan for your children's education.</p>
            </div>
            
            <div class="tool-details" id="retirement-details">
                <h3>Retirement Planner</h3>
                <p>Plan for your retirement with our comprehensive retirement calculator. Determine how much you need to save to maintain your desired lifestyle after retirement.</p>
                <p>This tool considers factors like current age, retirement age, life expectancy, inflation, and expected returns on investments.</p>
            </div>
        </div>
    </section>

    <!-- Team Section - Updated Layout -->
    <section class="team" id="team">
        <div class="container">
            <div class="section-title">
                <h2>Our Team</h2>
                <p>Meet the talented individuals behind the Losers system</p>
            </div>
            <div class="team-grid">
                <!-- Supervisor at top/center -->
                <div class="supervisor-container">
                    <div class="team-member supervisor" data-member="4">
                        <div class="member-img">
                            <img src="https://salman.rfnhsc.com/bank/uploads/tasmin.jpg" alt="Tasmin Akther">
                        </div>
                        <div class="member-info">
                            <h3>Tasmin Akther</h3>
                            <p>Project Supervisor</p>
                        </div>
                    </div>
                </div>
                
                <!-- Other team members in a row -->
                <div class="team-members-row">
                    <div class="team-member" data-member="1">
                        <div class="member-img">
                            <img src="https://salman.rfnhsc.com/bank/uploads/salman.png" alt="Sayed Mahbub Salman">
                        </div>
                        <div class="member-info">
                            <h3>Sayed Mahbub Salman</h3>
                            <p>CSE 031 08201</p>
                        </div>
                    </div>
                    <div class="team-member" data-member="2">
                        <div class="member-img">
                            <img src="https://salman.rfnhsc.com/bank/uploads/2.png" alt="Nur Mohammad Akash">
                        </div>
                        <div class="member-info">
                            <h3>Nur Mohammad Akash</h3>
                            <p>CSE 031 08182</p>
                        </div>
                    </div>
                    <div class="team-member" data-member="3">
                        <div class="member-img">
                            <img src="https://salman.rfnhsc.com/bank/uploads/1.png" alt="Md Shifuddin">
                        </div>
                        <div class="member-info">
                            <h3>Md Shifuddin</h3>
                            <p>CSE 031 08165</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Details Section -->
    <section class="team-details" id="team-details">
        <div class="container">
            <div class="details-container">
                <div class="details-img" id="details-img">
                    <img src="https://salman.rfnhsc.com/bank/uploads/tasmin.jpg" alt="Team Member">
                </div>
                <div class="details-content">
                    <h2 id="details-name">Tasmin Akther</h2>
                    <p class="role" id="details-role">Project Supervisor</p>
                    <p id="details-bio">Tasmin Akther provides academic guidance and industry insights to ensure the project meets both educational and professional standards. With extensive experience in software engineering and financial systems, he mentors the team through complex technical challenges.</p>
                    <p><strong>Skills:</strong> <span id="details-skills">Software Engineering, Project Management, Financial Systems, Research Methodology</span></p>
                    <p><strong>Responsibilities:</strong> <span id="details-responsibilities">Academic supervision, project guidance, quality assurance, industry alignment</span></p>
                    <button class="btn back-btn" id="back-btn">Back to Team</button>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq" id="faq">
        <div class="container">
            <div class="section-title">
                <h2>Frequently Asked Questions</h2>
                <p>Find answers to common questions about our bank and services</p>
            </div>
            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How do I open an account with Losers Bank?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>You can open an account with Losers Bank by visiting any of our branches with valid identification documents, or you can apply online through our website. The online process takes about 10-15 minutes and requires digital copies of your ID and proof of address.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>What are your banking hours?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>Our branch hours are Monday to Friday from 9:00 AM to 5:00 PM, and Saturdays from 9:00 AM to 1:00 PM. Selected branches in metropolitan areas have extended hours until 7:00 PM on weekdays. Our digital banking services are available 24/7.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How can I reset my online banking password?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>To reset your online banking password, click on the "Forgot Password" link on the login page. You will need to verify your identity through registered mobile number or email. Alternatively, you can visit any branch with your identification documents to reset your password.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>What security measures do you have in place?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>We employ multiple layers of security including 256-bit SSL encryption, two-factor authentication, biometric verification for mobile banking, real-time fraud monitoring, and secure socket layer technology. We never ask for your password or OTP via email or phone.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How can I find the nearest branch or ATM?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>You can use our branch locator tool on our website or mobile app to find the nearest branch or ATM. Simply enter your location or allow the app to access your location services to see all nearby Losers Bank facilities with directions and operating hours.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Announcements Section -->
    <section class="announcements" id="announcements">
        <div class="container">
            <div class="section-title">
                <h2>Announcements</h2>
                <p>Stay informed with our latest updates and news</p>
            </div>
            <div class="announcement-container">
                <div class="announcement-item" data-announcement="1">
                    <div class="announcement-date">October 15, 2023</div>
                    <h3>New Mobile Banking App Launched</h3>
                    <p>We're excited to announce the launch of our new mobile banking app with enhanced features and improved security.</p>
                    <div class="announcement-details" id="announcement-1">
                        <p>The new Losers Bank mobile app includes features like biometric login, instant money transfers, bill payments, investment tracking, and personalized financial insights. The app is available for both iOS and Android devices.</p>
                        <p>Key improvements in the new version:</p>
                        <ul>
                            <li>Faster transaction processing</li>
                            <li>Enhanced security with facial recognition</li>
                            <li>Personalized dashboard with spending analytics</li>
                            <li>Integration with popular payment platforms</li>
                            <li>24/7 customer support through in-app chat</li>
                        </ul>
                        <p>Download the app today from the App Store or Google Play Store.</p>
                    </div>
                </div>
                <div class="announcement-item" data-announcement="2">
                    <div class="announcement-date">October 10, 2023</div>
                    <h3>Extended Branch Hours</h3>
                    <p>Selected branches will now remain open until 7 PM on weekdays to better serve our customers.</p>
                    <div class="announcement-details" id="announcement-2">
                        <p>To better serve our customers' busy schedules, we're extending operating hours at 25 branches across major metropolitan areas. These branches will now be open from 9 AM to 7 PM on weekdays.</p>
                        <p>The extended hours will allow working professionals to visit our branches after work hours for services that require in-person assistance.</p>
                        <p>Check our website or mobile app to see if your local branch is included in this initiative.</p>
                    </div>
                </div>
                <div class="announcement-item" data-announcement="3">
                    <div class="announcement-date">October 5, 2023</div>
                    <h3>Security Awareness Campaign</h3>
                    <p>Join our security awareness campaign to learn how to protect yourself from online banking fraud.</p>
                    <div class="announcement-details" id="announcement-3">
                        <p>We're launching a comprehensive security awareness campaign to help our customers protect themselves from online banking fraud and scams.</p>
                        <p>The campaign includes:</p>
                        <ul>
                            <li>Free workshops at all branches</li>
                            <li>Online webinars on cybersecurity best practices</li>
                            <li>Educational materials on identifying phishing attempts</li>
                            <li>Tips for creating strong passwords and securing devices</li>
                        </ul>
                        <p>Remember, Losers Bank will never ask for your password, PIN, or OTP via email, phone, or text message. Always verify the authenticity of any communication claiming to be from the bank.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Security Section -->
    <section class="security">
        <div class="container">
            <div class="section-title">
                <h2>Security First</h2>
                <p>Your security is our top priority</p>
            </div>
            <div class="security-content">
                <p>We employ industry-leading security measures to protect your financial data and transactions.</p>
                <div class="security-icons">
                    <div class="security-icon"><i class="fas fa-lock"></i></div>
                    <div class="security-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="security-icon"><i class="fas fa-fingerprint"></i></div>
                    <div class="security-icon"><i class="fas fa-user-shield"></i></div>
                </div>
                <p>Always verify our official website and never share your banking credentials with anyone.</p>
                <a href="#" class="btn">Security Tips</a>
            </div>
        </div>
    </section>
    
    <!-- Fraud Alert -->
    <div class="fraud-alert" id="fraud-alert">
        <div class="container">
            <p><i class="fas fa-exclamation-triangle"></i> ALERT: Beware of fraudulent calls and emails pretending to be from Losers Bank. We never ask for your password or OTP.</p>
            <button class="close-alert" id="close-alert"><i class="fas fa-times"></i></button>
        </div>
    </div>
    
    <!-- Footer -->
    <footer id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Losers Bank</h3>
                    <p>Advanced bank management solutions for modern financial institutions.</p>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <a href="#home">Home</a>
                    <a href="#features">Features</a>
                    <a href="#rates">Rates</a>
                    <a href="#team">Our Team</a>
                    <a href="#announcements">Announcements</a>
                    <a href="#contact">Contact</a>
                </div>
                <div class="footer-column">
                    <h3>Services</h3>
                    <a href="#">Personal Banking</a>
                    <a href="#">Business Banking</a>
                    <a href="#">Loans & Credit</a>
                    <a href="#">Investment Plans</a>
                    <a href="#">Insurance</a>
                </div>
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <p><i class="fas fa-envelope"></i> Email: info@losersbank.com</p>
                    <p><i class="fas fa-phone"></i> Phone: +880 1609 506363</p>
                    <p><i class="fas fa-map-marker-alt"></i> Address: PCIU, South Khulshi, Chattogram.</p>
                </div>
            </div>
            <div class="copyright">
                <p class="developed"> &copy; 2025 Losers Bank. All rights reserved. | Developed by The "Losers" Team</p>

            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script>
        // Initialize GSAP animations
        gsap.registerPlugin(ScrollTrigger);
        
        // Hero section animation
        gsap.from(".hero h1", {
            duration: 1,
            y: 50,
            opacity: 0,
            ease: "power3.out"
        });
        
        gsap.from(".hero p", {
            duration: 1,
            y: 30,
            opacity: 0,
            delay: 0.3,
            ease: "power3.out"
        });
        
        gsap.from(".hero-btns", {
            duration: 1,
            y: 30,
            opacity: 0,
            delay: 0.6,
            ease: "power3.out"
        });
        
        // Features section animation
        gsap.from(".feature-card", {
            scrollTrigger: {
                trigger: ".features",
                start: "top 80%",
                end: "bottom 20%",
                toggleActions: "play none none reverse"
            },
            y: 50,
            opacity: 0,
            duration: 0.8,
            stagger: 0.2,
            ease: "power3.out"
        });
        
        // Quick access section animation
        gsap.from(".access-card", {
            scrollTrigger: {
                trigger: ".quick-access",
                start: "top 80%",
                end: "bottom 20%",
                toggleActions: "play none none reverse"
            },
            y: 30,
            opacity: 0,
            duration: 0.6,
            stagger: 0.1,
            ease: "power3.out"
        });
        
        // Team section animation
        gsap.from(".team-member", {
            scrollTrigger: {
                trigger: ".team",
                start: "top 80%",
                end: "bottom 20%",
                toggleActions: "play none none reverse"
            },
            y: 50,
            opacity: 0,
            duration: 0.8,
            stagger: 0.2,
            ease: "power3.out"
        });
        
        // Rates section animation
        gsap.from(".rate-card-enhanced", {
            scrollTrigger: {
                trigger: ".enhanced-rates",
                start: "top 80%",
                end: "bottom 20%",
                toggleActions: "play none none reverse"
            },
            y: 30,
            opacity: 0,
            duration: 0.6,
            stagger: 0.1,
            ease: "power3.out"
        });
        
        // FAQ section animation
        gsap.from(".faq-item", {
            scrollTrigger: {
                trigger: ".faq",
                start: "top 80%",
                end: "bottom 20%",
                toggleActions: "play none none reverse"
            },
            y: 30,
            opacity: 0,
            duration: 0.6,
            stagger: 0.1,
            ease: "power3.out"
        });

        // Theme Toggle
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = themeToggle.querySelector('i');
        
        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-theme');
            
            if (document.body.classList.contains('dark-theme')) {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        });
        
        // FAQ Functionality
        const faqQuestions = document.querySelectorAll('.faq-question');
        
        faqQuestions.forEach(question => {
            question.addEventListener('click', function() {
                const answer = this.nextElementSibling;
                const toggle = this.querySelector('.faq-toggle');
                
                // Toggle active class
                answer.classList.toggle('active');
                toggle.classList.toggle('active');
                
                // Close other open FAQs
                faqQuestions.forEach(otherQuestion => {
                    if (otherQuestion !== this) {
                        const otherAnswer = otherQuestion.nextElementSibling;
                        const otherToggle = otherQuestion.querySelector('.faq-toggle');
                        otherAnswer.classList.remove('active');
                        otherToggle.classList.remove('active');
                    }
                });
            });
        });

        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Team member data
        const teamMembers = {
            1: {
                name: "Sayed Mahbub Salman",
                role: "Project Manager & Lead Developer",
                bio: "Salman is a seasoned software engineer with over 8 years of experience in financial technology. He specializes in system architecture and project management, ensuring that our bank management system meets the highest standards of security and performance.",
                skills: "PHP, JavaScript, MySQL, System Architecture, Project Management",
                responsibilities: "Overseeing project development, system design, code review, client communication",
                avatar: "https://salman.rfnhsc.com/bank/uploads/salman.png"
            },
            2: {
                name: "Nur Mohammad Akash",
                role: "Frontend Developer & UI/UX Designer",
                bio: "Akash is a creative frontend developer with a keen eye for design and user experience. He specializes in creating intuitive interfaces that make complex banking operations simple and accessible for all users.",
                skills: "HTML5, CSS3, JavaScript, React, UI/UX Design",
                responsibilities: "Frontend development, user interface design, responsive layouts, user experience optimization",
                avatar: "https://salman.rfnhsc.com/bank/uploads/2.png"
            },
            3: {
                name: "Md Shifuddin",
                role: "Backend Developer & Database Specialist",
                bio: "Shifuddin is a backend specialist with expertise in database design and server-side programming. He ensures that our bank management system runs smoothly, efficiently, and securely at all times.",
                skills: "Java, Python, SQL, Database Design, API Development",
                responsibilities: "Backend development, database management, API integration, system security",
                avatar: "https://salman.rfnhsc.com/bank/uploads/1.png"
            },
            4: {
                name: "Tasmin Akther",
                role: "Project Supervisor",
                bio: "Tasmin Akther provides academic guidance and industry insights to ensure the project meets both educational and professional standards. With extensive experience in software engineering and financial systems, he mentors the team through complex technical challenges.",
                skills: "Software Engineering, Project Management, Financial Systems, Research Methodology",
                responsibilities: "Academic supervision, project guidance, quality assurance, industry alignment",
                avatar: "https://salman.rfnhsc.com/bank/uploads/tasmin.jpg"
            }
        };

        // Team member click handler
        const teamMembersElements = document.querySelectorAll('.team-member');
        const teamSection = document.getElementById('team');
        const teamDetailsSection = document.getElementById('team-details');
        const backBtn = document.getElementById('back-btn');
        
        teamMembersElements.forEach(member => {
            member.addEventListener('click', function() {
                const memberId = this.getAttribute('data-member');
                const memberData = teamMembers[memberId];
                
                document.getElementById('details-name').textContent = memberData.name;
                document.getElementById('details-role').textContent = memberData.role;
                document.getElementById('details-bio').textContent = memberData.bio;
                document.getElementById('details-skills').textContent = memberData.skills;
                document.getElementById('details-responsibilities').textContent = memberData.responsibilities;
                document.getElementById('details-img').innerHTML = `<img src="${memberData.avatar}" alt="${memberData.name}">`;
                
                teamSection.style.display = 'none';
                teamDetailsSection.classList.add('active');
                
                // Scroll to top of details section
                window.scrollTo({
                    top: teamDetailsSection.offsetTop - 100,
                    behavior: 'smooth'
                });
            });
        });
        
        // Back button handler
        backBtn.addEventListener('click', function() {
            teamDetailsSection.classList.remove('active');
            teamSection.style.display = 'block';
            
            // Scroll back to team section
            window.scrollTo({
                top: teamSection.offsetTop - 100,
                behavior: 'smooth'
            });
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Initialize Slick Slider
        $(document).ready(function(){
            $('.offer-slider').slick({
                dots: true,
                infinite: true,
                speed: 500,
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 4000,
                adaptiveHeight: true
            });
            
            // Initialize Image Slider
            $('.image-slider').slick({
                dots: true,
                infinite: true,
                speed: 500,
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 3000,
                adaptiveHeight: true
            });
        });

        // Rates Tabs
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons and contents
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Show corresponding content
                const tabId = this.getAttribute('data-tab');
                document.getElementById(`${tabId}-tab`).classList.add('active');
            });
        });

        // Live Chat Functionality
        const liveChat = document.getElementById('live-chat');
        const chatWindow = document.getElementById('chat-window');
        const closeChat = document.getElementById('close-chat');
        const chatBody = document.getElementById('chat-body');
        const chatInput = document.getElementById('chat-input');
        const chatSend = document.getElementById('chat-send');
        
        liveChat.addEventListener('click', function() {
            chatWindow.style.display = 'flex';
        });
        
        closeChat.addEventListener('click', function() {
            chatWindow.style.display = 'none';
        });
        
        chatSend.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
        
        function sendMessage() {
            const message = chatInput.value.trim();
            if (message) {
                // Add user message
                const userMessage = document.createElement('div');
                userMessage.classList.add('chat-message', 'user');
                userMessage.textContent = message;
                chatBody.appendChild(userMessage);
                
                // Clear input
                chatInput.value = '';
                
                // Scroll to bottom
                chatBody.scrollTop = chatBody.scrollHeight;
                
                // Simulate bot response after a delay
                setTimeout(function() {
                    const botMessage = document.createElement('div');
                    botMessage.classList.add('chat-message', 'bot');
                    
                    // Simple response logic
                    if (message.toLowerCase().includes('account')) {
                        botMessage.textContent = "For account-related queries, please visit our Internet Banking portal or contact our customer service at 1-800-LOSERS.";
                    } else if (message.toLowerCase().includes('loan')) {
                        botMessage.textContent = "We offer various loan products. You can check our current rates on this page or apply online through our portal.";
                    } else if (message.toLowerCase().includes('card')) {
                        botMessage.textContent = "For credit or debit card issues, please call our 24/7 card services at 1-800-CARDHELP.";
                    } else {
                        botMessage.textContent = "Thank you for your message. Our customer service team will get back to you shortly. Is there anything specific I can help you with?";
                    }
                    
                    chatBody.appendChild(botMessage);
                    chatBody.scrollTop = chatBody.scrollHeight;
                }, 1000);
            }
        }

        // Fraud Alert Close
        const closeAlert = document.getElementById('close-alert');
        const fraudAlert = document.getElementById('fraud-alert');
        
        closeAlert.addEventListener('click', function() {
            fraudAlert.style.display = 'none';
        });

        // NEW FUNCTIONALITY FOR EXPANDABLE SECTIONS
        
        // Expandable Section Cards
        const sectionHeaders = document.querySelectorAll('.section-header');
        
        sectionHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const content = this.nextElementSibling;
                const toggle = this.querySelector('.section-toggle');
                
                // Toggle active class
                content.classList.toggle('active');
                toggle.classList.toggle('active');
                
                // Close other open sections
                sectionHeaders.forEach(otherHeader => {
                    if (otherHeader !== this) {
                        const otherContent = otherHeader.nextElementSibling;
                        const otherToggle = otherHeader.querySelector('.section-toggle');
                        otherContent.classList.remove('active');
                        otherToggle.classList.remove('active');
                    }
                });
            });
        });
        
        // Expandable Announcements
        const announcementItems = document.querySelectorAll('.announcement-item');
        
        announcementItems.forEach(item => {
            item.addEventListener('click', function() {
                const announcementId = this.getAttribute('data-announcement');
                const details = document.getElementById(`announcement-${announcementId}`);
                
                // Toggle active class
                details.classList.toggle('active');
                
                // Close other open announcements
                announcementItems.forEach(otherItem => {
                    if (otherItem !== this) {
                        const otherAnnouncementId = otherItem.getAttribute('data-announcement');
                        const otherDetails = document.getElementById(`announcement-${otherAnnouncementId}`);
                        otherDetails.classList.remove('active');
                    }
                });
            });
        });
        
        // Expandable Tools
        const toolCards = document.querySelectorAll('.tool-card');
        let activeTool = null;
        
        toolCards.forEach(card => {
            card.addEventListener('click', function() {
                const toolId = this.getAttribute('data-tool');
                const details = document.getElementById(`${toolId}-details`);
                
                // If clicking the same tool, close it
                if (activeTool === toolId) {
                    details.classList.remove('active');
                    activeTool = null;
                } else {
                    // Close previously active tool
                    if (activeTool) {
                        const prevDetails = document.getElementById(`${activeTool}-details`);
                        prevDetails.classList.remove('active');
                    }
                    
                    // Open new tool
                    details.classList.add('active');
                    activeTool = toolId;
                    
                    // Scroll to the tool details
                    details.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });
        });
        
        // EMI Calculator Functionality
        document.getElementById('calculate-emi').addEventListener('click', function() {
            const loanAmount = parseFloat(document.getElementById('loan-amount').value);
            const interestRate = parseFloat(document.getElementById('interest-rate').value);
            const loanTenure = parseInt(document.getElementById('loan-tenure').value);
            
            if (isNaN(loanAmount) || isNaN(interestRate) || isNaN(loanTenure)) {
                document.getElementById('emi-result').innerHTML = '<p style="color: var(--danger);">Please enter valid numbers for all fields.</p>';
                return;
            }
            
            const monthlyInterestRate = interestRate / 12 / 100;
            const emi = loanAmount * monthlyInterestRate * Math.pow(1 + monthlyInterestRate, loanTenure) / (Math.pow(1 + monthlyInterestRate, loanTenure) - 1);
            const totalPayment = emi * loanTenure;
            const totalInterest = totalPayment - loanAmount;
            
            document.getElementById('emi-result').innerHTML = `
                <div style="margin-top: 20px; padding: 15px; background-color: var(--light); border-radius: 8px;">
                    <h4>EMI Calculation Results</h4>
                    <p><strong>Monthly EMI:</strong> $${emi.toFixed(2)}</p>
                    <p><strong>Total Payment:</strong> $${totalPayment.toFixed(2)}</p>
                    <p><strong>Total Interest:</strong> $${totalInterest.toFixed(2)}</p>
                </div>
            `;
        });
    </script>
</body>
</html>