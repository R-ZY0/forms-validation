<?php
session_start();
require('config.php');
require('auth.php');
require('helpers/security.php');
require('helpers/validation.php');

// Check authentication
requireAuth();

$errors = [];

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithError('addpost.php', ['form' => 'Invalid request method']);
}

// CSRF Token Validation
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    redirectWithError('addpost.php', ['csrf_token' => 'Invalid security token']);
}

// Post Token Validation
$postToken = $_POST['post_token'] ?? '';
if (empty($postToken) || !isset($_SESSION['post_token']) || !hash_equals($_SESSION['post_token'], $postToken)) {
    redirectWithError('addpost.php', ['token' => 'Invalid request token']);
}

// Get input data
$title = trim($_POST['title'] ?? '');
$body = trim($_POST['body'] ?? '');
$userId = getCurrentUserId();

// Validate post data
$errors = validatePost($_POST);

// Validate image if uploaded
if (!empty($_FILES['image']['name'])) {
    $imageErrors = validateImage($_FILES['image']);
    if (!empty($imageErrors)) {
        $errors['image'] = implode(', ', $imageErrors);
    }
}

// If there are errors, redirect back
if (!empty($errors)) {
    redirectWithError('addpost.php', $errors, [
        'title' => $title,
        'body' => $body
    ]);
}

// Upload image
$imageName = null;
if (!empty($_FILES['image']['name'])) {
    $imageName = uploadImage($_FILES['image'], 'uploads/posts/');
    
    if ($imageName === null) {
        redirectWithError('addpost.php', ['image' => 'Failed to upload image'], [
            'title' => $title,
            'body' => $body
        ]);
    }
}

// Insert post into database
$stmt = $con->prepare(
    "INSERT INTO posts (title, body, image, post_token, user_id, created_at) 
     VALUES (?, ?, ?, ?, ?, NOW())"
);

$stmt->bind_param("ssssi", $title, $body, $imageName, $postToken, $userId);

if (!$stmt->execute()) {
    // If insert fails, delete uploaded image
    if ($imageName) {
        deleteImage($imageName);
    }
    
    error_log("Post creation error: " . $stmt->error);
    redirectWithError('addpost.php', ['database' => 'Failed to create post. Please try again.'], [
        'title' => $title,
        'body' => $body
    ]);
}

$stmt->close();

// Clear tokens
unset($_SESSION['csrf_token']);
unset($_SESSION['post_token']);

// Redirect with success message
redirectWithSuccess('index.php', 'Post created successfully!');
?>