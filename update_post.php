<?php
session_start();
require('config.php');

if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized");
}

$title = trim($_POST['title'] ?? '');
$body  = trim($_POST['body'] ?? '');
$post_token = $_POST['post_token'] ?? '';

if (empty($title) || empty($body) || empty($post_token)) {
    exit("Invalid data");
}

/* =======================
   Update (Owner only)
======================= */
$stmt = $con->prepare(
    "UPDATE posts 
     SET titel= ?, body = ?
     WHERE post_token = ? AND user_id = ?"
);
$stmt->bind_param("sssi", $titel, $body, $post_token, $_SESSION['user_id']);
$stmt->execute();

header("Location: post.php?token=" . $post_token);
exit;
