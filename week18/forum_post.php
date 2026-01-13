<?php
// forum_post.php - 论坛发帖处理
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_email']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forum.php');
    exit;
}

$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$author_email = $_SESSION['user_email'];

if (!$db_connected) {
    header('Location: forum.php?error=Database+connection+failed');
    exit;
}

// 插入论坛帖子
$sql = "INSERT INTO wp_forum_posts (author_email, title, content, post_date) 
        VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $author_email, $title, $content);
$stmt->execute();
$stmt->close();

header('Location: forum.php?success=Post+created+successfully');
exit;
?>