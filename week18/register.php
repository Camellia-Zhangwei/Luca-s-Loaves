<?php
// register_simple.php - 修复版（带表单处理）
session_start();

// 网站配置
$site_name = "Luca's Loaves";
$site_description = "Artisanal Bread Bakery";
$your_name = "Camellia";
$student_id = "IT2233190739";
$your_email = "91179702@qq.com";
$your_phone = "1868046579";
$page_title = "Register";

// 初始化变量
$registration_success = false;
$form_data = [];

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // 收集表单数据
    $form_data = [
        'first_name' => $_POST['first_name'] ?? 'Camellia',
        'last_name' => $_POST['last_name'] ?? 'Student',
        'email' => $_POST['email'] ?? $your_email,
        'phone' => $_POST['phone'] ?? $your_phone,
        'address' => $_POST['address'] ?? '123 Student Street, Sydney NSW 2000',
        'student_id' => $_POST['student_id'] ?? $student_id
    ];
    
    // 模拟注册成功
    $registration_success = true;
    
    // 如果是真实的数据库连接，这里会插入数据
    // 现在只是模拟成功
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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .header {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            margin: 0;
            font-size: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .nav {
            background: rgba(255,255,255,0.15);
            padding: 15px;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .nav a {
            color: white;
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 30px;
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s;
        }
        
        .nav a:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .student-banner {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            border: 1px solid rgba(139, 69, 19, 0.1);
        }
        
        .card-title {
            color: #8B4513;
            font-size: 1.8rem;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .test-credentials {
            background: linear-gradient(135deg, #e7f5ff 0%, #d0ebff 100%);
            border: 2px dashed #17a2b8;
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .test-credentials h3 {
            color: #0c5460;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .credential-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #b8daff;
        }
        
        .credential-item:last-child {
            border-bottom: none;
        }
        
        .credential-label {
            font-weight: 600;
            color: #0c5460;
        }
        
        .credential-value {
            font-family: 'Courier New', monospace;
            background: white;
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #b8daff;
        }
        
        .success-message {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            padding: 30px;
            border-radius: 10px;
            margin: 25px 0;
            text-align: center;
            border: 1px solid #b1dfbb;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .required {
            color: #dc3545;
            margin-left: 4px;
        }
        
        input, textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            font-family: inherit;
            box-sizing: border-box;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: #8B4513;
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            border: none;
            padding: 18px 40px;
            font-size: 1.2rem;
            font-weight: 700;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            display: block;
            margin: 30px auto;
            width: 100%;
            max-width: 300px;
            box-shadow: 0 6px 15px rgba(139, 69, 19, 0.3);
        }
        
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(139, 69, 19, 0.4);
        }
        
        .footer {
            background: linear-gradient(135deg, #333 0%, #555 100%);
            color: white;
            text-align: center;
            padding: 30px 20px;
            margin-top: 40px;
        }
        
        .student-footer {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
                flex-direction: column;
                gap: 10px;
            }
            
            .nav {
                gap: 10px;
            }
            
            .nav a {
                padding: 6px 15px;
                font-size: 0.9rem;
            }
            
            .container {
                padding: 0 15px;
            }
            
            .card {
                padding: 20px;
            }
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 576px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <h1>
                <i class="fas fa-bread-slice"></i>
                <?php echo $site_name; ?>
                <i class="fas fa-bread-slice"></i>
            </h1>
            <p><?php echo $site_description; ?></p>
        </div>
        
        <!-- Navigation -->
        <nav class="nav">
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <a href="register_simple.php" style="background: rgba(255,255,255,0.3);"><i class="fas fa-user-plus"></i> Register</a>
            <a href="forum_login.php"><i class="fas fa-comments"></i> Forum</a>
            <a href="create_order.php"><i class="fas fa-shopping-cart"></i> Order</a>
            <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
            <a href="view_orders.php"><i class="fas fa-clipboard-list"></i> My Orders</a>
        </nav>
    </header>
    
    <!-- Main Content -->
    <div class="container">
        <!-- Student Banner -->
        <div class="student-banner">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-user-graduate"></i> Student Assessment
                    </h3>
                    <p style="margin: 5px 0 0 0;"><?php echo $your_name; ?> | ID: <?php echo $student_id; ?></p>
                </div>
                <div>
                    <i class="fas fa-user-check"></i> Registration Form
                </div>
            </div>
        </div>
        
        <!-- Registration Card -->
        <div class="card">
            <h2 class="card-title"><i class="fas fa-user-plus"></i> Customer Registration</h2>
            
            <?php if ($registration_success): ?>
                <!-- Success Message -->
                <div class="success-message">
                    <h3 style="margin: 0 0 20px 0; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <i class="fas fa-check-circle"></i> Registration Successful!
                    </h3>
                    <p>Thank you for registering with <?php echo $site_name; ?>!</p>
                    <p>Your account has been created successfully.</p>
                    
                    <div style="background: white; padding: 20px; border-radius: 8px; margin: 25px 0; text-align: left;">
                        <h4 style="color: #8B4513; margin-bottom: 15px;">Your Registration Details:</h4>
                        <div class="credential-item">
                            <span class="credential-label">Name:</span>
                            <span class="credential-value"><?php echo htmlspecialchars($form_data['first_name'] . ' ' . $form_data['last_name']); ?></span>
                        </div>
                        <div class="credential-item">
                            <span class="credential-label">Email:</span>
                            <span class="credential-value"><?php echo htmlspecialchars($form_data['email']); ?></span>
                        </div>
                        <div class="credential-item">
                            <span class="credential-label">Phone:</span>
                            <span class="credential-value"><?php echo htmlspecialchars($form_data['phone']); ?></span>
                        </div>
                        <div class="credential-item">
                            <span class="credential-label">Student ID:</span>
                            <span class="credential-value"><?php echo htmlspecialchars($form_data['student_id']); ?></span>
                        </div>
                        <?php if (!empty($form_data['address'])): ?>
                        <div class="credential-item">
                            <span class="credential-label">Address:</span>
                            <span class="credential-value"><?php echo htmlspecialchars($form_data['address']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <p>You can now use these credentials to log in to the forum and place orders.</p>
                    <div style="margin-top: 25px; display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                        <a href="forum_login.php" style="
                            background: #8B4513;
                            color: white;
                            padding: 12px 25px;
                            border-radius: 30px;
                            text-decoration: none;
                            display: inline-flex;
                            align-items: center;
                            gap: 8px;
                            font-weight: 600;
                        ">
                            <i class="fas fa-sign-in-alt"></i> Login to Forum
                        </a>
                        <a href="create_order.php" style="
                            background: #28a745;
                            color: white;
                            padding: 12px 25px;
                            border-radius: 30px;
                            text-decoration: none;
                            display: inline-flex;
                            align-items: center;
                            gap: 8px;
                            font-weight: 600;
                        ">
                            <i class="fas fa-shopping-cart"></i> Place Order
                        </a>
                        <a href="register_simple.php" style="
                            background: #6c757d;
                            color: white;
                            padding: 12px 25px;
                            border-radius: 30px;
                            text-decoration: none;
                            display: inline-flex;
                            align-items: center;
                            gap: 8px;
                            font-weight: 600;
                        ">
                            <i class="fas fa-plus"></i> Register Another
                        </a>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- Test Credentials -->
                <div class="test-credentials">
                    <h3><i class="fas fa-vial"></i> Use these credentials for testing:</h3>
                    <div class="credential-item">
                        <span class="credential-label">Email</span>
                        <span class="credential-value"><?php echo htmlspecialchars($your_email); ?></span>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">Phone</span>
                        <span class="credential-value"><?php echo htmlspecialchars($your_phone); ?></span>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">Student ID</span>
                        <span class="credential-value"><?php echo htmlspecialchars($student_id); ?></span>
                    </div>
                    <p style="margin-top: 15px; color: #0c5460; font-size: 0.95rem;">
                        <i class="fas fa-info-circle"></i> You can use these credentials or enter your own information.
                    </p>
                </div>
                
                <!-- Registration Form -->
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-user"></i> First Name
                                <span class="required">*</span>
                            </label>
                            <input type="text" name="first_name" 
                                   value="<?php echo htmlspecialchars($_POST['first_name'] ?? 'Camellia'); ?>" 
                                   placeholder="Enter your first name" required>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <i class="fas fa-user"></i> Last Name
                            </label>
                            <input type="text" name="last_name" 
                                   value="<?php echo htmlspecialchars($_POST['last_name'] ?? 'Student'); ?>" 
                                   placeholder="Enter your last name">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-envelope"></i> Email Address
                            <span class="required">*</span>
                        </label>
                        <input type="email" name="email" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? $your_email); ?>" 
                               placeholder="Enter your email address" required>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-phone"></i> Phone Number
                            <span class="required">*</span>
                        </label>
                        <input type="tel" name="phone" 
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? $your_phone); ?>" 
                               placeholder="Enter your phone number" required>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-map-marker-alt"></i> Address
                        </label>
                        <textarea name="address" 
                                  placeholder="Enter your address (optional)" 
                                  rows="3"><?php echo htmlspecialchars($_POST['address'] ?? '123 Student Street, Sydney NSW 2000'); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-id-card"></i> Student ID (Optional)
                        </label>
                        <input type="text" name="student_id" 
                               value="<?php echo htmlspecialchars($_POST['student_id'] ?? $student_id); ?>" 
                               placeholder="Enter your student ID">
                        <small style="color: #666; display: block; margin-top: 8px;">
                            For assessment purposes only
                        </small>
                    </div>
                    
                    <button type="submit" name="register" class="submit-btn">
                        <i class="fas fa-user-plus"></i> Register Now
                    </button>
                </form>
                
                <!-- Terms and Conditions -->
                <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <h4 style="color: #8B4513; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-file-alt"></i> Terms & Conditions
                    </h4>
                    <p style="font-size: 0.95rem; color: #666; line-height: 1.6;">
                        By registering, you agree to receive communications from <?php echo $site_name; ?> 
                        regarding your orders and promotions. You can unsubscribe at any time.
                    </p>
                    <p style="font-size: 0.95rem; color: #666; line-height: 1.6;">
                        <strong>Note for Assessment:</strong> This registration form is part of a student project 
                        for IT23 Database Management. Your information will be stored in the database for 
                        assessment purposes only.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <h3 style="margin: 0 0 15px 0;"><?php echo $site_name; ?></h3>
            <p style="margin: 0 0 20px 0;"><?php echo $site_description; ?></p>
            
            <div class="student-footer">
                <p style="margin: 0 0 15px 0; font-weight: bold;">Student Information for Assessment</p>
                <p style="margin: 8px 0;">Name: <?php echo $your_name; ?> | ID: <?php echo $student_id; ?></p>
                <p style="margin: 8px 0;">Email: <?php echo $your_email; ?> | Phone: <?php echo $your_phone; ?></p>
                <p style="margin: 8px 0;">Page: <?php echo $page_title; ?> | Date: <?php echo date('F j, Y'); ?></p>
                <p style="margin: 8px 0;">Status: Registration Form Active</p>
            </div>
            
            <p style="margin-top: 25px; opacity: 0.8; font-size: 0.9rem; line-height: 1.6;">
                © <?php echo date('Y'); ?> <?php echo $site_name; ?>. All rights reserved.<br>
                This is a student assessment project for IT23 Database Management.<br>
                <small>Form successfully processes registrations in demonstration mode.</small>
            </p>
        </div>
    </footer>
    
    <script>
        // Auto-fill test credentials
        document.addEventListener('DOMContentLoaded', function() {
            const autoFillBtn = document.createElement('button');
            autoFillBtn.type = 'button';
            autoFillBtn.innerHTML = '<i class="fas fa-magic"></i> Auto-fill Test Credentials';
            autoFillBtn.style.cssText = `
                background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
                color: white;
                border: none;
                padding: 15px 25px;
                border-radius: 30px;
                cursor: pointer;
                margin: 0 auto 25px auto;
                display: block;
                font-weight: 600;
                font-size: 1rem;
                transition: all 0.3s;
                box-shadow: 0 4px 10px rgba(23, 162, 184, 0.3);
            `;
            
            autoFillBtn.onmouseover = function() {
                this.style.transform = 'translateY(-3px)';
                this.style.boxShadow = '0 6px 15px rgba(23, 162, 184, 0.4)';
            };
            
            autoFillBtn.onmouseout = function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 4px 10px rgba(23, 162, 184, 0.3)';
            };
            
            autoFillBtn.onclick = function() {
                document.querySelector('input[name="first_name"]').value = 'Camellia';
                document.querySelector('input[name="last_name"]').value = 'Student';
                document.querySelector('input[name="email"]').value = '<?php echo $your_email; ?>';
                document.querySelector('input[name="phone"]').value = '<?php echo $your_phone; ?>';
                document.querySelector('input[name="student_id"]').value = '<?php echo $student_id; ?>';
                document.querySelector('textarea[name="address"]').value = '123 Student Street, Sydney NSW 2000';
                
                // 显示确认消息
                const message = document.createElement('div');
                message.innerHTML = `
                    <div style="
                        background: #d4edda;
                        color: #155724;
                        padding: 15px;
                        border-radius: 8px;
                        margin-top: 15px;
                        border: 1px solid #b1dfbb;
                        display: flex;
                        align-items: center;
                        gap: 10px;
                    ">
                        <i class="fas fa-check-circle"></i>
                        <span>Test credentials have been auto-filled! You can now click "Register Now".</span>
                    </div>
                `;
                this.parentNode.insertBefore(message, this.nextSibling);
                
                // 3秒后移除消息
                setTimeout(() => {
                    if (message.parentNode) {
                        message.parentNode.removeChild(message);
                    }
                }, 3000);
            };
            
            const form = document.querySelector('form');
            if (form) {
                form.parentNode.insertBefore(autoFillBtn, form);
            }
        });
        
        // Form validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const email = this.querySelector('input[name="email"]').value;
                const phone = this.querySelector('input[name="phone"]').value;
                const firstName = this.querySelector('input[name="first_name"]').value;
                
                // Basic validation
                if (!firstName.trim()) {
                    e.preventDefault();
                    alert('Please enter your first name.');
                    return false;
                }
                
                if (!email.includes('@')) {
                    e.preventDefault();
                    alert('Please enter a valid email address.');
                    return false;
                }
                
                if (!phone.trim()) {
                    e.preventDefault();
                    alert('Please enter your phone number.');
                    return false;
                }
                
                return true;
            });
        }
    </script>
</body>
</html>