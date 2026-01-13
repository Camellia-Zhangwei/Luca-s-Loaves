<?php
// register_process.php - 注册处理
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';

ob_start();

if (!$db_connected) {
    $error = "Database connection failed. Cannot register.";
    $success = false;
} else {
    // 检查邮箱是否已存在
    $check_sql = "SELECT id FROM wp_fluentcrm_subscribers WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        $error = "This email is already registered.";
        $success = false;
    } else {
        // 插入新用户
        $user_field = 'user_' . date('YmdHis');
        $insert_sql = "INSERT INTO wp_fluentcrm_subscribers 
                      (first_name, last_name, email, phone, address, user001, created_at) 
                      VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone, $address, $user_field);
        
        if ($insert_stmt->execute()) {
            $user_id = $conn->insert_id;
            $success = true;
            
            // 自动登录
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $first_name . ' ' . $last_name;
            
            $message = "Registration successful! You are now logged in.";
        } else {
            $error = "Registration failed: " . $conn->error;
            $success = false;
        }
        $insert_stmt->close();
    }
    $check_stmt->close();
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header <?php echo $success ? 'bg-success' : 'bg-danger'; ?> text-white py-3">
                    <h2 class="card-title mb-0 text-center">
                        <i class="fas fa-<?php echo $success ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                        Registration <?php echo $success ? 'Successful' : 'Failed'; ?>
                    </h2>
                </div>
                <div class="card-body p-4 text-center">
                    
                    <?php if ($success): ?>
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h3 class="mb-3">Welcome, <?php echo htmlspecialchars($first_name); ?>!</h3>
                        <p class="lead mb-4"><?php echo $message; ?></p>
                        
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-user-circle me-2"></i>
                            <strong>Account Created:</strong><br>
                            Email: <?php echo htmlspecialchars($email); ?><br>
                            Name: <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?><br>
                            User Field: <?php echo htmlspecialchars($user_field); ?>
                        </div>
                        
                        <div class="mb-4">
                            <a href="create_order.php" class="btn btn-primary btn-lg me-2">
                                <i class="fas fa-shopping-cart me-2"></i>Place Your First Order
                            </a>
                            <a href="forum.php" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-comments me-2"></i>Visit Forum
                            </a>
                        </div>
                        
                        <div class="alert alert-success">
                            <i class="fas fa-database me-2"></i>
                            Your account has been saved to the <code>wp_fluentcrm_subscribers</code> table.
                        </div>
                        
                    <?php else: ?>
                        <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                        <h3 class="mb-3">Registration Failed</h3>
                        <p class="lead mb-4"><?php echo $error; ?></p>
                        
                        <a href="register.php" class="btn btn-primary btn-lg me-2">
                            <i class="fas fa-redo me-2"></i>Try Again
                        </a>
                        <a href="index.php" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-home me-2"></i>Back to Home
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-center text-muted">
                    <small>
                        <?php echo date('Y-m-d H:i:s'); ?>
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