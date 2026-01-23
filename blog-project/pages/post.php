<?php
session_start();
require('config.php');
require('helpers/security.php');

$token = $_GET['token'] ?? '';

if (empty($token)) {
    header("Location: index.php");
    exit;
}

// Get post with user information
$stmt = $con->prepare(
    "SELECT posts.*, users.name
     FROM posts
     JOIN users ON posts.user_id = users.user_id
     WHERE posts.post_token = ?"
);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirectWithError('index.php', ['post' => 'Post not found']);
}

$post = $result->fetch_assoc();

// Check if current user is the owner
$isOwner = false;
if (isLoggedIn() && getCurrentUserId() == $post['user_id']) {
    $isOwner = true;
}

// Generate CSRF token for delete form
if ($isOwner) {
    $csrfToken = generateCSRFToken();
}

// Get success message from session
$successMessage = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0;
            background: #f4f6f8;
        }

        .navbar {
            background: #fff;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 40px;
        }

        .nav-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            font-size: 22px;
            font-weight: 600;
            color: #4e73df;
            text-decoration: none;
        }

        .nav-link {
            color: #4e73df;
            text-decoration: none;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 6px;
            transition: 0.3s;
        }

        .nav-link:hover {
            background: #f0f2f5;
        }

        .post-container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .post-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .post-title {
            font-size: 32px;
            margin: 0 0 15px;
            color: #333;
        }

        .post-meta {
            color: #999;
            font-size: 14px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .post-meta strong {
            color: #666;
        }

        .post-body {
            font-size: 16px;
            line-height: 1.8;
            color: #444;
            margin-bottom: 30px;
            white-space: pre-wrap;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #c3e6cb;
        }

        .post-actions {
            display: flex;
            gap: 12px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-edit {
            background: #1cc88a;
            color: #fff;
        }

        .btn-edit:hover {
            background: #17a673;
        }

        .btn-delete {
            background: #e74c3c;
            color: #fff;
        }

        .btn-delete:hover {
            background: #c0392b;
        }

        .no-image {
            background: #f8f9fa;
            padding: 60px 20px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 30px;
            color: #999;
        }

        @media (max-width: 768px) {
            .post-container {
                padding: 25px 20px;
                margin: 0 15px;
            }

            .post-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-brand">← Back to Posts</a>
        <?php if (isLoggedIn()): ?>
            <a href="addpost.php" class="nav-link">+ New Post</a>
        <?php endif; ?>
    </div>
</nav>

<!-- Post Content -->
<div class="post-container">
    
    <?php if ($successMessage): ?>
        <div class="success-message"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>

    <?php if (!empty($post['image'])): ?>
        <img src="uploads/posts/<?= htmlspecialchars($post['image']) ?>" 
             alt="<?= htmlspecialchars($post['title']) ?>"
             class="post-image">
    <?php else: ?>
        <div class="no-image">
            <p>No image for this post</p>
        </div>
    <?php endif; ?>

    <h1 class="post-title"><?= htmlspecialchars($post['title']) ?></h1>

    <div class="post-meta">
        <strong>Author:</strong> <?= htmlspecialchars($post['name']) ?>
        <?php if (!empty($post['created_at'])): ?>
            | <strong>Posted:</strong> <?= date('F j, Y', strtotime($post['created_at'])) ?>
        <?php endif; ?>
        <?php if (!empty($post['updated_at']) && $post['updated_at'] != $post['created_at']): ?>
            | <strong>Updated:</strong> <?= date('F j, Y', strtotime($post['updated_at'])) ?>
        <?php endif; ?>
    </div>

    <div class="post-body">
        <?= nl2br(htmlspecialchars($post['body'])) ?>
    </div>

    <?php if ($isOwner): ?>
        <div class="post-actions">
            <a href="edit_post.php?token=<?= $post['post_token'] ?>" class="btn btn-edit">
                Edit Post
            </a>

            <form action="delete_post.php" method="POST" 
                  onsubmit="return confirm('Are you sure you want to delete this post? This action cannot be undone.')"
                  style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="post_token" value="<?= $post['post_token'] ?>">
                <button type="submit" class="btn btn-delete">Delete Post</button>
            </form>
        </div>
    <?php endif; ?>

</div>

</body>
</html>