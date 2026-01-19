<?php
session_start();
require('config.php');

$errors = [];

/* ================= تأكد إن الفورم POST ================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
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
    header("Location: login.php");
    exit;
}

/* ================= VALIDATION ================= */
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Invalid email address";
}

if (empty($password)) {
    $errors['password'] = "Password is required";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST;
    header("Location: login.php");
    exit;
}

/* ================= DATABASE ================= */
$query = mysqli_prepare($con, "SELECT user_id, password FROM users WHERE email = ?");
mysqli_stmt_bind_param($query, "s", $email);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);

if (mysqli_num_rows($result) !== 1) {
    $errors['email'] = "No account found";
} else {
    $user = mysqli_fetch_assoc($result);

    $hashed = password_hash($password, PASSWORD_BCRYPT);
    if (!password_verify($password, $hashed)) {
        $errors['password'] = "Wrong";
    }
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST;
    header("Location: login.php");
    exit;
}

/* ================= LOGIN SUCCESS ================= */
$_SESSION['login'] = true;
$_SESSION['user_id'] = $user['user_id'];

/* Cookie token */
$cookie_token = bin2hex(random_bytes(32));
$update = mysqli_prepare(
    $con,
    "UPDATE users SET cookie=? WHERE user_id=?"
);
mysqli_stmt_bind_param($update, "si", $cookie_token, $user['user_id']);
mysqli_stmt_execute($update);

setcookie("cookie_token", $cookie_token, time() + (86400 * 30), "/", "", false, true);

/* امسح التوكنز */
unset($_SESSION['csrf_token'], $_SESSION['brute_token']);

header("Location: viewPost.php");
exit;
