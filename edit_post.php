<?php
session_start();
require('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$token = $_GET['token'] ?? '';

if (empty($token)) {
    header("Location: index.php");
    exit;
}

/* =======================
   Get Post & Check Owner
======================= */
$stmt = $con->prepare(
    "SELECT * FROM posts WHERE post_token = ? AND user_id = ?"
);
$stmt->bind_param("si", $token, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h2 style='text-align:center'>Unauthorized or Post not found</h2>";
    exit;
}

$post = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f8;
        }

        .form-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            color: #fff;
        }

        .btn-save {
            background: #1cc88a;
        }

        .btn-cancel {
            background: #6c757d;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Post</h2>

    <form action="update_post.php" method="POST">
        <input type="hidden" name="post_token" value="<?= $post['post_token'] ?>">

        <label>Title</label>
        <input type="text" name="title" class="form-control"
               value="<?= htmlspecialchars($post['titel']) ?>">

        <label>Body</label>
        <textarea name="body" class="form-control" rows="5"><?= htmlspecialchars($post['body']) ?></textarea>

        <button type="submit" class="btn btn-save">Save Changes</button>
        <a href="post.php?token=<?= $post['post_token'] ?>" class="btn btn-cancel">Cancel</a>
    </form>
</div>

</body>
</html>
