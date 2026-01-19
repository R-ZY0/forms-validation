<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <style>
    * {
        box-sizing: border-box;
        font-family: Arial, Helvetica, sans-serif;
    }

    body {
        background: linear-gradient(135deg, #4e73df, #1cc88a);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
    }

    form {
        background: #fff;
        width: 100%;
        max-width: 400px;
        padding: 25px 30px;
        border-radius: 10px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }

    h3 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #555;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        outline: none;
        transition: border-color 0.3s;
    }

    input:focus {
        border-color: #4e73df;
    }

    .error {
        color: #e74c3c;
        font-size: 13px;
        margin-top: 5px;
    }

    small {
        display: block;
        color: #e74c3c;
        margin-top: 5px;
        font-size: 13px;
    }

    button {
        width: 100%;
        background: #4e73df;
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s;
        margin-top: 15px;
    }

    button:hover {
        background: #375acb;
    }

    .token-box {
        background: #f8f9fc;
        padding: 10px;
        border-radius: 5px;
        margin-top: 10px;
        font-size: 14px;
    }
</style>

</head>

<body>
    <?php
    session_start();
    $brute_token = bin2hex(random_bytes(2));
    $_SESSION['brute_token'] = $brute_token;

    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;

    $errors = $_SESSION['errors'] ?? [];
    $old = $_SESSION['old'] ?? [];

    /* امسح errors و old بس */
    unset($_SESSION['errors'], $_SESSION['old']);


    ?>
    <form action="handel.php" method="post">


        <h3>Login Here</h3>
        <div class="form-group">
            <label>Email:</label>
            <input type="text" name="email" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
            <?php if (!empty($errors['email'])): ?>
                <div class="error"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" value="<?php echo htmlspecialchars($old['password'] ?? ''); ?>">
            <?php if (!empty($errors['password'])): ?>
                <div class="error"><?php echo $errors['password']; ?></div>
            <?php endif; ?>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <div class="form-group token-box">
    <label>Write this token: <strong><?php echo $brute_token; ?></strong></label>
    <input type="text" name="brute_token" placeholder="Enter Brute Token">
    <small><?php echo $errors['brute_token'] ?? '' ?></small>
</div>


        <button type="submit" name="submit">Login</button>
    </form>
</body>

</html>