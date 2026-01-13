<?php
// customers.php - 修正版（使用实际字段）
require_once 'config.php';

// 开启错误显示
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 确保变量已定义
if (!isset($db_connected)) $db_connected = false;
if (!isset($db_error)) $db_error = '';
if (!isset($site_name)) $site_name = "Luca's Loaves";
if (!isset($site_description)) $site_description = "Artisanal Bread Bakery";
if (!isset($your_name)) $your_name = 'Camellia';
if (!isset($student_id)) $student_id = 'IT2233190739';

// 设置页面标题
$page_title = "Customer List";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo $site_name; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #8B4513;
            --primary-light: #A0522D;
            --secondary: #28a745;
            --light: #f8f9fa;
            --dark: #333;
            --border: #e0e0e0;
            --shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background: #f5f5f5;
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 2rem 1rem;
            text-align: center;
            box-shadow: var(--shadow);
        }
        
        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }
        
        .logo i {
            color: #FFD700;
        }
        
        /* Navigation */
        .nav {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 1rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1.5rem;
            border-radius: 2rem;
            transition: all 0.3s;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .nav a:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .nav a.active {
            background: rgba(255,255,255,0.3);
        }
        
        /* Container */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        /* Student Banner */
        .student-banner {
            background: linear-gradient(135deg, var(--secondary) 0%, #20c997 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }
        
        .student-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        /* Content Card */
        .card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }
        
        .card-title {
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        /* Table Styles */
        .table-container {
            overflow-x: auto;
            margin: 1rem 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        
        th {
            background: var(--primary);
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .customer-count {
            background: #e7f5ff;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        /* Footer */
        .footer {
            background: linear-gradient(135deg, var(--dark) 0%, #555 100%);
            color: white;
            text-align: center;
            padding: 2rem 1rem;
            margin-top: 3rem;
        }
        
        .student-footer {
            background: rgba(255,255,255,0.1);
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin-top: 1.5rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .nav a {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
            
            .container {
                padding: 0 0.5rem;
            }
            
            .card {
                padding: 1.5rem;
            }
            
            table {
                font-size: 0.9rem;
            }
            
            th, td {
                padding: 0.75rem 0.5rem;
            }
        }
        
        /* Error Message */
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin: 2rem 0;
            border: 1px solid #f5c6cb;
        }
        
        /* Highlight your record */
        .your-record {
            background: #d4edda !important;
            border-left: 4px solid #28a745;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo">
            <i class="fas fa-bread-slice"></i>
            <?php echo $site_name; ?>
            <i class="fas fa-bread-slice"></i>
        </div>
        <p><?php echo $site_description; ?></p>
    </header>
    
    <!-- Navigation -->
    <nav class="nav">
        <a href="index.php"><i class="fas fa-home"></i> Home</a>
        <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
        <a href="forum_login.php"><i class="fas fa-comments"></i> Forum</a>
        <a href="create_order.php"><i class="fas fa-shopping-cart"></i> Order</a>
        <a href="customers.php" class="active"><i class="fas fa-users"></i> Customers</a>
        <a href="view_orders.php"><i class="fas fa-clipboard-list"></i> My Orders</a>
    </nav>
    
    <!-- Main Content -->
    <div class="container">
        <!-- Student Banner -->
        <div class="student-banner">
            <div class="student-info">
                <div>
                    <h3><i class="fas fa-user-graduate"></i> Student Assessment</h3>
                    <p><?php echo $your_name; ?> | ID: <?php echo $student_id; ?></p>
                </div>
                <div>
                    <i class="fas fa-database"></i> 
                    Database: <?php echo $db_connected ? 'Connected' : 'Disconnected'; ?>
                </div>
            </div>
        </div>
        
        <!-- Customer List Card -->
        <div class="card">
            <h2 class="card-title"><i class="fas fa-users"></i> Customer List</h2>
            
            <?php if (!$db_connected): ?>
                <div class="error-message">
                    <h3><i class="fas fa-exclamation-circle"></i> Database Connection Failed</h3>
                    <p>Unable to connect to the database. Please check your configuration.</p>
                    <?php if (isset($db_error)): ?>
                        <p><small>Error details: <?php echo htmlspecialchars($db_error); ?></small></p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php
                // 获取客户总数
                $count_result = $conn->query("SELECT COUNT(*) as total FROM wp_fluentcrm_subscribers");
                $count_row = $count_result->fetch_assoc();
                $total_customers = $count_row['total'];
                
                // 获取客户列表（使用实际字段名）
                $result = $conn->query("SELECT 
                    id, 
                    first_name, 
                    last_name, 
                    email, 
                    phone, 
                    loyalty_points, 
                    status, 
                    created_at,
                    address
                FROM wp_fluentcrm_subscribers 
                ORDER BY created_at DESC");
                ?>
                
                <div class="customer-count">
                    <i class="fas fa-user-friends"></i> Total Customers: <?php echo $total_customers; ?>
                    <?php if ($total_customers >= 5): ?>
                        <span style="color: green; margin-left: 10px;">
                            <i class="fas fa-check-circle"></i> Requirement met (5+ records)
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Loyalty Points</th>
                                <th>Status</th>
                                <th>Joined Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($customer = $result->fetch_assoc()): ?>
                                    <?php 
                                    $is_you = ($customer['email'] == '91179702@qq.com');
                                    $row_class = $is_you ? 'your-record' : '';
                                    ?>
                                    <tr class="<?php echo $row_class; ?>">
                                        <td><?php echo $customer['id']; ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?>
                                            <?php if ($is_you): ?>
                                                <span style="color: #28a745; font-weight: bold; margin-left: 5px;">
                                                    <i class="fas fa-user-check"></i> (You - <?php echo $student_id; ?>)
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                        <td>
                                            <?php if (!empty($customer['address'])): ?>
                                                <?php echo htmlspecialchars(substr($customer['address'], 0, 30)); ?>
                                                <?php if (strlen($customer['address']) > 30): ?>...<?php endif; ?>
                                            <?php else: ?>
                                                <span style="color: #999; font-style: italic;">Not provided</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span style="color: #28a745; font-weight: bold;">
                                                <?php echo $customer['loyalty_points']; ?>
                                            </span>
                                            <?php if ($customer['loyalty_points'] > 0): ?>
                                                <i class="fas fa-star" style="color: gold; margin-left: 5px;"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span style="
                                                background: <?php echo $customer['status'] == 'active' ? '#d4edda' : '#f8d7da'; ?>;
                                                color: <?php echo $customer['status'] == 'active' ? '#155724' : '#721c24'; ?>;
                                                padding: 3px 8px;
                                                border-radius: 3px;
                                                font-size: 0.9rem;
                                            ">
                                                <?php echo ucfirst($customer['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo date('Y-m-d', strtotime($customer['created_at'])); ?>
                                            <br>
                                            <small style="color: #666;">
                                                <?php echo date('H:i:s', strtotime($customer['created_at'])); ?>
                                            </small>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 2rem;">
                                        <i class="fas fa-user-slash fa-2x" style="color: #ccc;"></i>
                                        <p>No customers found.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Statistics -->
                <div style="margin-top: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 0.5rem;">
                    <h3><i class="fas fa-chart-bar"></i> Customer Statistics</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
                        <div style="background: white; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <div style="color: #28a745; font-size: 1.5rem; font-weight: bold;">
                                <?php
                                $active_result = $conn->query("SELECT COUNT(*) as active FROM wp_fluentcrm_subscribers WHERE status = 'active'");
                                $active_row = $active_result->fetch_assoc();
                                echo $active_row['active'];
                                ?>
                            </div>
                            <div style="color: #666; font-size: 0.9rem;">Active Customers</div>
                        </div>
                        
                        <div style="background: white; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <div style="color: #8B4513; font-size: 1.5rem; font-weight: bold;">
                                <?php echo $total_customers; ?>
                            </div>
                            <div style="color: #666; font-size: 0.9rem;">Total Customers</div>
                        </div>
                        
                        <div style="background: white; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <div style="color: #17a2b8; font-size: 1.5rem; font-weight: bold;">
                                <?php
                                $points_result = $conn->query("SELECT SUM(loyalty_points) as total_points FROM wp_fluentcrm_subscribers");
                                $points_row = $points_result->fetch_assoc();
                                echo $points_row['total_points'] ?? 0;
                                ?>
                            </div>
                            <div style="color: #666; font-size: 0.9rem;">Total Loyalty Points</div>
                        </div>
                        
                        <div style="background: white; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <div style="color: #dc3545; font-size: 1.5rem; font-weight: bold;">
                                <?php
                                $inactive_result = $conn->query("SELECT COUNT(*) as inactive FROM wp_fluentcrm_subscribers WHERE status != 'active'");
                                $inactive_row = $inactive_result->fetch_assoc();
                                echo $inactive_row['inactive'];
                                ?>
                            </div>
                            <div style="color: #666; font-size: 0.9rem;">Inactive Customers</div>
                        </div>
                    </div>
                </div>
                
                <!-- Export Button -->
                <div style="text-align: center; margin-top: 2rem;">
                    <button onclick="window.print()" style="
                        background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 5px;
                        cursor: pointer;
                        font-size: 1rem;
                    ">
                        <i class="fas fa-print"></i> Print Customer List
                    </button>
                    <p style="color: #666; font-size: 0.9rem; margin-top: 10px;">
                        <i class="fas fa-info-circle"></i> This page shows <?php echo $total_customers; ?> customer records including your student record.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <h3><?php echo $site_name; ?></h3>
            <p>Crafted with passion, served with love</p>
            
            <div class="student-footer">
                <p><strong>Student Information for Assessment</strong></p>
                <p>Name: <?php echo $your_name; ?> | Student ID: <?php echo $student_id; ?></p>
                <p>Email: 91179702@qq.com | Phone: 1868046579</p>
                <p>Page: <?php echo $page_title; ?> | Date: <?php echo date('F j, Y H:i:s'); ?></p>
                <p>Database: <?php echo $database; ?> | Status: <?php echo $db_connected ? 'Connected' : 'Disconnected'; ?></p>
                <p>Customer Records: <?php echo $total_customers ?? 0; ?> | Your Record: Highlighted in green</p>
            </div>
            
            <p style="margin-top: 1.5rem; opacity: 0.7; font-size: 0.9rem;">
                © <?php echo date('Y'); ?> <?php echo $site_name; ?>. All rights reserved.<br>
                This page is part of the IT23 Assessment for Database Management.
            </p>
        </div>
    </footer>
    
    <!-- JavaScript for interactivity -->
    <script>
        // Highlight search functionality
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.querySelector('table');
            const tr = table.getElementsByTagName('tr');
            
            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < td.length; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                tr[i].style.display = found ? '' : 'none';
            }
        }
        
        // Add search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tableContainer = document.querySelector('.table-container');
            const searchDiv = document.createElement('div');
            searchDiv.innerHTML = `
                <div style="margin-bottom: 1rem;">
                    <input type="text" id="searchInput" placeholder="Search customers..." 
                           style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 5px;"
                           onkeyup="searchTable()">
                    <p style="color: #666; font-size: 0.9rem; margin-top: 5px;">
                        <i class="fas fa-search"></i> Type to search in customer records
                    </p>
                </div>
            `;
            tableContainer.parentNode.insertBefore(searchDiv, tableContainer);
        });
    </script>
</body>
</html>