<?php
// index_test.php - 简单测试首页
require_once 'config.php';

// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_name; ?> - Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .header { background: #8B4513; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { background: #333; color: white; text-align: center; padding: 15px; margin-top: 20px; }
        .status { margin: 20px 0; padding: 15px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo $site_name; ?></h1>
        <p><?php echo $site_description; ?></p>
        <p>Welcome, <?php echo $your_name; ?> (ID: <?php echo $student_id; ?>)</p>
    </div>
    
    <div class="content">
        <h2>System Status Test</h2>
        
        <div class="status <?php echo $db_connected ? 'success' : 'error'; ?>">
            <h3>Database Connection</h3>
            <?php if ($db_connected): ?>
                <p>✅ Connected to database: <?php echo $database; ?></p>
                <p>✅ Tables exist: products, wp_fluentcrm_subscribers, wp_orders, etc.</p>
            <?php else: ?>
                <p>❌ Connection failed: <?php echo $db_error; ?></p>
            <?php endif; ?>
        </div>
        
        <div class="status success">
            <h3>Website Information</h3>
            <p>URL: <?php echo $site_url; ?></p>
            <p>Current Page: <?php echo $current_page; ?></p>
            <p>Page Title: <?php echo $page_title; ?></p>
        </div>
        
        <div class="status success">
            <h3>Your Information</h3>
            <p>Name: <?php echo $your_name; ?></p>
            <p>Student ID: <?php echo $student_id; ?></p>
            <p>Email: <?php echo $your_email; ?></p>
            <p>Phone: <?php echo $your_phone; ?></p>
        </div>
        
        <h3>Quick Actions</h3>
        <ul>
            <li><a href="?action=test">Test Database Query</a></li>
            <li><a href="?action=check_tables">Check All Tables</a></li>
            <li><a href="?action=add_subscriber">Add My Subscriber Record</a></li>
        </ul>
        
        <?php
        if (isset($_GET['action'])) {
            echo "<div style='margin-top:20px; padding:15px; background:#f0f0f0;'>";
            echo "<h4>Action Result:</h4>";
            
            switch ($_GET['action']) {
                case 'test':
                    $result = $conn->query("SELECT COUNT(*) as count FROM products");
                    if ($result) {
                        $row = $result->fetch_assoc();
                        echo "Products in database: " . $row['count'];
                    }
                    break;
                    
                case 'check_tables':
                    $result = $conn->query("SHOW TABLES");
                    echo "Tables found:<br>";
                    while ($row = $result->fetch_array()) {
                        echo "- " . $row[0] . "<br>";
                    }
                    break;
                    
                case 'add_subscriber':
                    // 确保你在wp_fluentcrm_subscribers表中
                    $check = $conn->query("SELECT * FROM wp_fluentcrm_subscribers WHERE email = '$your_email'");
                    if ($check->num_rows > 0) {
                        echo "✅ You are already in subscribers table";
                    } else {
                        $sql = "INSERT INTO wp_fluentcrm_subscribers (first_name, last_name, email, phone, user001) 
                                VALUES ('Alex', 'Chen', '$your_email', '$your_phone', '$student_id')";
                        if ($conn->query($sql)) {
                            echo "✅ Added your record to subscribers table";
                        } else {
                            echo "❌ Error: " . $conn->error;
                        }
                    }
                    break;
            }
            echo "</div>";
        }
        ?>
    </div>
    
    <div class="footer">
        <p>© <?php echo date('Y'); ?> <?php echo $site_name; ?>. All rights reserved.</p>
        <p>Student: <?php echo $your_name; ?> | ID: <?php echo $student_id; ?></p>
        <p>Page: <?php echo $page_title; ?> | Time: <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>
</body>
</html>