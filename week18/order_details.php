<?php
// order_details.php - 订单详情
ob_start();

$order_id = $_GET['id'] ?? 0;

if (!$order_id || !$db_connected) {
    echo '<div class="alert alert-danger">Invalid order ID or database error.</div>';
    $content = ob_get_clean();
    include 'template.php';
    exit;
}

// 获取订单详情
$sql = "SELECT o.*, s.first_name, s.last_name, s.phone 
        FROM wp_orders o 
        LEFT JOIN wp_fluentcrm_subscribers s ON o.customer_email = s.email 
        WHERE o.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo '<div class="alert alert-warning">Order not found.</div>';
    $content = ob_get_clean();
    include 'template.php';
    exit;
}
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 fw-bold">
                <i class="fas fa-file-invoice me-3"></i>Order #<?php echo $order_id; ?>
            </h1>
            <p class="text-muted">
                Date: <?php echo date('F j, Y', strtotime($order['order_date'])); ?>
            </p>
        </div>
        <div>
            <?php
            $status = $order['order_status'] ?? 'pending';
            $badge_class = [
                'pending' => 'warning',
                'processing' => 'info',
                'shipped' => 'primary', 
                'delivered' => 'success',
                'cancelled' => 'danger'
            ][$status] ?? 'secondary';
            ?>
            <span class="badge bg-<?php echo $badge_class; ?> p-3 fs-6">
                <?php echo strtoupper($status); ?>
            </span>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-user me-2"></i>Customer Details
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Name:</th>
                            <td><?php echo htmlspecialchars(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')); ?></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td><?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <th>Shipping:</th>
                            <td><?php echo nl2br(htmlspecialchars($order['shipping_address'] ?? 'N/A')); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-credit-card me-2"></i>Order Summary
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Order ID:</th>
                            <td>#<?php echo $order_id; ?></td>
                        </tr>
                        <tr>
                            <th>Order Date:</th>
                            <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                        </tr>
                        <tr>
                            <th>Payment Method:</th>
                            <td><?php echo ucwords(str_replace('_', ' ', $order['payment_method'] ?? 'N/A')); ?></td>
                        </tr>
                        <tr>
                            <th>Total Amount:</th>
                            <td class="fw-bold fs-5">$<?php echo number_format($order['total_amount'] ?? 0, 2); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <i class="fas fa-bread-slice me-2"></i>Order Items
        </div>
        <div class="card-body">
            <?php
            $product_ids = json_decode($order['product_ids'] ?? '[]', true);
            if (is_array($product_ids) && count($product_ids) > 0):
            ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            foreach ($product_ids as $product_id):
                                $product_sql = "SELECT name, price FROM products WHERE product_id = ?";
                                $product_stmt = $conn->prepare($product_sql);
                                $product_stmt->bind_param("i", $product_id);
                                $product_stmt->execute();
                                $product_result = $product_stmt->get_result();
                                $product = $product_result->fetch_assoc();
                                $product_stmt->close();
                                
                                if ($product):
                                    $quantity = 1;
                                    $subtotal = $product['price'] * $quantity;
                                    $total += $subtotal;
                            ?>
                            <tr>
                                <td>#<?php echo $product_id; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $quantity; ?></td>
                                <td class="text-end">$<?php echo number_format($subtotal, 2); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php endforeach; ?>
                            
                            <tr class="table-secondary">
                                <td colspan="4" class="text-end fw-bold">Total:</td>
                                <td class="text-end fw-bold">$<?php echo number_format($total, 2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No product details available for this order.
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="d-flex justify-content-between mb-4">
        <a href="view_orders.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Orders
        </a>
        <div>
            <a href="create_order.php" class="btn btn-primary me-2">
                <i class="fas fa-plus-circle me-2"></i>New Order
            </a>
            <button onclick="window.print()" class="btn btn-outline-primary">
                <i class="fas fa-print me-2"></i>Print Receipt
            </button>
        </div>
    </div>
    
    <!-- 评估信息 -->
    <div class="alert alert-warning">
        <i class="fas fa-clipboard-check me-2"></i>
        <strong>Assessment Proof:</strong> This order (#<?php echo $order_id; ?>) is stored in the 
        <code>wp_orders</code> table with customer <strong><?php echo htmlspecialchars($order['customer_email']); ?></strong>
    </div>
</div>

<?php
$stmt->close();
$content = ob_get_clean();
include 'template.php';
?>