<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Posts</title>
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
            align-items: center;
        }

        .btn {
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            color: #fff;
        }

        .btn-posts {
            background: #3498db;
        }

        .btn-add {
            background: #1cc88a;
        }

        .btn-logout {
            background: #e74c3c;
        }

        /* ===== POSTS GRID ===== */
        .posts-container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        /* ===== POST CARD ===== */
        .post-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            transition: 0.3s;
        }

        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        }

        .post-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .post-content {
            padding: 18px;
        }

        .post-content h4 {
            margin: 0 0 10px;
            font-size: 18px;
            color: #333;
        }

        .post-meta {
            font-size: 13px;
            color: #999;
            margin-bottom: 10px;
        }

        .post-content p {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .post-actions {
            text-align: right;
        }

        .post-actions a {
            padding: 6px 14px;
            border-radius: 6px;
            background: #4e73df;
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            transition: 0.3s;
        }

        .post-actions a:hover {
            background: #2e59d9;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 600px) {
            .post-card img {
                height: 170px;
            }
        }
    </style>
</head>

<body>

<!-- ===== NAVBAR ===== -->
<header class="site-header">
    <div class="container">
        <div class="logo">
            <a href="#">MyWebsite</a>
        </div>

        <nav class="nav-actions">
            <a href="index.php" class="btn btn-posts">All Posts</a>
            <a href="addpost.php" class="btn btn-add">Add Post</a>
            <a href="delete_coocke.php" class="btn btn-logout">Logout</a>
        </nav>
    </div>
</header>

<!-- ===== POSTS ===== -->
<div class="posts-container">

   
<?php
session_start();
require ('config.php');
require ('getPosts.php')

?>
    <!-- Post 2 -->
    <?php if (!empty($errors['result'])) { ?>
    <p style="text-align:center; color:#999; font-size:16px;">
        <?= $errors['result']; ?>
    </p>
    <?php } ?>
    <?php if ($result && $result->num_rows > 0) { ?>
    <?php while ($post = $result->fetch_assoc()) { 
        //  print_r($post);
        ?>
   
        <div class="post-card">
            <img src="images/<?= htmlspecialchars($post['image'] ?? 'default.jpg') ?>">

            <div class="post-content">
                <h4><?= htmlspecialchars($post['titel']) ?></h4>

                <div class="post-meta">
                    Created by: <?= htmlspecialchars($post['name']) ?>
                </div>

                <p><?= htmlspecialchars(substr($post['body'], 0, 120)) ?>...</p>

                <div class="post-actions">
                    <a href="post.php?token=<?= $post['post_token'] ?>">View</a>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>

    


</div>

</body>
</html>
