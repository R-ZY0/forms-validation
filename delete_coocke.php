<?php
session_start();
require('config.php'); // $con mysqli connection

/* =======================
   1. DELETE COOKIE FROM DATABASE
======================= */

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $query = mysqli_prepare($con, "UPDATE users SET cookie = NULL WHERE user_id = ?");
    mysqli_stmt_bind_param($query, "i", $user_id);
    mysqli_stmt_execute($query);
    mysqli_stmt_close($query);
}

/* =======================
   2. DELETE COOKIE FROM BROWSER
======================= */

if (isset($_COOKIE['cookie'])) {
    setcookie("cookie", "", time() - 3600, "/", "", false, true);
}

/* Delete PHP session cookie */
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), "", time() - 3600, "/");
}

/* =======================
   3. DESTROY SESSION
======================= */

$_SESSION = [];
session_destroy();

/* =======================
   4. REDIRECT
======================= */

header("Location: login.php");
exit;
