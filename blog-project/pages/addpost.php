<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Post</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0;
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            color: #333;
        }

        /* ===== Header ===== */
        .page-heading {
            padding: 70px 0;
            text-align: center;
            color: #fff;
        }

        .page-heading h4 {
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 300;
            margin-bottom: 10px;
        }

        .page-heading h2 {
            font-size: 32px;
            font-weight: 600;
        }

        /* ===== Form Card ===== */
        .form-container {
            max-width: 550px;
            background: #fff;
            margin: -40px auto 40px;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .form-container h3 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
            color: #4e73df;
        }

        /* ===== Inputs ===== */
        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .form-control,
        .form-control-file {
            width: 100%;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #d1d3e2;
            outline: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 3px rgba(78,115,223,0.15);
        }

        textarea.form-control {
            resize: vertical;
        }

        .mb-3 {
            margin-bottom: 20px;
        }

        /* ===== Button ===== */
        .btn-primary {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            background: #4e73df;
            color: #fff;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: #2e59d9;
        }

        /* ===== Responsive ===== */
        @media (max-width: 600px) {
            .form-container {
                margin: -20px 15px 30px;
                padding: 25px;
            }

            .page-heading h2 {
                font-size: 26px;
            }
        }
        /* ===== Back Button ===== */
.back-btn {
    margin-bottom: 15px;
}

.back-btn a {
    display: inline-block;
    text-decoration: none;
    color: #4e73df;
    font-size: 14px;
    font-weight: 500;
    transition: 0.3s;
}

.back-btn a:hover {
    color: #2e59d9;
    transform: translateX(-3px);
}

    </style>
</head>
<body>

<!-- ===== Page Heading ===== -->
<div class="page-heading">
    <h4>New Post</h4>
    <h2>Add New Personal Post</h2>
</div>
<?php
session_start();
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$_SESSION['post_token'] = bin2hex(random_bytes(32));



?>
<!-- ===== Form ===== -->
<div class="form-container">
    <h3>Add New Post</h3>

    <form method="POST" action="add.php" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Enter post title">
        </div>

        <div class="mb-3">
            <label for="body" class="form-label">Body</label>
            <textarea class="form-control" id="body" name="body" rows="5" placeholder="Write your post..."></textarea>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" class="form-control-file" id="image" name="image">
        </div>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="post_token" value="<?= $_SESSION['post_token']; ?>">

        <button type="submit" class="btn-primary" name="submit">Submit</button>
        <div class="back-btn">

    <a href="index.php">← Back to Home</a>
</div>
    </form>
</div>

</body>
</html>
