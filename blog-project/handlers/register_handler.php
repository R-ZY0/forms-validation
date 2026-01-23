<?php
session_start();
require('../config/database.php');      // Go up one level
require('../helpers/security.php');     // Go up one level
require('../helpers/validation.php');   // Go up one level
require('../auth.php'); 

$errors = [];

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithError('registration.php', ['form' => 'Invalid request method']);
}

// CSRF Token Validation
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    redirectWithError('registration.php', ['csrf_token' => 'Invalid security token. Please try again.']);
}

// Get input data
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validate all fields
$errors = validateRegistration($_POST);

// Check if email already exists
if (empty($errors['email']) && emailExists($con, $email)) {
    $errors['email'] = "This email is already registered";
}

// Check if username already exists
if (empty($errors['username']) && usernameExists($con, $username)) {
    $errors['username'] = "This username is already taken";
}

// If there are validation errors, redirect back
if (!empty($errors)) {
    redirectWithError('registration.php', $errors, [
        'username' => $username,
        'email' => $email,
        'mobile' => $mobile
    ]);
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Insert new user
$stmt = $con->prepare(
    "INSERT INTO users (name, email, phone, password, created_at) VALUES (?, ?, ?, ?, NOW())"
);
$stmt->bind_param("ssss", $username, $email, $mobile, $hashedPassword);

if (!$stmt->execute()) {
    error_log("Registration error: " . $stmt->error);
    redirectWithError('registration.php', ['database' => 'Registration failed. Please try again.'], [
        'username' => $username,
        'email' => $email,
        'mobile' => $mobile
    ]);
}

// Registration successful
$stmt->close();

// Clear CSRF token
unset($_SESSION['csrf_token']);

// Redirect to login with success message
redirectWithSuccess('login.php', 'Registration successful! Please login.');
?>