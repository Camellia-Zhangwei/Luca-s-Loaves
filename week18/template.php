<?php
// template.php - 完整模板
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . ' | ' . $site_name; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
    <!-- 自定义样式 -->
    <style>
        :root {
            --primary: #d4a574;
            --primary-dark: #b8864c;
            --secondary: #8b4513;
            --light: #f8f4e9;
            --dark: #2c1810;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: 70px;
        }
        
        /* 导航栏 */
        .navbar {
            background: linear-gradient(135deg, var(--dark) 0%, #4a2c1a 100%);
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary) !important;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.85) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            margin: 0 2px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover,
        .nav-link.active {
            color: var(--dark) !important;
            background-color: var(--primary);
            transform: translateY(-2px);
        }
        
        /* 按钮 */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            color: var(--dark);
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 165, 116, 0.3);
        }
        
        /* 卡片 */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        /* 页脚 */
        footer {
            background: linear-gradient(135deg, var(--dark) 0%, #1a0f0a 100%);
            color: white;
            margin-top: auto;
            padding: 2rem 0;
        }
        
        .developer-info {
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 1.2rem;
            margin-top: 1rem;
        }
        
        /* 表格 */
        .table th {
            background-color: var(--dark);
            color: white;
            border-color: #454d55;
        }
        
        /* 响应式 */
        @media (max-width: 768px) {
            body { padding-top: 56px; }
            .navbar-brand { font-size: 1.3rem; }
        }
        
        /* 动画 */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* 状态徽章 */
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <!-- 导航栏 -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-bread-slice me-2"></i><?php echo $site_name; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page=='index.php'?'active':''; ?>" href="index.php">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page=='create_order.php'?'active':''; ?>" href="create_order.php">
                            <i class="fas fa-shopping-cart me-1"></i> Order Now
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page=='view_orders.php'?'active':''; ?>" href="view_orders.php">
                            <i class="fas fa-receipt me-1"></i> My Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page=='customers.php'?'active':''; ?>" href="customers.php">
                            <i class="fas fa-users me-1"></i> Customers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page=='forum.php'?'active':''; ?>" href="forum.php">
                            <i class="fas fa-comments me-1"></i> Forum
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_email'])): ?>
                        <li class="nav-item">
                            <span class="nav-link text-light">
                                <i class="fas fa-user-circle me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page=='register.php'?'active':''; ?>" href="register.php">
                                <i class="fas fa-user-plus me-1"></i> Register
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page=='forum_login.php'?'active':''; ?>" href="forum_login.php">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- 主要内容 -->
    <main class="flex-grow-1 container py-4 fade-in">
        <!-- 数据库状态提示 -->
        <?php if (isset($db_error)): ?>
        <div class="alert alert-warning alert-dismissible fade show mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Database Warning:</strong> <?php echo $db_error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- 页面内容 -->
        <?php if (isset($content)): ?>
            <?php echo $content; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Page content is loading...
            </div>
        <?php endif; ?>
    </main>

    <!-- 页脚 -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="mb-3">
                        <i class="fas fa-bread-slice me-2"></i><?php echo $site_name; ?>
                    </h5>
                    <p class="mb-3" style="color: rgba(255,255,255,0.8);">
                        <?php echo $site_description; ?><br>
                        Freshly baked bread delivered daily.
                    </p>
                    <div class="d-flex gap-3 mb-3">
                        <a href="#" class="text-primary"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-primary"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-primary"><i class="fab fa-twitter fa-lg"></i></a>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="developer-info">
                        <h6 class="mb-2" style="color: var(--primary);">
                            <i class="fas fa-code me-2"></i>Assessment Website
                        </h6>
                        <p class="mb-1">
                            <i class="fas fa-user me-2"></i>
                            <strong><?php echo $your_name; ?></strong>
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-id-card me-2"></i>
                            Student ID: <strong><?php echo $student_id; ?></strong>
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-calendar me-2"></i>
                            <?php echo date('F j, Y'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4 pt-3 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
                <p class="mb-2" style="color: rgba(255,255,255,0.7);">
                    &copy; <?php echo date("Y"); ?> <?php echo $site_name; ?>. All rights reserved.
                </p>
                <small style="color: rgba(255,255,255,0.5);">
                    URL: <?php echo $site_url . $current_page; ?>
                </small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- 自定义脚本 -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 自动关闭警告框
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // 表单验证
        const forms = document.querySelectorAll('.needs-validation');
        forms.forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
        
        // 当前年份
        document.getElementById('current-year')?.textContent = new Date().getFullYear();
    });
    </script>
</body>
</html>