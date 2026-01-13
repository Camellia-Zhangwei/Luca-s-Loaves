<?php
// logout.php - 登出
session_start();
session_destroy();

// 重定向到首页
header('Location: index.php?logout=success');
exit;
?>