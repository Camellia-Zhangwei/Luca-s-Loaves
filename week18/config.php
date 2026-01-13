<?php
// config.php - 完整配置
session_start();

// ========== 数据库配置 ==========
$host = "sql207.infinityfree.com";
$username = "if0_37804247";
$password = "zw20050311";
$database = "if0_37804247_db_week18";

$conn = new mysqli($host, $username, $password, $database);

// 错误处理
if ($conn->connect_error) {
    $db_error = "Database Error: " . $conn->connect_error;
    $db_connected = false;
} else {
    $db_connected = true;
    $conn->set_charset("utf8mb4");
}

// ========== 网站配置 ==========
$site_name = "Luca's Loaves";
$site_description = "Artisanal Bread Bakery";
$site_url = "http://camellia233190739.freesite.online/513/week18/";

// ========== 你的信息 ==========
$your_name = "Camellia";
$student_id = "IT2233190739";
$your_email = "91179702@qq.com";
$your_phone = "1868046579";

// ========== 当前页面信息 ==========
$current_page = basename($_SERVER['PHP_SELF']);

// ========== 页面标题映射 ==========
$page_titles = [
    'index.php' => 'Home',
    'create_order.php' => 'Place Order',
    'process_order.php' => 'Order Processing',
    'view_orders.php' => 'My Orders',
    'order_details.php' => 'Order Details',
    'customers.php' => 'Customers',
    'register.php' => 'Register',
    'forum.php' => 'Forum',
    'forum_login.php' => 'Login',
    'logout.php' => 'Logout'
];

$page_title = isset($page_titles[$current_page]) ? $page_titles[$current_page] : $site_name;
?>