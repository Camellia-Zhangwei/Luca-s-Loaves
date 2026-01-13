<?php
// forum_login.php - 论坛登录
session_start();

if (isset($_SESSION['user_email'])) {
    header('Location: forum.php');
    exit;
}

ob_start();
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="fas fa-sign-in-alt me-3"></i>Forum Login
                </h1>
                <p class="lead text-muted">Login using your registered email and phone</p>
            </div>
            
            <div class="card shadow">
                <div class="card-header bg-primary text-white py-3">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-key me-2"></i>Authentication Required
                    </h3>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="forum_login_process.php" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Email Address *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="invalid-feedback">Please enter your email.</div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Phone Number *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-control" name="phone" required>
                            </div>
                            <div class="invalid-feedback">Please enter your phone number.</div>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-unlock me-2"></i>Login to Forum
                            </button>
                            <div class="mt-3">
                                <small class="text-muted">
                                    Don't have an account? 
                                    <a href="register.php" class="text-decoration-none">Register here</a>
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- 测试凭证 -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <i class="fas fa-vial me-2"></i>Test Credentials
                </div>
                <div class="card-body">
                    <p class="mb-2">For assessment testing:</p>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tr>
                                <th>Email</th>
                                <td><?php echo $your_email; ?></td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td><?php echo $your_phone; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>