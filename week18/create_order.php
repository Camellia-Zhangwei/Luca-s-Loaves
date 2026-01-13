<?php
// create_order.php - 智能版（自动检测表结构）
require_once 'config.php';

// 开启错误显示
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 确保所有必需变量都已定义
if (!isset($db_connected)) $db_connected = false;
if (!isset($db_error)) $db_error = '';
if (!isset($your_name)) $your_name = 'Camellia';
if (!isset($student_id)) $student_id = 'IT2233190739';
if (!isset($your_email)) $your_email = '91179702@qq.com';
if (!isset($your_phone)) $your_phone = '1868046579';
if (!isset($page_title)) $page_title = 'Order Bread';
if (!isset($site_name)) $site_name = "Luca's Loaves";
if (!isset($site_description)) $site_description = "Artisanal Bread Bakery";

// 获取产品列表
$products = [];
if ($db_connected) {
    $result = $conn->query("SELECT * FROM products ORDER BY name");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
}

// 处理表单提交
$order_success = false;
$order_id = null;
$error_message = '';
$debug_info = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    if ($db_connected) {
        try {
            // 收集表单数据
            $customer_name = $_POST['full_name'] ?? $your_name;
            $customer_email = $_POST['email'] ?? $your_email;
            $customer_phone = $_POST['phone'] ?? $your_phone;
            $shipping_address = $_POST['address'] ?? '';
            $selected_products = $_POST['products'] ?? [];
            
            // 验证必填字段
            if (empty($customer_name) || empty($customer_email) || empty($shipping_address)) {
                throw new Exception('Please fill in all required fields');
            }
            
            // 构建产品数据
            $product_ids = [];
            $product_names = [];
            $total_amount = 0;
            
            foreach ($selected_products as $product_id => $quantity) {
                $quantity = intval($quantity);
                if ($quantity > 0) {
                    $product = array_filter($products, function($p) use ($product_id) {
                        return $p['product_id'] == $product_id;
                    });
                    $product = reset($product);
                    if ($product) {
                        $product_ids[] = $product_id;
                        $product_names[] = $product['name'] . ' (x' . $quantity . ')';
                        $total_amount += $product['price'] * $quantity;
                    }
                }
            }
            
            if (empty($product_ids)) {
                throw new Exception('Please select at least one product');
            }
            
            // 检查 wp_orders 表结构
            $debug_info .= "检查 wp_orders 表结构...<br>";
            $check_result = $conn->query("DESCRIBE wp_orders");
            $columns = [];
            $column_types = [];
            
            while ($row = $check_result->fetch_assoc()) {
                $columns[] = $row['Field'];
                $column_types[$row['Field']] = $row['Type'];
                $debug_info .= "字段: " . $row['Field'] . " | 类型: " . $row['Type'] . "<br>";
            }
            
            $debug_info .= "找到的字段: " . implode(', ', $columns) . "<br>";
            
            // 智能映射字段名
            $field_mapping = [];
            
            // 尝试匹配姓名字段
            $name_fields = ['customer_name', 'full_name', 'name', 'client_name', 'order_customer_name', 'customer'];
            foreach ($name_fields as $field) {
                if (in_array($field, $columns)) {
                    $field_mapping['name'] = $field;
                    break;
                }
            }
            
            // 尝试匹配邮箱字段
            $email_fields = ['customer_email', 'email', 'client_email', 'order_email'];
            foreach ($email_fields as $field) {
                if (in_array($field, $columns)) {
                    $field_mapping['email'] = $field;
                    break;
                }
            }
            
            // 尝试匹配电话字段
            $phone_fields = ['customer_phone', 'phone', 'client_phone', 'order_phone'];
            foreach ($phone_fields as $field) {
                if (in_array($field, $columns)) {
                    $field_mapping['phone'] = $field;
                    break;
                }
            }
            
            // 尝试匹配地址字段
            $address_fields = ['shipping_address', 'address', 'delivery_address', 'client_address'];
            foreach ($address_fields as $field) {
                if (in_array($field, $columns)) {
                    $field_mapping['address'] = $field;
                    break;
                }
            }
            
            // 尝试匹配总金额字段
            $total_fields = ['total_amount', 'total', 'order_total', 'amount'];
            foreach ($total_fields as $field) {
                if (in_array($field, $columns)) {
                    $field_mapping['total'] = $field;
                    break;
                }
            }
            
            // 尝试匹配学号字段
            $student_fields = ['student_id', 'user001', 'student_number', 'user_id'];
            foreach ($student_fields as $field) {
                if (in_array($field, $columns)) {
                    $field_mapping['student_id'] = $field;
                    break;
                }
            }
            
            // 尝试匹配产品ID字段
            $product_fields = ['product_ids', 'products', 'items', 'order_items'];
            foreach ($product_fields as $field) {
                if (in_array($field, $columns)) {
                    $field_mapping['products'] = $field;
                    break;
                }
            }
            
            $debug_info .= "字段映射结果: " . print_r($field_mapping, true) . "<br>";
            
            // 构建SQL插入语句
            $product_ids_json = json_encode($product_ids);
            
            // 构建字段名和值
            $field_names = [];
            $field_values = [];
            $placeholders = [];
            $bind_types = '';
            $bind_params = [];
            
            // 添加映射的字段
            if (isset($field_mapping['name'])) {
                $field_names[] = $field_mapping['name'];
                $field_values[] = $customer_name;
                $placeholders[] = '?';
                $bind_types .= 's';
                $bind_params[] = &$customer_name;
            }
            
            if (isset($field_mapping['email'])) {
                $field_names[] = $field_mapping['email'];
                $field_values[] = $customer_email;
                $placeholders[] = '?';
                $bind_types .= 's';
                $bind_params[] = &$customer_email;
            }
            
            if (isset($field_mapping['phone'])) {
                $field_names[] = $field_mapping['phone'];
                $field_values[] = $customer_phone;
                $placeholders[] = '?';
                $bind_types .= 's';
                $bind_params[] = &$customer_phone;
            }
            
            if (isset($field_mapping['address'])) {
                $field_names[] = $field_mapping['address'];
                $field_values[] = $shipping_address;
                $placeholders[] = '?';
                $bind_types .= 's';
                $bind_params[] = &$shipping_address;
            }
            
            if (isset($field_mapping['total'])) {
                $field_names[] = $field_mapping['total'];
                $field_values[] = $total_amount;
                $placeholders[] = '?';
                $bind_types .= 'd';
                $bind_params[] = &$total_amount;
            }
            
            if (isset($field_mapping['student_id'])) {
                $field_names[] = $field_mapping['student_id'];
                $field_values[] = $student_id;
                $placeholders[] = '?';
                $bind_types .= 's';
                $bind_params[] = &$student_id;
            }
            
            if (isset($field_mapping['products'])) {
                $field_names[] = $field_mapping['products'];
                $field_values[] = $product_ids_json;
                $placeholders[] = '?';
                $bind_types .= 's';
                $bind_params[] = &$product_ids_json;
            }
            
            // 如果没有找到关键字段，使用第一个可用的字段
            if (empty($field_names) && !empty($columns)) {
                // 至少插入到第一个字段中
                $first_field = $columns[0];
                $field_names[] = $first_field;
                $field_values[] = $customer_name;
                $placeholders[] = '?';
                $bind_types .= 's';
                $bind_params[] = &$customer_name;
                
                $debug_info .= "使用第一个字段: $first_field<br>";
            }
            
            if (empty($field_names)) {
                throw new Exception('Cannot determine table structure');
            }
            
            $sql = "INSERT INTO wp_orders (" . implode(', ', $field_names) . ") 
                    VALUES (" . implode(', ', $placeholders) . ")";
            
            $debug_info .= "生成的SQL: $sql<br>";
            $debug_info .= "绑定类型: $bind_types<br>";
            $debug_info .= "绑定参数: " . print_r($field_values, true) . "<br>";
            
            // 准备和执行语句
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $conn->error);
            }
            
            // 动态绑定参数
            array_unshift($bind_params, $bind_types);
            call_user_func_array([$stmt, 'bind_param'], $bind_params);
            
            if ($stmt->execute()) {
                $order_id = $stmt->insert_id;
                $order_success = true;
                $stmt->close();
                
                // 创建订单日志
                $log_details = "Order #$order_id created for $customer_name";
                $conn->query("INSERT INTO wp_order_logs (order_id, action, details) VALUES ($order_id, 'created', '$log_details')");
                
                $debug_info .= "订单插入成功！ID: $order_id<br>";
            } else {
                throw new Exception('Failed to save order: ' . $stmt->error);
            }
            
        } catch (Exception $e) {
            $error_message = $e->getMessage();
            $debug_info .= "错误: " . $e->getMessage() . "<br>";
        }
    } else {
        $error_message = 'Database connection failed';
    }
}

