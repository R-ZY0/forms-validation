<?php
session_start();
require('../config/database.php');      // Go up one level
require('../helpers/security.php');     // Go up one level
require('../helpers/validation.php');   // Go up one level
require('../auth.php'); 
$errors = [];

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithError('login.php', ['form' => 'Invalid request method']);
}

// CSRF Token Validation
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    redirectWithError('login.php', ['csrf_token' => 'Invalid security token. Please try again.']);
}

// Get and validate input
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Basic validation
$errors = validateLogin($_POST);

if (!empty($errors)) {
    redirectWithError('login.php', $errors, ['email' => $email]);
}

// Rate limiting check
if (!checkRateLimit($email)) {
    $remaining = getRateLimitRemaining($email);
    $minutes = ceil($remaining / 60);
    redirectWithError('login.php', [
        'rate_limit' => "Too many login attempts. Please try again in {$minutes} minute(s)."
    ], ['email' => $email]);
}

// Database query - get user
$stmt = $con->prepare("SELECT user_id, password, name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $errors['email'] = "Invalid email or password";
} else {
    $user = $result->fetch_assoc();
    
    // CORRECT: Verify the input password against the stored hashed password
    if (!password_verify($password, $user['password'])) {
        $errors['password'] = "Invalid email or password";
    }
}

// If errors exist, redirect back
if (!empty($errors)) {
    redirectWithError('login.php', $errors, ['email' => $email]);
}

// Login successful - clear rate limit
clearRateLimit($email);

// Set session variables
$_SESSION['login'] = true;
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['user_name'] = $user['name'];

// Generate and store cookie token for "Remember Me"
$cookieToken = bin2hex(random_bytes(32));
$stmt = $con->prepare("UPDATE users SET cookie = ? WHERE user_id = ?");
$stmt->bind_param("si", $cookieToken, $user['user_id']);
$stmt->execute();

// Set secure cookie (30 days)
setcookie(
    "cookie_token",
    $cookieToken,
    [
        'expires' => time() + (86400 * 30),
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']), // Only over HTTPS in production
        'httponly' => true,
        'samesite' => 'Strict'
    ]
);

// Clear old tokens
unset($_SESSION['csrf_token']);

// Redirect to dashboard
redirectWithSuccess('index.php', 'Welcome back, ' . $user['name'] . '!');
?>