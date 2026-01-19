<?php
session_start();
require('config.php');

$token = $_GET['token'] ?? '';

if (empty($token)) {
    header("Location: index.php");
    exit;
}

/* =======================
   Get Post
======================= */
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
    echo "<h2 style='text-align:center'>Post not found</h2>";
    exit;
}

$post = $result->fetch_assoc();

/* =======================
   Check Owner
======================= */
$isOwner = false;
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']) {
    $isOwner = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['titel']) ?></title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f8;
        }

        .post-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
        }

        .post-container img {
            width: 100%;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .post-actions {
            margin-top: 25px;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            color: #fff;
            font-size: 14px;
        }

        .btn-edit {
            background: #1cc88a;
        }

        .btn-delete {
            background: #e74c3c;
        }
    </style>
</head>

<body>

<div class="post-container">

    <?php if (!empty($post['image'])) { ?>
        <img src="images/<?= htmlspecialchars($post['image']) ?>">
    <?php } ?>

    <h2><?= htmlspecialchars($post['titel']) ?></h2>
    <p><strong>Created by:</strong> <?= htmlspecialchars($post['name']) ?></p>

    <p><?= nl2br(htmlspecialchars($post['body'])) ?></p>

    <!-- ===== Actions ===== -->
    <?php if ($isOwner) { ?>
        <div class="post-actions">
            <a href="edit_post.php?token=<?= $post['post_token'] ?>" class="btn btn-edit">Edit</a>

            <form action="delete_post.php" method="POST" onsubmit="return confirm('Are you sure?')">
                <input type="hidden" name="post_token" value="<?= $post['post_token'] ?>">
                <button type="submit" class="btn btn-delete">Delete</button>
            </form>
        </div>
    <?php } ?>

</div>

</body>
</html>