// 如果是学生测试，可以显示调试信息
if (isset($_GET['debug']) && $student_id == 'IT2233190739') {
    echo "<div style='background:#f8f9fa; padding:20px; margin:20px; border:1px solid #ddd;'>";
    echo "<h3>调试信息（仅学生可见）</h3>";
    echo $debug_info;
    echo "</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Fresh Bread - <?php echo $site_name; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 精简但美观的样式 */
        :root {
            --primary: #8B4513;
            --primary-light: #A0522D;
            --secondary: #28a745;
            --light: #f8f9fa;
            --dark: #333;
            --success: #28a745;
            --danger: #dc3545;
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
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
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
        
        .tagline {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
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
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .student-info h3 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Cards */
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
        
        /* Form */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #555;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }
        
        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .product-card {
            border: 2px solid var(--border);
            border-radius: 0.75rem;
            padding: 1.5rem;
            transition: all 0.3s;
            background: white;
        }
        
        .product-card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(139, 69, 19, 0.1);
        }
        
        .product-card.selected {
            border-color: var(--success);
            background: #f8fff9;
        }
        
        .product-icon {
            font-size: 2rem;
            color: var(--primary);
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .product-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
            text-align: center;
        }
        
        .product-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--success);
            text-align: center;
            margin: 1rem 0;
        }
        
        /* Quantity Selector */
        .quantity-selector {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .qty-btn {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            border: 2px solid var(--primary);
            background: white;
            color: var(--primary);
            font-size: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        
        .qty-btn:hover {
            background: var(--primary);
            color: white;
        }
        
        .qty-input {
            width: 4rem;
            text-align: center;
            padding: 0.5rem;
            border: 2px solid var(--border);
            border-radius: 0.5rem;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        /* Order Summary */
        .order-summary {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 1rem;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: var(--shadow);
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px dashed var(--border);
        }
        
        .summary-total {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--success);
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid var(--primary);
        }
        
        /* Submit Button */
        .submit-btn {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 1.2rem 3rem;
            font-size: 1.2rem;
            font-weight: 700;
            border-radius: 2.5rem;
            cursor: pointer;
            transition: all 0.3s;
            display: block;
            margin: 2rem auto;
            box-shadow: 0 6px 15px rgba(40, 167, 69, 0.3);
        }
        
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.4);
        }
        
        /* Messages */
        .alert {
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin-bottom: 2rem;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 1px solid #b1dfbb;
        }
        
        .alert-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 1px solid #f1b0b7;
        }
        
        /* Footer */
        .footer {
            background: linear-gradient(135deg, var(--dark) 0%, #555 100%);
            color: white;
            text-align: center;
            padding: 3rem 1rem;
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
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .student-banner {
                flex-direction: column;
                text-align: center;
            }
            
            .container {
                padding: 0 0.5rem;
            }
            
            .card {
                padding: 1.5rem;
            }
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
        <p class="tagline"><?php echo $site_description; ?></p>
    </header>
    
    <!-- Navigation -->
    <nav class="nav">
        <a href="index.php"><i class="fas fa-home"></i> Home</a>
        <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
        <a href="forum_login.php"><i class="fas fa-comments"></i> Forum</a>
        <a href="create_order.php" class="active"><i class="fas fa-shopping-cart"></i> Order</a>
        <a href="view_orders.php"><i class="fas fa-clipboard-list"></i> My Orders</a>
    </nav>
    
    <!-- Main Content -->
    <div class="container">
        <!-- Student Banner -->
        <div class="student-banner">
            <div class="student-info">
                <h3><i class="fas fa-user-graduate"></i> Student Assessment</h3>
                <p><?php echo $your_name; ?> | ID: <?php echo $student_id; ?></p>
            </div>
            <div class="database-status">
                <i class="fas fa-database"></i> <?php echo $db_connected ? 'Connected' : 'Disconnected'; ?>
            </div>
        </div>
        
        <!-- Messages -->
        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <h3><i class="fas fa-exclamation-circle"></i> Error</h3>
                <p><?php echo htmlspecialchars($error_message); ?></p>
                <?php if ($student_id == 'IT2233190739'): ?>
                    <p><small><a href="?debug=1">Show debug info</a></small></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($order_success): ?>
            <div class="alert alert-success">
                <h3><i class="fas fa-check-circle"></i> Order Successful!</h3>
                <p>Thank you for your order! Your order ID is <strong>#<?php echo $order_id; ?></strong>.</p>
                <p>Total: <strong>$<?php echo number_format($total_amount ?? 0, 2); ?></strong></p>
                <p><a href="view_orders.php" style="color: #155724; font-weight: bold;">View Your Orders</a></p>
            </div>
        <?php endif; ?>
        
        <!-- Order Form -->
        <form method="POST" action="" id="orderForm">
            <!-- Customer Information -->
            <div class="card">
                <h2 class="card-title"><i class="fas fa-user-circle"></i> Customer Information</h2>
                
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-user"></i> Full Name *</label>
                    <input type="text" class="form-control" name="full_name" 
                           value="<?php echo htmlspecialchars($your_name); ?>" 
                           placeholder="Enter your full name" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-envelope"></i> Email Address *</label>
                    <input type="email" class="form-control" name="email" 
                           value="<?php echo htmlspecialchars($your_email); ?>" 
                           placeholder="Enter your email address" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-phone"></i> Phone Number *</label>
                    <input type="tel" class="form-control" name="phone" 
                           value="<?php echo htmlspecialchars($your_phone); ?>" 
                           placeholder="Enter your phone number" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-map-marker-alt"></i> Shipping Address *</label>
                    <textarea class="form-control" name="address" 
                              placeholder="Enter complete shipping address" 
                              rows="3" required>123 Student Street, Sydney NSW 2000</textarea>
                </div>
            </div>
            
            <!-- Products Selection -->
            <div class="card">
                <h2 class="card-title"><i class="fas fa-bread-slice"></i> Select Products</h2>
                <p style="text-align: center; color: #666; margin-bottom: 1rem;">Choose from our selection of freshly baked breads</p>
                
                <?php if (!empty($products)): ?>
                    <div class="products-grid">
                        <?php foreach ($products as $index => $product): ?>
                            <div class="product-card" id="product-<?php echo $product['product_id']; ?>">
                                <div class="product-icon">
                                    <?php 
                                    $icons = ['fas fa-bread-slice', 'fas fa-wheat-awn', 'fas fa-burger', 'fas fa-cookie', 'fas fa-cake'];
                                    $icon = $icons[$index % count($icons)];
                                    ?>
                                    <i class="<?php echo $icon; ?>"></i>
                                </div>
                                <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                <div style="color: #666; font-size: 0.9rem; text-align: center; margin-bottom: 1rem;">
                                    <?php echo htmlspecialchars($product['description']); ?>
                                </div>
                                <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                                
                                <div class="quantity-selector">
                                    <button type="button" class="qty-btn minus" data-product="<?php echo $product['product_id']; ?>">-</button>
                                    <input type="number" class="qty-input" 
                                           name="products[<?php echo $product['product_id']; ?>]" 
                                           value="0" min="0" max="10" 
                                           data-price="<?php echo $product['price']; ?>">
                                    <button type="button" class="qty-btn plus" data-product="<?php echo $product['product_id']; ?>">+</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 2rem; color: var(--danger);">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                        <h3>No Products Available</h3>
                        <p>Please check back later or contact administrator.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Order Summary -->
            <div class="order-summary">
                <h2 class="card-title"><i class="fas fa-receipt"></i> Order Summary</h2>
                <div id="orderSummary">
                    <div class="summary-item">
                        <span>Selected Items:</span>
                        <span id="itemCount">0</span>
                    </div>
                    <div class="summary-item">
                        <span>Subtotal:</span>
                        <span id="subtotal">$0.00</span>
                    </div>
                    <div class="summary-item">
                        <span>Delivery Fee:</span>
                        <span>$5.00</span>
                    </div>
                    <div class="summary-item summary-total">
                        <span>Total Amount:</span>
                        <span id="totalAmount">$5.00</span>
                    </div>
                </div>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" name="submit_order" class="submit-btn">
                <i class="fas fa-shopping-cart"></i> Place Order Now
            </button>
        </form>
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <h3><?php echo $site_name; ?></h3>
            <p>Crafted with passion, served with love</p>
            
            <div class="student-footer">
                <p><strong>Student Information for Assessment</strong></p>
                <p>Name: <?php echo $your_name; ?> | ID: <?php echo $student_id; ?></p>
                <p>Email: <?php echo $your_email; ?> | Phone: <?php echo $your_phone; ?></p>
                <p>Course: IT23 Database Management</p>
                <p>Date: <?php echo date('F j, Y'); ?> | Time: <?php echo date('H:i:s'); ?></p>
            </div>
            
            <p style="margin-top: 1.5rem; opacity: 0.7; font-size: 0.9rem;">
                © <?php echo date('Y'); ?> <?php echo $site_name; ?>. All rights reserved.
            </p>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        // Quantity buttons functionality
        document.querySelectorAll('.qty-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product');
                const input = document.querySelector(`input[name="products[${productId}]"]`);
                let currentValue = parseInt(input.value) || 0;
                
                if (this.classList.contains('plus')) {
                    if (currentValue < 10) {
                        currentValue++;
                    }
                } else if (this.classList.contains('minus')) {
                    if (currentValue > 0) {
                        currentValue--;
                    }
                }
                
                input.value = currentValue;
                
                // Update product card style
                const card = document.getElementById(`product-${productId}`);
                if (currentValue > 0) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
                
                updateOrderSummary();
            });
        });
        
        // Update quantity input directly
        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', function() {
                let value = parseInt(this.value) || 0;
                if (value < 0) value = 0;
                if (value > 10) value = 10;
                this.value = value;
                
                const productId = this.name.match(/\[(\d+)\]/)[1];
                const card = document.getElementById(`product-${productId}`);
                if (value > 0) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
                
                updateOrderSummary();
            });
        });
        
        // Update order summary
        function updateOrderSummary() {
            let itemCount = 0;
            let subtotal = 0;
            
            document.querySelectorAll('.qty-input').forEach(input => {
                const quantity = parseInt(input.value) || 0;
                if (quantity > 0) {
                    const price = parseFloat(input.getAttribute('data-price'));
                    itemCount += quantity;
                    subtotal += quantity * price;
                }
            });
            
            const deliveryFee = 5.00;
            const total = subtotal + deliveryFee;
            
            document.getElementById('itemCount').textContent = itemCount;
            document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('totalAmount').textContent = '$' + total.toFixed(2);
        }
        
        // Form validation
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            const itemCount = parseInt(document.getElementById('itemCount').textContent) || 0;
            
            if (itemCount === 0) {
                e.preventDefault();
                alert('Please select at least one product before placing your order.');
                return false;
            }
            
            return true;
        });
        
        // Initialize order summary
        updateOrderSummary();
    </script>
</body>
</html>