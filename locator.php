<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOSERS BANK - Branch Locator</title>
        <link rel="icon" type="image/png" sizes="32x32" href="https://salman.rfnhsc.com/bank/uploads/losers.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://salman.rfnhsc.com/bank/uploads/losers.png">
<link rel="shortcut icon" href="https://salman.rfnhsc.com/bank/uploads/losers.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            background: linear-gradient(135deg, #1a2a6c, #b21f1f);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .logo i {
            font-size: 40px;
            color: white;
        }
        
        .bank-name {
            font-size: 32px;
            font-weight: 700;
            color: #1a2a6c;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .bank-tagline {
            font-size: 16px;
            color: #b21f1f;
            font-weight: 500;
        }
        
        .page-title {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            font-size: 36px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .page-subtitle {
            text-align: center;
            color: white;
            margin-bottom: 40px;
            font-size: 20px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .main-content {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .branch-list {
            flex: 1;
            background: rgba(255, 255, 255, 0.9);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            max-height: 600px;
            overflow-y: auto;
        }
        
        .branch-list h2 {
            color: #1a2a6c;
            margin-bottom: 20px;
            font-size: 24px;
            border-bottom: 2px solid #fdbb2d;
            padding-bottom: 10px;
        }
        
        .search-box {
            margin-bottom: 20px;
        }
        
        .search-box input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .branch-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .branch-item:hover {
            background: rgba(26, 42, 108, 0.05);
        }
        
        .branch-item.active {
            background: rgba(26, 42, 108, 0.1);
            border-left: 4px solid #1a2a6c;
        }
        
        .branch-name {
            font-weight: 700;
            color: #1a2a6c;
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .branch-address {
            color: #555;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .branch-phone {
            color: #b21f1f;
            font-size: 14px;
        }
        
        .branch-details {
            flex: 2;
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            min-height: 600px;
        }
        
        .branch-details h2 {
            color: #1a2a6c;
            margin-bottom: 20px;
            font-size: 28px;
            border-bottom: 2px solid #fdbb2d;
            padding-bottom: 10px;
        }
        
        .detail-section {
            margin-bottom: 25px;
        }
        
        .detail-section h3 {
            color: #1a2a6c;
            margin-bottom: 10px;
            font-size: 20px;
            display: flex;
            align-items: center;
        }
        
        .detail-section h3 i {
            margin-right: 10px;
            color: #b21f1f;
        }
        
        .branch-map {
            height: 250px;
            background: #eee;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(26, 42, 108, 0.1), rgba(26, 42, 108, 0.2));
            color: #1a2a6c;
            font-weight: 600;
        }
        
        .contact-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .contact-item {
            flex: 1;
            min-width: 200px;
            padding: 15px;
            background: rgba(253, 187, 45, 0.1);
            border-radius: 8px;
            border-left: 4px solid #fdbb2d;
        }
        
        .contact-item h4 {
            color: #1a2a6c;
            margin-bottom: 8px;
            font-size: 16px;
        }
        
        .hours-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .hours-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
        }
        
        .hours-table tr:last-child td {
            border-bottom: none;
        }
        
        .hours-table .day {
            font-weight: 600;
            color: #1a2a6c;
        }
        
        .services-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }
        
        .service-item {
            padding: 10px;
            background: rgba(26, 42, 108, 0.05);
            border-radius: 5px;
            font-size: 14px;
        }
        
        .service-item i {
            margin-right: 8px;
            color: #b21f1f;
        }
        
        .no-branch-selected {
            text-align: center;
            padding: 50px 20px;
            color: #777;
        }
        
        .no-branch-selected i {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .chattogram-focus {
            background: rgba(253, 187, 45, 0.2);
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #fdbb2d;
        }
        
        .chattogram-focus h3 {
            color: #1a2a6c;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .chattogram-focus h3 i {
            margin-right: 10px;
            color: #b21f1f;
        }
        
        footer {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .footer-section {
            flex: 1;
            min-width: 250px;
            margin-bottom: 20px;
        }
        
        .footer-section h3 {
            color: #1a2a6c;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .footer-section p, .footer-section a {
            color: #555;
            margin-bottom: 8px;
            display: block;
            text-decoration: none;
        }
        
        .footer-section a:hover {
            color: #b21f1f;
        }
        
        .copyright {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #777;
            font-size: 14px;
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
            
            .branch-list {
                max-height: 400px;
            }
            
            .page-title {
                font-size: 28px;
            }
            
            .page-subtitle {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo-container">
                <div class="logo">
                    <link rel="icon" type="image/png" sizes="16x16" href="https://salman.rfnhsc.com/bank/uploads/losers.png">
                </div>
                <div>
                    <h1 href="https://salman.rfnhsc.com/bank/" class="bank-name">LOSERS BANK</h1>
                    <p class="bank-tagline">Your Trust, Our Responsibility - Serving Chattogram since 1995</p>
                </div>
            </div>
            <div>
                <p><i class="fas fa-phone"></i> +880 31-XXXXXX</p>
                <p><i class="fas fa-map-marker-alt"></i> Chattogram, Bangladesh</p>
            </div>
        </header>
        
        <h1 class="page-title">LOSERS BANK Branch Locator</h1>
        <p class="page-subtitle">Find your nearest LOSERS BANK branch in Chattogram and across Bangladesh. Click on any branch to view detailed information.</p>
        
        <div class="main-content">
            <div class="branch-list">
                <h2><i class="fas fa-map-marker-alt"></i> Our Branches</h2>
                
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search by branch name or location...">
                </div>
                
                <div class="branch-item active" data-branch="agrabad">
                    <div class="branch-name">Agrabad Main Branch</div>
                    <div class="branch-address">Agrabad Commercial Area, Chattogram</div>
                    <div class="branch-phone"><i class="fas fa-phone"></i> +880 31-625841</div>
                </div>
                
                <div class="branch-item" data-branch="khulsi">
                    <div class="branch-name">Khulsi Branch</div>
                    <div class="branch-address">Khulshi Residential Area, Chattogram</div>
                    <div class="branch-phone"><i class="fas fa-phone"></i> +880 31-671234</div>
                </div>
                
                <div class="branch-item" data-branch="bahaddarhat">
                    <div class="branch-name">Bahaddarhat Branch</div>
                    <div class="branch-address">Bahaddarhat Circle, Chattogram</div>
                    <div class="branch-phone"><i class="fas fa-phone"></i> +880 31-715678</div>
                </div>
                
                <div class="branch-item" data-branch="gEC">
                    <div class="branch-name">GEC Circle Branch</div>
                    <div class="branch-address">GEC Circle, Chattogram</div>
                    <div class="branch-phone"><i class="fas fa-phone"></i> +880 31-743219</div>
                </div>
                
                <div class="branch-item" data-branch="chawkbazar">
                    <div class="branch-name">Chawkbazar Branch</div>
                    <div class="branch-address">Chawkbazar, Chattogram</div>
                    <div class="branch-phone"><i class="fas fa-phone"></i> +880 31-632587</div>
                </div>
                
                <div class="branch-item" data-branch="pahartali">
                    <div class="branch-name">Pahartali Branch</div>
                    <div class="branch-address">Pahartali, Chattogram</div>
                    <div class="branch-phone"><i class="fas fa-phone"></i> +880 31-698745</div>
                </div>
                
                <div class="chattogram-focus">
                    <h3><i class="fas fa-map-marker-alt"></i> Chattogram Focus</h3>
                    <p>As a bank founded in Chattogram, we have the most extensive branch network in the port city with 12 locations serving your banking needs.</p>
                </div>
            </div>
            
            <div class="branch-details">
                <div id="branchContent">
                    <!-- Agrabad Branch Details -->
                    <div class="branch-detail active" id="agrabad-details">
                        <h2>Agrabad Main Branch</h2>
                        
                        <div class="branch-map">
                           <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14762.020219152446!2d91.79328343735845!3d22.334550872675162!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30acd8cfc396b825%3A0x2cfcfc117c7cd60e!2z4Keo4KeqIOCmqOCmgiDgpongpqTgp43gpqTgprAg4KaG4KaX4KeN4Kaw4Ka-4Kas4Ka-4KamIOCmk-Cmr-CmvOCmvuCmsOCnjeCmoSwg4Kaa4Kaf4KeN4Kaf4KaX4KeN4Kaw4Ka-4Kau!5e0!3m2!1sbn!2sbd!4v1762409780521!5m2!1sbn!2sbd" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        
                        <div class="contact-info">
                            <div class="contact-item">
                                <h4><i class="fas fa-map-marker-alt"></i> Address</h4>
                                <p>Agrabad Commercial Area,<br>Chattogram 4100, Bangladesh</p>
                            </div>
                            <div class="contact-item">
                                <h4><i class="fas fa-phone"></i> Contact</h4>
                                <p>+880 31-625841<br>+880 31-625842</p>
                            </div>
                            <div class="contact-item">
                                <h4><i class="fas fa-envelope"></i> Email</h4>
                                <p>agrabad@losersbank.com</p>
                            </div>
                            <div class="contact-item">
                                <h4><i class="fas fa-user-tie"></i> Branch Manager</h4>
                                <p>Mr. Abdul Karim</p>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h3><i class="far fa-clock"></i> Opening Hours</h3>
                            <table class="hours-table">
                                <tr>
                                    <td class="day">Sunday - Thursday</td>
                                    <td>9:00 AM - 5:00 PM</td>
                                </tr>
                                <tr>
                                    <td class="day">Friday</td>
                                    <td>9:00 AM - 12:30 PM, 2:30 PM - 5:00 PM</td>
                                </tr>
                                <tr>
                                    <td class="day">Saturday</td>
                                    <td>9:00 AM - 2:00 PM</td>
                                </tr>
                                <tr>
                                    <td class="day">Holidays</td>
                                    <td>Closed</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="detail-section">
                            <h3><i class="fas fa-concierge-bell"></i> Services Available</h3>
                            <div class="services-list">
                                <div class="service-item"><i class="fas fa-check"></i> Personal Banking</div>
                                <div class="service-item"><i class="fas fa-check"></i> Business Banking</div>
                                <div class="service-item"><i class="fas fa-check"></i> Loan Services</div>
                                <div class="service-item"><i class="fas fa-check"></i> Foreign Exchange</div>
                                <div class="service-item"><i class="fas fa-check"></i> ATM Services</div>
                                <div class="service-item"><i class="fas fa-check"></i> Online Banking</div>
                                <div class="service-item"><i class="fas fa-check"></i> Credit Cards</div>
                                <div class="service-item"><i class="fas fa-check"></i> Investment Services</div>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h3><i class="fas fa-info-circle"></i> Branch Information</h3>
                            <p>Our Agrabad Main Branch is the flagship branch of LOSERS BANK, located in the heart of Chattogram's commercial district. This branch offers full banking services with dedicated counters for corporate clients, NRB banking, and import-export financing.</p>
                            <p>Facilities: ATM, Security Vault, Disabled Access, Customer Lounge, Business Center</p>
                        </div>
                    </div>
                    
                    <!-- Other branch details would be here but hidden by default -->
                    <div class="no-branch-selected" id="noBranchSelected">
                        <i class="fas fa-map-marked-alt"></i>
                        <h3>Select a branch to view details</h3>
                        <p>Click on any branch from the list to see complete information including address, contact details, opening hours, and services.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <footer>
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Contact Us</h3>
                   <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14762.020219152446!2d91.79328343735845!3d22.334550872675162!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30acd8cfc396b825%3A0x2cfcfc117c7cd60e!2z4Keo4KeqIOCmqOCmgiDgpongpqTgp43gpqTgprAg4KaG4KaX4KeN4Kaw4Ka-4Kas4Ka-4KamIOCmk-Cmr-CmvOCmvuCmsOCnjeCmoSwg4Kaa4Kaf4KeN4Kaf4KaX4KeN4Kaw4Ka-4Kau!5e0!3m2!1sbn!2sbd!4v1762410226532!5m2!1sbn!2sbd" width="200" height="150" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <a href="#">Home</a>
                    <a href="#">About Us</a>
                    <a href="#">Services</a>
                    <a href="#">Branch Locator</a>
                    <a href="#">Contact</a>
                </div>
                
                <div class="footer-section">
                    <h3>Follow Us</h3>
                    <a href="#"><i class="fab fa-facebook"></i> Facebook</a>
                    <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
                    <a href="#"><i class="fab fa-linkedin"></i> LinkedIn</a>
                    <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2023 LOSERS BANK. All Rights Reserved. | Regulated by Bangladesh Bank</p>
            </div>
        </footer>
    </div>

    <script>
        // Branch data
        const branches = {
            agrabad: {
                name: "Agrabad Main Branch",
                address: "Agrabad Commercial Area, Chattogram 4100, Bangladesh",
                phone: "+880 31-625841, +880 31-625842",
                email: "agrabad@losersbank.com",
                manager: "Mr. Abdul Karim",
                hours: {
                    weekdays: "9:00 AM - 5:00 PM",
                    friday: "9:00 AM - 12:30 PM, 2:30 PM - 5:00 PM",
                    saturday: "9:00 AM - 2:00 PM",
                    holidays: "Closed"
                },
                services: [
                    "Personal Banking", "Business Banking", "Loan Services", "Foreign Exchange",
                    "ATM Services", "Online Banking", "Credit Cards", "Investment Services"
                ],
                info: "Our Agrabad Main Branch is the flagship branch of LOSERS BANK, located in the heart of Chattogram's commercial district. This branch offers full banking services with dedicated counters for corporate clients, NRB banking, and import-export financing.",
                facilities: "ATM, Security Vault, Disabled Access, Customer Lounge, Business Center"
            },
            khulsi: {
                name: "Khulsi Branch",
                address: "Khulshi Residential Area, Chattogram 4225, Bangladesh",
                phone: "+880 31-671234, +880 31-671235",
                email: "khulsi@losersbank.com",
                manager: "Ms. Fatema Begum",
                hours: {
                    weekdays: "9:00 AM - 4:00 PM",
                    friday: "9:00 AM - 12:00 PM, 2:30 PM - 4:00 PM",
                    saturday: "9:00 AM - 1:00 PM",
                    holidays: "Closed"
                },
                services: [
                    "Personal Banking", "Loan Services", "ATM Services", 
                    "Online Banking", "Credit Cards", "Savings Accounts"
                ],
                info: "The Khulsi Branch serves the residential community in one of Chattogram's fastest growing areas. We focus on personal banking services with extended hours for customer convenience.",
                facilities: "ATM, Disabled Access, Customer Lounge"
            },
            bahaddarhat: {
                name: "Bahaddarhat Branch",
                address: "Bahaddarhat Circle, Chattogram 4203, Bangladesh",
                phone: "+880 31-715678, +880 31-715679",
                email: "bahaddarhat@losersbank.com",
                manager: "Mr. Rahim Uddin",
                hours: {
                    weekdays: "9:00 AM - 5:00 PM",
                    friday: "9:00 AM - 12:30 PM, 2:30 PM - 5:00 PM",
                    saturday: "9:00 AM - 2:00 PM",
                    holidays: "Closed"
                },
                services: [
                    "Personal Banking", "Business Banking", "Loan Services", 
                    "Foreign Exchange", "ATM Services", "Online Banking"
                ],
                info: "Located at the busy Bahaddarhat Circle, this branch serves both residential and commercial customers in the area. We specialize in small business banking and personal loans.",
                facilities: "ATM, Security Vault, Customer Lounge"
            }
        };

        // Branch selection functionality
        document.querySelectorAll('.branch-item').forEach(item => {
            item.addEventListener('click', function() {
                // Remove active class from all branches
                document.querySelectorAll('.branch-item').forEach(branch => {
                    branch.classList.remove('active');
                });
                
                // Add active class to clicked branch
                this.classList.add('active');
                
                // Get branch ID
                const branchId = this.getAttribute('data-branch');
                
                // Update branch details
                updateBranchDetails(branchId);
            });
        });

        // Function to update branch details
        function updateBranchDetails(branchId) {
            const branch = branches[branchId];
            const branchContent = document.getElementById('branchContent');
            
            if (branch) {
                branchContent.innerHTML = `
                    <div class="branch-detail active">
                        <h2>${branch.name}</h2>
                        
                        <div class="branch-map">
                            <i class="fas fa-map-marked-alt"></i> Map View - ${branch.address}
                        </div>
                        
                        <div class="contact-info">
                            <div class="contact-item">
                                <h4><i class="fas fa-map-marker-alt"></i> Address</h4>
                                <p>${branch.address}</p>
                            </div>
                            <div class="contact-item">
                                <h4><i class="fas fa-phone"></i> Contact</h4>
                                <p>${branch.phone}</p>
                            </div>
                            <div class="contact-item">
                                <h4><i class="fas fa-envelope"></i> Email</h4>
                                <p>${branch.email}</p>
                            </div>
                            <div class="contact-item">
                                <h4><i class="fas fa-user-tie"></i> Branch Manager</h4>
                                <p>${branch.manager}</p>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h3><i class="far fa-clock"></i> Opening Hours</h3>
                            <table class="hours-table">
                                <tr>
                                    <td class="day">Sunday - Thursday</td>
                                    <td>${branch.hours.weekdays}</td>
                                </tr>
                                <tr>
                                    <td class="day">Friday</td>
                                    <td>${branch.hours.friday}</td>
                                </tr>
                                <tr>
                                    <td class="day">Saturday</td>
                                    <td>${branch.hours.saturday}</td>
                                </tr>
                                <tr>
                                    <td class="day">Holidays</td>
                                    <td>${branch.hours.holidays}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="detail-section">
                            <h3><i class="fas fa-concierge-bell"></i> Services Available</h3>
                            <div class="services-list">
                                ${branch.services.map(service => `<div class="service-item"><i class="fas fa-check"></i> ${service}</div>`).join('')}
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h3><i class="fas fa-info-circle"></i> Branch Information</h3>
                            <p>${branch.info}</p>
                            <p>Facilities: ${branch.facilities}</p>
                        </div>
                    </div>
                `;
            }
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const branchItems = document.querySelectorAll('.branch-item');
            
            branchItems.forEach(item => {
                const branchName = item.querySelector('.branch-name').textContent.toLowerCase();
                const branchAddress = item.querySelector('.branch-address').textContent.toLowerCase();
                
                if (branchName.includes(searchTerm) || branchAddress.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Initialize with Agrabad branch details
        updateBranchDetails('agrabad');
    </script>
</body>
</html>