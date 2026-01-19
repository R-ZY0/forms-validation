<?php
require ('auth.php');
session_start();
require('config.php');
$errors=[];
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    $errors['csrf_token'] = "CSRF token is invalid";
}
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST;
    header("Location: login.php");
    exit;
}
$title = trim($_POST['title'] ?? '');
$body = $_POST['body'] ?? '';
$post_token = $_POST['post_token'] ?? '';
$user_id=$_SESSION['user_id'];
$image_name  = $_FILES['image']['name'];
$tmp_name = $_FILES['image']['tmp_name'];
$errors = [];





if (empty($title)) {
    $errors['title'] = "Title is required";
} elseif (strlen($title) < 3) {
    $errors['title'] = "Title must be at least 3 characters";
} elseif (strlen($title) > 255) {
    $errors['title'] = "Title must be less than 255 characters";
}


if (empty($body)) {
    $errors['body'] = "Body is required";
}



if (!$user_id) {
    $errors['auth'] = "Unauthorized access";
}



if (empty($post_token) || !isset($_SESSION['post_token']) || $post_token !== $_SESSION['post_token']) {
    $errors['token'] = "Invalid request";
}




if (!empty($_FILES['image']['name'])) {

    $image_name = $_FILES['image']['name'];
    $tmp_name   = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];

    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext)) {
        $errors['image'] = "Image must be jpg, jpeg, png, or webp";
    } elseif ($image_size > 2 * 1024 * 1024) {
        $errors['image'] = "Image size must be less than 2MB";
    }
}
if (empty($errors)) {
    $new_image_name = null;

    if (!empty($_FILES['image']['name'])) {
        $new_image_name = uniqid() . '.' . $ext;
        move_uploaded_file($tmp_name, "images/" . $new_image_name);
    }


    // INSERT QUERY HERE
}

$stmt = $con->prepare(
    "INSERT INTO posts (titel, body, image, post_token, user_id)
     VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param("ssssi", $title, $body, $new_image_name, $post_token, $user_id);
$stmt->execute();

header("location:index.php");








?>
