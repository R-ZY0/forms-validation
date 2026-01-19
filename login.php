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
     $_SESSION['csrf_token']= $csrf_token;
    ?>
    <form action="handel.php" method="post">
        <h3>Login Here</h3>
        <div class="group">
            <label>Email:</label>
            <input type="text" name="email" value="<?= $old['email'] ?? '' ?>">
            <small><?= $errors['email'] ?? '' ?></small>
        </div>

         <div class="group">
            <label>Password:</label>
            <input type="password" name="password" >
            <small><?= $errors['password'] ?? '' ?></small>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
       <label for="">Write this <?php echo $brute_token; ?></label>
       <input type="text" nbame="brute_token" placeholder="Enter Brute Token">
        <button type="submit" name="submit">Login</button>
    </form>
</body>
</html>