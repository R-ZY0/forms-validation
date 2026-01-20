<?php
session_start();
require('config.php');
require ('auth.php');
if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized");
}

$post_token = $_POST['post_token'] ?? '';

$stmt = $con->prepare(
    "DELETE FROM posts WHERE post_token = ? AND user_id = ?"
);
$stmt->bind_param("si", $post_token, $_SESSION['user_id']);
$stmt->execute();

header("Location: index.php");
exit;
?>