<?php
// forum_login_process.php - 登录处理
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forum_login.php');
    exit;
}

$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';

ob_start();

if (!$db_connected) {
    $error = "Database connection failed. Cannot login.";
    $success = false;
} else {
    // 验证用户凭证
    $sql = "SELECT id, first_name, last_name, email, phone 
            FROM wp_fluentcrm_subscribers 
            WHERE email = ? AND phone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user) {
        // 登录成功
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_phone'] = $user['phone'];
        
        $success = true;
        $message = "Login successful!";
    } else {
        $error = "Invalid email or phone number.";
        $success = false;
    }
    $stmt->close();
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header <?php echo $success ? 'bg-success' : 'bg-danger'; ?> text-white py-3">
                    <h2 class="card-title mb-0 text-center">
                        <i class="fas fa-<?php echo $success ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                        Login <?php echo $success ? 'Successful' : 'Failed'; ?>
                    </h2>
                </div>
                <div class="card-body p-4 text-center">
                    
                    <?php if ($success): ?>
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h3 class="mb-3">Welcome Back!</h3>
                        <p class="lead mb-4"><?php echo $message; ?></p>
                        
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-user-circle me-2"></i>
                            <strong>Logged in as:</strong><br>
                            Name: <?php echo htmlspecialchars($_SESSION['user_name']); ?><br>
                            Email: <?php echo htmlspecialchars($_SESSION['user_email']); ?>
                        </div>
                        
                        <div class="mb-4">
                            <a href="forum.php" class="btn btn-primary btn-lg me-2">
                                <i class="fas fa-comments me-2"></i>Go to Forum
                            </a>
                            <a href="create_order.php" class="btn btn-success btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i>Place Order
                            </a>
                        </div>
                        
                        <script>
                        setTimeout(function() {
                            window.location.href = 'forum.php';
                        }, 3000);
                        </script>
                        
                    <?php else: ?>
                        <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                        <h3 class="mb-3">Login Failed</h3>
                        <p class="lead mb-4"><?php echo $error; ?></p>
                        
                        <div class="mb-4">
                            <a href="forum_login.php" class="btn btn-primary btn-lg me-2">
                                <i class="fas fa-redo me-2"></i>Try Again
                            </a>
                            <a href="register.php" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Register Now
                            </a>
                        </div>
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