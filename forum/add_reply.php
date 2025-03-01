<?php
session_start();
include '../netting/connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
    $thread_id = intval($_POST['thread_id']);
    $content   = trim($_POST['content']);
    $user_id   = $_SESSION['user_id'];

    if (empty($content)) {
        header("Location: thread.php?id=" . $thread_id);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO forum_comments (thread_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
    if ($stmt) {
        $stmt->bind_param("iis", $thread_id, $user_id, $content);
        $stmt->execute();
        $stmt->close();
    }
}
header("Location: thread.php?id=" . $thread_id);
exit;
?>
