

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <style>
        * {
    box-sizing: border-box;
    font-family: "Segoe UI", Tahoma, Arial, sans-serif;
}

 body {
        background: linear-gradient(135deg, #4e73df, #1cc88a);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
    }


.container {
    width: 100%;
    max-width: 500px;
    background: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.container h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #333;
}

.group {
    margin-bottom: 18px;
}

label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #444;
}

input {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    outline: none;
    font-size: 14px;
    transition: 0.3s;
}

input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.15);
}

small {
    display: block;
    margin-top: 5px;
    color: #e63946;
    font-size: 13px;
}

button {
    width: 100%;
    padding: 12px;
    background: #667eea;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 10px;
}

button:hover {
    background: #5a67d8;
}

button:active {
    transform: scale(0.98);
}

.login-btn {
    display: block;
    text-align: center;
    margin-top: 15px;
    padding: 12px;
    background: #f1f1f1;
    color: #333;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: 0.3s;
}

.login-btn:hover {
    background: #e0e0e0;
}

    </style>
</head>
<body>
 
<div class="container">
    <h2>Register</h2>



<form method="POST" action="handel2.php">
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

    
        <div class="group">
            <label>Username:</label>
            <input type="text" name="username" value="<?= $old['username'] ?? '' ?>">
            <small><?= $errors['username'] ?? '' ?></small>
        </div>

       
       
 

    
        <div class="group">
            <label>Email:</label>
            <input type="text" name="email" value="<?= $old['email'] ?? '' ?>">
            <small><?= $errors['email'] ?? '' ?></small>
        </div>

        <div class="group">
            <label>Mobile No:</label>
            <input type="text" name="mobile" value="<?= $old['mobile'] ?? '' ?>">
            <small><?= $errors['mobile'] ?? '' ?></small>
        </div>
    

    
        <div class="group">
            <label>Password:</label>
            <input type="password" name="password">
            <small><?= $errors['password'] ?? '' ?></small>
      

        <div class="group">
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password">
            <small><?= $errors['confirm_password'] ?? '' ?></small>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
         <div class="form-group token-box">
    <label>Write this token: <strong><?php echo $brute_token; ?></strong></label>
    <input type="text" name="brute_token" placeholder="Enter  Token">
    <small><?php echo $errors['brute_token'] ?? '' ?></small>
    </div>
   

    

    <button type="submit">Submit</button>
    <a href="login.php" class="login-btn">Already have an account? Login</a>
</form>

</div>
</body>
</html>
