<?php
// forum.php - 论坛
session_start();
require_once 'config.php';

ob_start();

$is_logged_in = isset($_SESSION['user_email']);
?>

<div class="container">
    <h1 class="display-6 fw-bold mb-4">
        <i class="fas fa-comments me-3"></i>Community Forum
    </h1>
    
    <?php if ($is_logged_in): ?>
        <!-- 登录用户 -->
        <div class="alert alert-success mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-user-circle me-2"></i>
                    Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></strong>!
                    You can post in the forum.
                </div>
                <div>
                    <a href="create_order.php" class="btn btn-sm btn-success">
                        <i class="fas fa-shopping-cart me-1"></i>Order Bread
                    </a>
                </div>
            </div>
        </div>
        
        <?php if ($db_connected): ?>
            <?php
            // 获取论坛帖子
            $posts_result = $conn->query("SELECT * FROM wp_forum_posts ORDER BY post_date DESC LIMIT 5");
            ?>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-list me-2"></i>Recent Discussions
                </div>
                <div class="card-body">
                    <?php if ($posts_result && $posts_result->num_rows > 0): ?>
                        <?php while($post = $posts_result->fetch_assoc()): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($post['content']); ?></p>
                                <div class="text-muted">
                                    <small>
                                        <i class="fas fa-user me-1"></i>
                                        <?php echo htmlspecialchars($post['author_email']); ?> | 
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo date('M d, Y', strtotime($post['post_date'])); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">No forum posts yet. Be the first to post!</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- 发帖表单 -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-edit me-2"></i>Create New Post
                </div>
                <div class="card-body">
                    <form method="POST" action="forum_post.php">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Post Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Content</label>
                            <textarea class="form-control" name="content" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane me-2"></i>Submit Post
                        </button>
                    </form>
                </div>
            </div>
            
            <?php if ($posts_result) $posts_result->free(); ?>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Database connection failed. Cannot load forum posts.
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <!-- 未登录用户 -->
        <div class="text-center py-5">
            <i class="fas fa-lock fa-5x text-warning mb-4"></i>
            <h2 class="mb-3">Access Restricted</h2>
            <p class="lead text-muted mb-4">You must be logged in to access the forum.</p>
            <div class="mt-4">
                <a href="forum_login.php" class="btn btn-primary btn-lg me-3">
                    <i class="fas fa-sign-in-alt me-2"></i>Login to Forum
                </a>
                <a href="register.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-user-plus me-2"></i>Register First
                </a>
            </div>
        </div>
        
        <div class="alert alert-info mt-4">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Forum Authentication:</strong> Uses <code>wp_fluentcrm_subscribers</code> table for authentication.
            This demonstrates the Customer → Forum Post one-to-many relationship.
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>