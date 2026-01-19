<?php
session_start();
require('config.php');
$errors = [];
/* ================= تأكد إن الفورم POST ================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: Registration.php");
    exit;
}   
/* ================= CSRF TOKEN ================= */
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    $errors['csrf_token'] = "CSRF token is invalid";
}   
/* ================= BRUTE TOKEN ================= */
if (
    empty($_POST['brute_token']) ||
    empty($_SESSION['brute_token']) ||
    $_POST['brute_token'] !== $_SESSION['brute_token']
) {
    $errors['brute_token'] = "Brute token is invalid";
}   

/* ================= لو التوكنز غلط ================= */
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST;
    header("Location: Registration.php");
    exit;
}   
/* ================= VALIDATION ================= */
$username = trim($_POST['username'] ?? '');

$email = trim($_POST['email'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
 if (strlen($username) < 5 || !preg_match("/^[a-zA-Z-' ]+$/", $username)) {
        $errors['username'] = "Username must at least 5 characters long";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email address";
    }
    if (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $errors['mobile'] = "Mobile number must be 10 digits";
    }
    if (strlen($password) < 6 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password)||empty($password)) {
        $errors['password'] = "Password must be at least 6 characters long and include at least one uppercase letter, one lowercase letter, and one number";
    }
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST;
    header("Location: Registration.php");
    exit;
}
/* ================= DATABASE ================= */
$stmt =$con->prepare("Insert into users (name, email, phone, password) values (?, ?, ?, ?)");
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$stmt->bind_param("ssss", $username, $email, $mobile, $hashed_password);
$stmt->execute();
header("Location: login.php");
?>













?>