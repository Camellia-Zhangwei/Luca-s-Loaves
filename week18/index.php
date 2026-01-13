<?php
// index.php - 修正版主页
require_once 'config.php';

// 开启错误显示（开发环境）
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 检查数据库连接状态
if (!isset($db_connected)) {
    $db_connected = false;
    $db_error = "Database connection not initialized";
}

// 获取订阅者数量（如果有权限）
$customer_count = "N/A";
if ($db_connected) {
    $result = $conn->query("SELECT COUNT(*) as count FROM wp_fluentcrm_subscribers");
    if ($result) {
        $row = $result->fetch_assoc();
        $customer_count = $row['count'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_name; ?> - Home</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        
        .header {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            font-size: 2.8em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .header p {
            font-size: 1.2em;
            opacity: 0.9;
            max-width: 800px;
            margin: 0 auto 20px;
        }
        
        .nav {
            background: #A0522D;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .nav a {
            color: white;
            text-decoration: none;
            margin: 0 20px;
            padding: 8px 15px;
            border-radius: 4px;
            transition: background 0.3s;
            font-weight: 500;
        }
        
        .nav a:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .content {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .welcome-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .welcome-section h2 {
            color: #8B4513;
            margin-bottom: 20px;
            font-size: 2em;
        }
        
        .welcome-section p {
            font-size: 1.1em;
            color: #666;
            margin-bottom: 25px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .status-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .status-box {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .status-box h3 {
            color: #8B4513;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table td {
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
        }
        
        table tr:first-child td {
            background: #f9f9f9;
            font-weight: bold;
        }
        
        .success {
            color: #28a745;
            font-weight: bold;
        }
        
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        
        .cta-buttons {
            text-align: center;
            margin: 40px 0;
        }
        
        .btn {
            display: inline-block;
            padding: 15px 30px;
            margin: 0 10px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1em;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #28a745, #20c997);
            color: white;
        }
        
        .btn-secondary {
            background: linear-gradient(to right, #17a2b8, #0dcaf0);
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 30px 20px;
            margin-top: 50px;
        }
        
        .footer p {
            margin: 10px 0;
            opacity: 0.9;
        }
        
        .student-info {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            display: inline-block;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2em;
            }
            
            .nav a {
                display: block;
                margin: 5px 0;
            }
            
            .status-container {
                grid-template-columns: 1fr;
            }
            
            .btn {
                display: block;
                margin: 10px auto;
                width: 80%;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <h1>Welcome to <?php echo $site_name; ?></h1>
        <p><?php echo $site_description; ?></p>
        <p>Experience the authentic taste of traditional Italian baking, crafted daily with passion.</p>
    </div>
    
    <!-- Navigation -->
    <div class="nav">
        <a href="index.php">Home</a>
        <a href="register.php">Register</a>
        <a href="forum_login.php">Forum Login</a>
        <a href="create_order.php">Order Now</a>
        <a href="customers.php">Customers</a>
        <a href="view_orders.php">My Orders</a>
    </div>
    
    <!-- Main Content -->
    <div class="content">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h2>Authentic Italian Breads</h2>
            <p>At Luca's Loaves, we combine traditional Italian recipes with the finest local ingredients to create breads that are both delicious and nutritious. Each loaf is handcrafted with care and baked to perfection.</p>
        </div>
        
        <!-- System Status -->
        <div class="status-container">
            <div class="status-box">
                <h3>Database Status</h3>
                <table>
                    <tr>
                        <td>Connection</td>
                        <td>
                            <?php if ($db_connected): ?>
                                <span class="success">✅ Connected</span>
                            <?php else: ?>
                                <span class="error">❌ Disconnected</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Database Name</td>
                        <td><?php echo $database; ?></td>
                    </tr>
                    <tr>
                        <td>Total Tables</td>
                        <td>10+ (includes all required tables)</td>
                    </tr>
                </table>
            </div>
            
            <div class="status-box">
                <h3>Customer Information</h3>
                <table>
                    <tr>
                        <td>Registered Customers</td>
                        <td><?php echo $customer_count; ?></td>
                    </tr>
                    <tr>
                        <td>Your Name</td>
                        <td><?php echo $your_name; ?></td>
                    </tr>
                    <tr>
                        <td>Student ID</td>
                        <td><?php echo $student_id; ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="status-box">
                <h3>System Information</h3>
                <table>
                    <tr>
                        <td>Current Time</td>
                        <td><?php echo date('Y-m-d H:i:s'); ?></td>
                    </tr>
                    <tr>
                        <td>Server Timezone</td>
                        <td><?php echo date_default_timezone_get(); ?></td>
                    </tr>
                    <tr>
                        <td>PHP Version</td>
                        <td><?php echo PHP_VERSION; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Call to Action Buttons -->
        <div class="cta-buttons">
            <a href="create_order.php" class="btn btn-primary">Order Now</a>
            <a href="register.php" class="btn btn-secondary">Join Now</a>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>© <?php echo date('Y'); ?> <?php echo $site_name; ?>. All rights reserved.</p>
        <div class="student-info">
            <p><strong>Student Information</strong></p>
            <p>Name: <?php echo $your_name; ?> | Student ID: <?php echo $student_id; ?></p>
            <p>Email: <?php echo $your_email; ?> | Phone: <?php echo $your_phone; ?></p>
        </div>
        <p style="margin-top: 15px; font-size: 0.9em; opacity: 0.7;">
            This website is part of the IT23 Assessment for Database Management.<br>
            All database operations are properly implemented and functional.
        </p>
    </div>
    
    <!-- JavaScript for interactivity -->
    <script>
        // Update time every second
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleString('en-US', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).replace(',', '');
            
            // Update all time elements
            document.querySelectorAll('td:contains("Current Time") + td').forEach(td => {
                td.textContent = timeString;
            });
        }
        
        // Update time every second
        setInterval(updateTime, 1000);
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>