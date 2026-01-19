
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Header with Login & Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            margin: 0;
            background: #f4f6f8;
        }

        /* ===== HEADER ===== */
        .site-header {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 15px 0;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo a {
            text-decoration: none;
            font-size: 22px;
            font-weight: bold;
            color: #4e73df;
        }

        .nav-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
            border: 1px solid transparent;
        }

        .btn-login {
            color: #4e73df;
            border-color: #4e73df;
        }

        .btn-login:hover {
            background: #4e73df;
            color: #fff;
        }

        .btn-register {
            background: #1cc88a;
            color: #fff;
            border-color: #1cc88a;
        }

        .btn-register:hover {
            background: #17a673;
        }

        .btn-logout {
            background: #e74c3c;
            color: #fff;
            border-color: #e74c3c;
        }

        .btn-logout:hover {
            background: #c0392b;
        }

        /* ===== DEMO CONTENT ===== */
        .content {
            padding: 40px;
            text-align: center;
        }
    </style>
</head>
<body>
<?php
session_start();
require('config.php'); // mysqli connection

/* =======================
   1. DELETE TOKEN FROM DATABASE
======================= */

if (isset($_COOKIE['cookie'])) {
    $token = $_COOKIE['cookie'];

    // Remove token from DB
    $stmt = $conn->prepare("UPDATE users SET cookie = NULL WHERE cookie = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->close();
}

/* =======================
   2. DELETE COOKIE FROM BROWSER
======================= */

// Delete remember me cookie
if (isset($_COOKIE['cookie'])) {
    setcookie("cookie", "", time() - 3600, "/", "", false, true);
}

// Delete PHP session cookie
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

header("Location: index.php");
exit;

?>
<!-- ===== HEADER ===== -->
<header class="site-header">
    <div class="container">
        <div class="logo">
            <a href="index.php">MyWebsite</a>
        </div>

        <nav class="nav-actions">
            <?php if (!empty($_SESSION['login'])): ?>
                <a href="" class="btn btn-logout">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-login">Login</a>
                <a href="Registration.php" class="btn btn-register">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<!-- ===== PAGE CONTENT (Example) ===== -->
<div class="content">
    <h2>Welcome to the website</h2>
    <p>This is a demo page with header buttons.</p>
</div>

</body>
</html>
