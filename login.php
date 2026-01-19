<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
        <div>
            <label for="">Write this <?php echo $brute_token; ?></label>
            <input type="text" name="brute_token" placeholder="Enter Brute Token">
            <small><?php echo $errors['brute_token'] ?? '' ?></small>
        </div>

        <button type="submit" name="submit">Login</button>
    </form>
</body>

</html>