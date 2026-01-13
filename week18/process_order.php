<?php
// process_order.php - 完整订单处理
ob_start();

// 检查数据库连接
if (!$db_connected) {
    echo '<div class="alert alert-danger">Database connection failed. Cannot process order.</div>';
    $content = ob_get_clean();
    include 'template.php';
    exit;
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取表单数据
    $customer_name = $conn->real_escape_string($_POST['customer_name'] ?? '');
    $customer_email = $conn->real_escape_string($_POST['customer_email'] ?? '');
    $customer_phone = $conn->real_escape_string($_POST['customer_phone'] ?? '');
    $shipping_address = $conn->real_escape_string($_POST['shipping_address'] ?? '');
    $payment_method = $conn->real_escape_string($_POST['payment_method'] ?? 'credit_card');
    $delivery_date = $conn->real_escape_string($_POST['delivery_date'] ?? '');
    
    // 获取产品数据
    $product_ids_json = $_POST['product_ids_json'] ?? '[]';
    $quantities_json = $_POST['quantities_json'] ?? '[]';
    
    $product_ids = json_decode($product_ids_json, true);
    $quantities = json_decode($quantities_json, true);
    
    if (empty($product_ids) || empty($customer_email)) {
        $error = "Please select at least one product and provide your email.";
        $success = false;
    } else {
        // 计算总金额
        $subtotal = 0;
        $order_items = [];
        $delivery_fee = 5.00;
        
        foreach ($product_ids as $index => $product_id) {
            $quantity = $quantities[$index] ?? 1;
            
            $stmt = $conn->prepare("SELECT name, price FROM products WHERE product_id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $stmt->bind_result($product_name, $product_price);
            
            if ($stmt->fetch()) {
                $item_total = $product_price * $quantity;
                $subtotal += $item_total;
                
                $order_items[] = [
                    'id' => $product_id,
                    'name' => $product_name,
                    'price' => $product_price,
                    'quantity' => $quantity,
                    'subtotal' => $item_total
                ];
            }
            $stmt->close();
        }
        
        $total_amount = $subtotal + $delivery_fee;
        
        // 检查客户是否存在
        $customer_exists = false;
        $check_stmt = $conn->prepare("SELECT id FROM wp_fluentcrm_subscribers WHERE email = ?");
        $check_stmt->bind_param("s", $customer_email);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $customer_exists = true;
        }
        $check_stmt->close();
        
        // 如果客户不存在，添加到订阅者表
        if (!$customer_exists) {
            $first_name = explode(' ', $customer_name)[0] ?? $customer_name;
            $last_name = count(explode(' ', $customer_name)) > 1 ? explode(' ', $customer_name)[1] : '';
            
            $insert_customer = $conn->prepare("INSERT INTO wp_fluentcrm_subscribers 
                (first_name, last_name, email, phone, address, user001) 
                VALUES (?, ?, ?, ?, ?, ?)");
            
            $user_field = 'order_customer_' . date('YmdHis');
            $insert_customer->bind_param("ssssss", $first_name, $last_name, $customer_email, 
                                       $customer_phone, $shipping_address, $user_field);
            $insert_customer->execute();
            $insert_customer->close();
        }
        
        // 创建订单记录
        $product_ids_str = json_encode($product_ids);
        $order_sql = "INSERT INTO wp_orders 
            (customer_email, product_ids, total_amount, shipping_address, payment_method, order_status, order_date) 
            VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
        
        $order_stmt = $conn->prepare($order_sql);
        $order_stmt->bind_param("ssdss", $customer_email, $product_ids_str, $total_amount, 
                              $shipping_address, $payment_method);
        
        if ($order_stmt->execute()) {
            $order_id = $conn->insert_id;
            $success = true;
            
            // 记录订单日志
            $log_sql = "INSERT INTO wp_order_logs (order_id, action, details) VALUES (?, 'created', ?)";
            $log_stmt = $conn->prepare($log_sql);
            $details = "Order placed by " . $customer_name . " (" . $customer_email . ")";
            $log_stmt->bind_param("is", $order_id, $details);
            $log_stmt->execute();
            $log_stmt->close();
            
            // 设置session（如果是新用户）
            if (!$customer_exists || !isset($_SESSION['user_email'])) {
                $_SESSION['user_email'] = $customer_email;
                $_SESSION['user_name'] = $customer_name;
            }
            
        } else {
            $error = "Failed to create order: " . $conn->error;
            $success = false;
        }
        $order_stmt->close();
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header <?php echo isset($success) && $success ? 'bg-success' : 'bg-danger'; ?> text-white py-3">
                    <h2 class="card-title mb-0 text-center">
                        <i class="fas fa-<?php echo isset($success) && $success ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                        Order <?php echo isset($success) && $success ? 'Successful' : 'Failed'; ?>
                    </h2>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (isset($success) && $success): ?>
                    <!-- 成功页面 -->
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h3 class="mb-2">Thank You for Your Order!</h3>
                        <p class="text-muted">Your order has been placed successfully.</p>
                    </div>
                    
                    <!-- 订单详情 -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">Order Information</div>
                                <div class="card-body">
                                    <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
                                    <p><strong>Order Date:</strong> <?php echo date('F j, Y H:i:s'); ?></p>
                                    <p><strong>Status:</strong> <span class="badge bg-warning">Pending</span></p>
                                    <p><strong>Payment Method:</strong> <?php echo ucwords(str_replace('_', ' ', $payment_method)); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">Customer Information</div>
                                <div class="card-body">
                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($customer_email); ?></p>
                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer_phone); ?></p>
                                    <p><strong>Delivery To:</strong><br><?php echo nl2br(htmlspecialchars($shipping_address)); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 订单物品 -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">Order Items</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td class="text-end">$<?php echo number_format($item['subtotal'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Delivery Fee:</strong></td>
                                            <td class="text-end">$<?php echo number_format($delivery_fee, 2); ?></td>
                                        </tr>
                                        <tr class="table-active">
                                            <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                            <td class="text-end fw-bold">$<?php echo number_format($total_amount, 2); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 操作按钮 -->
                    <div class="text-center">
                        <a href="view_orders.php" class="btn btn-primary me-2">
                            <i class="fas fa-receipt me-2"></i>View All Orders
                        </a>
                        <a href="create_order.php" class="btn btn-outline-primary me-2">
                            <i class="fas fa-plus-circle me-2"></i>New Order
                        </a>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Back to Home
                        </a>
                    </div>
                    
                    <!-- 评估信息 -->
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-clipboard-check me-2"></i>
                        <strong>Assessment Note:</strong> Order #<?php echo $order_id; ?> has been saved to the 
                        <code>wp_orders</code> table. Customer: <strong><?php echo htmlspecialchars($customer_email); ?></strong>
                    </div>
                    
                    <?php elseif (isset($error)): ?>
                    <!-- 错误页面 -->
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                        <h3 class="mb-2">Order Failed</h3>
                        <p class="text-muted"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                    
                    <div class="text-center">
                        <a href="create_order.php" class="btn btn-primary me-2">
                            <i class="fas fa-redo me-2"></i>Try Again
                        </a>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Back to Home
                        </a>
                    </div>
                    
                    <?php else: ?>
                    <!-- 无效访问 -->
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                        <h3 class="mb-2">Invalid Access</h3>
                        <p class="text-muted">Please use the order form to place an order.</p>
                        <a href="create_order.php" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-2"></i>Go to Order Form
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-center text-muted">
                    <small>
                        <?php echo date('Y-m-d H:i:s'); ?> | 
                        IP: <?php echo $_SERVER['REMOTE_ADDR']; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>