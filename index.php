<?php
session_start();
require('auth.php');
require('config.php');

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Navbar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f8;
        }

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
            align-items: center;
        }

        .btn {
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
        }

        .btn-posts {
            background: #3498db;
            color: #fff;
        }

        .btn-posts:hover {
            background: #2980b9;
        }

        .btn-logout {
            background: #e74c3c;
            color: #fff;
            border: none;
        }

        .btn-logout:hover {
            background: #c0392b;
        }
    </style>
</head>

<body>

<header class="site-header">
    <div class="container">
        <div class="logo">
            <a href="index.php">MyWebsite</a>
        </div>

        <nav class="nav-actions">
            <!-- All Posts -->
            <a href="posts.php" class="btn btn-posts">All Posts</a>

            <!-- Logout (POST) -->
            <form action="delete_coocke.php" method="post">
                <button type="submit" class="btn btn-logout">Logout</button>
            </form>
        </nav>
    </div>
</header>
