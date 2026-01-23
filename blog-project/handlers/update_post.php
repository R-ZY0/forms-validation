<?php
session_start();
require('../config/database.php');      // Go up one level
require('../helpers/security.php');     // Go up one level
require('../helpers/validation.php');   // Go up one level
require('../auth.php'); 

// Check authentication
requireAuth();

$errors = [];

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithError('index.php', ['form' => 'Invalid request method']);
}

// CSRF Token Validation
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    redirectWithError('index.php', ['csrf_token' => 'Invalid security token']);
}

// Get input data
$title = trim($_POST['title'] ?? '');
$body = trim($_POST['body'] ?? '');
$postToken = $_POST['post_token'] ?? '';
$userId = getCurrentUserId();

// Validate inputs
if (empty($postToken)) {
    redirectWithError('index.php', ['token' => 'Invalid post token']);
}

// Validate post data
$errors = validatePost($_POST);

// Validate image if uploaded
$updateImage = false;
$newImageName = null;

if (!empty($_FILES['image']['name'])) {
    $imageErrors = validateImage($_FILES['image']);
    if (!empty($imageErrors)) {
        $errors['image'] = implode(', ', $imageErrors);
    } else {
        $updateImage = true;
    }
}

// If there are errors, redirect back
if (!empty($errors)) {
    redirectWithError('edit_post.php?token=' . $postToken, $errors, [
        'title' => $title,
        'body' => $body
    ]);
}

// Get current post to check ownership and get old image
$stmt = $con->prepare(
    "SELECT image FROM posts WHERE post_token = ? AND user_id = ?"
);
$stmt->bind_param("si", $postToken, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirectWithError('index.php', ['auth' => 'Unauthorized or post not found']);
}

$currentPost = $result->fetch_assoc();
$oldImage = $currentPost['image'];

// Upload new image if provided
if ($updateImage) {
    $newImageName = uploadImage($_FILES['image'], 'uploads/posts/');
    
    if ($newImageName === null) {
        redirectWithError('edit_post.php?token=' . $postToken, ['image' => 'Failed to upload image'], [
            'title' => $title,
            'body' => $body
        ]);
    }
}

// Update post in database
if ($updateImage && $newImageName) {
    // Update with new image
    $stmt = $con->prepare(
        "UPDATE posts 
         SET title = ?, body = ?, image = ?, updated_at = NOW()
         WHERE post_token = ? AND user_id = ?"
    );
    $stmt->bind_param("ssssi", $title, $body, $newImageName, $postToken, $userId);
} else {
    // Update without changing image
    $stmt = $con->prepare(
        "UPDATE posts 
         SET title = ?, body = ?, updated_at = NOW()
         WHERE post_token = ? AND user_id = ?"
    );
    $stmt->bind_param("sssi", $title, $body, $postToken, $userId);
}

if (!$stmt->execute()) {
    // If update fails and new image was uploaded, delete it
    if ($newImageName) {
        deleteImage($newImageName);
    }
    
    error_log("Post update error: " . $stmt->error);
    redirectWithError('edit_post.php?token=' . $postToken, ['database' => 'Failed to update post'], [
        'title' => $title,
        'body' => $body
    ]);
}

// If update successful and new image uploaded, delete old image
if ($updateImage && $newImageName && $oldImage) {
    deleteImage($oldImage);
}

$stmt->close();

// Clear CSRF token
unset($_SESSION['csrf_token']);

// Redirect with success message
redirectWithSuccess('post.php?token=' . $postToken, 'Post updated successfully!');
?>