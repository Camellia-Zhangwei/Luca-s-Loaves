<?php
// view_orders.php - 修正版（使用实际字段）
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
if (!isset($your_email)) $your_email = '91179702@qq.com';
if (!isset($your_phone)) $your_phone = '1868046579';

// 设置页面标题
$page_title = "My Orders";

// 初始化变量
$orders = [];
$total_orders = 0;
$total_spent = 0;

// 如果数据库连接成功，获取订单数据
if ($db_connected) {
    // 根据实际字段名查询订单
    $query = "SELECT * FROM wp_orders WHERE customer_email = ? ORDER BY order_date DESC";
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("s", $your_email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
            $total_spent += $row['total_amount'] ?? 0;
        }
        $total_orders = count($orders);
        $stmt->close();
    } else {
        // 如果prepare失败，直接查询
        $result = $conn->query("SELECT * FROM wp_orders WHERE customer_email = '$your_email' ORDER BY order_date DESC");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
                $total_spent += $row['total_amount'] ?? 0;
            }
            $total_orders = count($orders);
        }
    }
}
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
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
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
        
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .summary-card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: var(--shadow);
            text-align: center;
            border: 1px solid var(--border);
        }
        
        .summary-card h3 {
            color: var(--primary);
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        
        .summary-card .number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--secondary);
        }
        
        .orders-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .order-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: transform 0.3s;
        }
        
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--border);
        }
        
        .order-id {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .order-status {
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-processing { background: #cce5ff; color: #004085; }
        
        .order-details {
            margin: 1rem 0;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px dashed var(--border);
        }
        
        .detail-label {
            color: #666;
            font-weight: 500;
        }
        
        .detail-value {
            font-weight: 600;
        }
        
        .order-total {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--secondary);
            text-align: right;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid var(--border);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #666;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #ccc;
            margin-bottom: 1rem;
        }
        
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
            
            .orders-container {
                grid-template-columns: 1fr;
            }
            
            .summary-cards {
                grid-template-columns: 1fr;
            }
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin: 2rem 0;
            border: 1px solid #f5c6cb;
        }
        
        .info-message {
            background: #d1ecf1;
            color: #0c5460;
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 1rem 0;
            border: 1px solid #bee5eb;
        }
        
        .cta-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .cta-button {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 1rem 2rem;
            border-radius: 2rem;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }
        
        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(139, 69, 19, 0.3);
        }
        
        .product-list {
            margin-top: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
            font-size: 0.9rem;
        }
        
        .product-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .product-item:last-child {
            border-bottom: none;
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
        <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
        <a href="view_orders.php" class="active"><i class="fas fa-clipboard-list"></i> My Orders</a>
    </nav>
    
    <!-- Main Content -->
    <div class="container">
        <!-- Student Banner -->
        <div class="student-banner">
            <div class="student-info">
                <div>
                    <h3><i class="fas fa-user-graduate"></i> Student Assessment</h3>
                    <p><?php echo $your_name; ?> | ID: <?php echo $student_id; ?></p>
                    <p>Email: <?php echo $your_email; ?> | Phone: <?php echo $your_phone; ?></p>
                </div>
                <div>
                    <i class="fas fa-database"></i> 
                    Database: <?php echo $db_connected ? 'Connected' : 'Disconnected'; ?>
                </div>
            </div>
        </div>
        
        <!-- Info Message -->
        <div class="info-message">
            <p><strong>数据库查询信息：</strong></p>
            <p>查询邮箱: <strong><?php echo $your_email; ?></strong></p>
            <p>表字段: order_id, customer_email, product_ids, order_date, total_amount, shipping_address, payment_method, order_status</p>
            <p>找到订单: <strong><?php echo $total_orders; ?> 个</strong></p>
        </div>
        
        <!-- Order History Card -->
        <div class="card">
            <h2 class="card-title"><i class="fas fa-history"></i> Order History</h2>
            
            <?php if (!$db_connected): ?>
                <div class="error-message">
                    <h3><i class="fas fa-exclamation-circle"></i> Database Connection Failed</h3>
                    <p>Cannot retrieve orders. Please check your database configuration.</p>
                </div>
            <?php else: ?>
                <!-- Summary Cards -->
                <div class="summary-cards">
                    <div class="summary-card">
                        <h3>Total Orders</h3>
                        <div class="number"><?php echo $total_orders; ?></div>
                        <p style="color: #666; font-size: 0.9rem; margin-top: 0.5rem;">
                            <?php echo $total_orders == 0 ? 'No orders yet' : 'All your orders'; ?>
                        </p>
                    </div>
                    
                    <div class="summary-card">
                        <h3>Total Spent</h3>
                        <div class="number">$<?php echo number_format($total_spent, 2); ?></div>
                        <p style="color: #666; font-size: 0.9rem; margin-top: 0.5rem;">
                            Lifetime value
                        </p>
                    </div>
                    
                    <div class="summary-card">
                        <h3>Average Order</h3>
                        <div class="number">
                            $<?php echo $total_orders > 0 ? number_format($total_spent / $total_orders, 2) : '0.00'; ?>
                        </div>
                        <p style="color: #666; font-size: 0.9rem; margin-top: 0.5rem;">
                            Per order average
                        </p>
                    </div>
                    
                    <div class="summary-card">
                        <h3>Last Order</h3>
                        <div class="number">
                            <?php 
                            if ($total_orders > 0 && !empty($orders[0]['order_date'])) {
                                echo date('M j', strtotime($orders[0]['order_date']));
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </div>
                        <p style="color: #666; font-size: 0.9rem; margin-top: 0.5rem;">
                            Most recent purchase
                        </p>
                    </div>
                </div>
                
                <!-- Orders List -->
                <?php if ($total_orders > 0): ?>
                    <div class="orders-container">
                        <?php foreach ($orders as $order): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <div class="order-id">Order #<?php echo $order['order_id']; ?></div>
                                    <div class="order-status status-<?php echo strtolower($order['order_status'] ?? 'pending'); ?>">
                                        <?php echo ucfirst($order['order_status'] ?? 'Pending'); ?>
                                    </div>
                                </div>
                                
                                <div class="order-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Order Date:</span>
                                        <span class="detail-value">
                                            <?php echo date('F j, Y', strtotime($order['order_date'])); ?>
                                            <br>
                                            <small style="color: #666;">
                                                <?php echo date('H:i:s', strtotime($order['order_date'])); ?>
                                            </small>
                                        </span>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <span class="detail-label">Email:</span>
                                        <span class="detail-value">
                                            <?php echo htmlspecialchars($order['customer_email']); ?>
                                        </span>
                                    </div>
                                    
                                    <?php if (!empty($order['shipping_address'])): ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Shipping To:</span>
                                        <span class="detail-value" style="max-width: 150px; text-align: right;">
                                            <?php echo htmlspecialchars(substr($order['shipping_address'], 0, 30)); ?>
                                            <?php if (strlen($order['shipping_address']) > 30): ?>...<?php endif; ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($order['payment_method'])): ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Payment Method:</span>
                                        <span class="detail-value">
                                            <?php echo htmlspecialchars($order['payment_method']); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <!-- Product Items -->
                                    <?php if (!empty($order['product_ids'])): ?>
                                    <div style="margin-top: 1rem;">
                                        <div class="detail-label" style="margin-bottom: 0.5rem;">Products:</div>
                                        <div class="product-list">
                                            <?php
                                            try {
                                                $products = json_decode($order['product_ids'], true);
                                                if ($products && is_array($products)) {
                                                    foreach ($products as $product) {
                                                        echo '<div class="product-item">';
                                                        echo '<span>Product ID: ' . ($product['product_id'] ?? 'N/A') . '</span>';
                                                        echo '<span>Qty: ' . ($product['quantity'] ?? 1) . '</span>';
                                                        echo '</div>';
                                                    }
                                                } else {
                                                    echo '<div style="color: #666; font-style: italic;">';
                                                    echo htmlspecialchars(substr($order['product_ids'], 0, 50));
                                                    if (strlen($order['product_ids']) > 50) echo '...';
                                                    echo '</div>';
                                                }
                                            } catch (Exception $e) {
                                                echo '<div style="color: #666;">Product data available</div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="order-total">
                                    Total: $<?php echo number_format($order['total_amount'] ?? 0, 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="empty-state">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>No Orders Found</h3>
                        <p>You haven't placed any orders yet. Start shopping to see your order history here.</p>
                        <p>Searching for orders with email: <strong><?php echo $your_email; ?></strong></p>
                        
                        <div class="cta-buttons">
                            <a href="create_order.php" class="cta-button">
                                <i class="fas fa-plus"></i> Place Your First Order
                            </a>
                            <a href="index.php" class="cta-button">
                                <i class="fas fa-home"></i> Return to Home
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Database Info -->
                <div style="margin-top: 2rem; padding: 1rem; background: #f8f9fa; border-radius: 0.5rem; font-size: 0.9rem;">
                    <p><strong>Database Info:</strong> 
                        Table: wp_orders | 
                        Your Email: <?php echo $your_email; ?> | 
                        Found: <?php echo $total_orders; ?> orders |
                        Query: SELECT * FROM wp_orders WHERE customer_email = '<?php echo $your_email; ?>'
                    </p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <h2 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <a href="create_order.php" style="
                    background: linear-gradient(135deg, var(--secondary) 0%, #20c997 100%);
                    color: white;
                    padding: 1.5rem;
                    border-radius: 0.75rem;
                    text-decoration: none;
                    text-align: center;
                    transition: transform 0.3s;
                " onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <i class="fas fa-plus fa-2x"></i>
                    <h3>New Order</h3>
                    <p>Place a new order for fresh bread</p>
                </a>
                
                <a href="customers.php" style="
                    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
                    color: white;
                    padding: 1.5rem;
                    border-radius: 0.75rem;
                    text-decoration: none;
                    text-align: center;
                    transition: transform 0.3s;
                " onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <i class="fas fa-users fa-2x"></i>
                    <h3>Customers</h3>
                    <p>View all registered customers</p>
                </a>
                
                <a href="index.php" style="
                    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
                    color: white;
                    padding: 1.5rem;
                    border-radius: 0.75rem;
                    text-decoration: none;
                    text-align: center;
                    transition: transform 0.3s;
                " onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <i class="fas fa-database fa-2x"></i>
                    <h3>Database</h3>
                    <p>View database structure and ERD</p>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <h3><?php echo $site_name; ?></h3>
            <p><?php echo $site_description; ?></p>
            <p>Freshly baked bread delivered daily.</p>
            
            <div class="student-footer">
                <p><strong>Assessment Website</strong></p>
                <p>Student: <?php echo $your_name; ?></p>
                <p>Student ID: <?php echo $student_id; ?></p>
                <p>Email: <?php echo $your_email; ?> | Phone: <?php echo $your_phone; ?></p>
                <p>Date: <?php echo date('F j, Y'); ?></p>
                <p>Page: <?php echo $page_title; ?> | Orders: <?php echo $total_orders; ?> found</p>
            </div>
            
            <p style="margin-top: 1.5rem; opacity: 0.7; font-size: 0.9rem;">
                © <?php echo date('Y'); ?> <?php echo $site_name; ?>. All rights reserved.<br>
                This is a student assessment project for IT23 Database Management.
            </p>
        </div>
    </footer>
    
    <script>
        // Filter orders by status
        function filterOrders(status) {
            const orderCards = document.querySelectorAll('.order-card');
            orderCards.forEach(card => {
                const cardStatus = card.querySelector('.order-status').textContent.toLowerCase();
                if (status === 'all' || cardStatus === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        // Add filter buttons if there are orders
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($total_orders > 0): ?>
            const ordersContainer = document.querySelector('.orders-container');
            if (ordersContainer) {
                const filterDiv = document.createElement('div');
                filterDiv.innerHTML = `
                    <div style="margin-bottom: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <button onclick="filterOrders('all')" 
                                style="padding: 0.5rem 1rem; background: var(--primary); color: white; border: none; border-radius: 0.25rem; cursor: pointer;">
                            All Orders (<?php echo $total_orders; ?>)
                        </button>
                        <button onclick="filterOrders('pending')" 
                                style="padding: 0.5rem 1rem; background: #fff3cd; color: #856404; border: none; border-radius: 0.25rem; cursor: pointer;">
                            Pending
                        </button>
                        <button onclick="filterOrders('completed')" 
                                style="padding: 0.5rem 1rem; background: #d4edda; color: #155724; border: none; border-radius: 0.25rem; cursor: pointer;">
                            Completed
                        </button>
                        <button onclick="filterOrders('processing')" 
                                style="padding: 0.5rem 1rem; background: #cce5ff; color: #004085; border: none; border-radius: 0.25rem; cursor: pointer;">
                            Processing
                        </button>
                    </div>
                `;
                ordersContainer.parentNode.insertBefore(filterDiv, ordersContainer);
            }
            <?php endif; ?>
        });
    </script>
</body>
</html>