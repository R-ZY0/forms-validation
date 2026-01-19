<?php
require('config.php');
session_start();
$erros = [];
if (isset($_SESSION['scrf_token']) && $_SESSION['scrf_token'] != $_POST['csrf_token']) {
    $erros[] = "CSRF token is invalid";
    if (!isset($_POST['brute_token']) || $_POST['brute_token'] != $_SESSION['brute_token']) {
        $erros[] = "Brute token is invalid";
        if (isset($_SESSION['locked']) && $_SESSION['locked'] > 5) {
            $erros[] = "Too many login attempts. Please try again later.";
        }
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];


        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email address";
        }
        if (empty($password)) {
            $errors['password'] = " Required Password field";
        }
        if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $_POST;
        exit;

    }
    $hashpassword= password_hash($password, PASSWORD_BCRYPT);
    $query= mysqli_prepare($con, "SELECT * FROM users WHERE email=?");
    mysqli_stmt_bind_param($query, "s", $email);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
           $_SESSION['login'] = true;
              $_SESSION['user_id'] = $user['user_id'];
              $user_id= $user['user_id'];
              $cocckie_token= bin2hex(random_bytes(32));
              $query_update= mysqli_prepare($con, "UPDATE users SET cookie_token=? WHERE user_id=?");
                mysqli_stmt_bind_param($query_update, "si", $cocckie_token, $user_id);
                mysqli_stmt_execute($query_update);
              setcookie("cocckie_token", $cocckie_token, time() + (86400 * 30), "/");
              header("Location: viewPost.php");


           
    } else {
        // User not found
        $errors['email'] = "No account found";
    }
}}
}