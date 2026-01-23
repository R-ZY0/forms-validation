<?php
session_start();
require('../config/database.php');      // Go up one level
require('../helpers/security.php');     // Go up one level
require('../helpers/validation.php');   // Go up one level
require('../auth.php'); 

// Check authentication
requireAuth();

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithError('index.php', ['form' => 'Invalid request method']);
}

// CSRF Token Validation
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    redirectWithError('index.php', ['csrf_token' => 'Invalid security token']);
}

$postToken = $_POST['post_token'] ?? '';
$userId = getCurrentUserId();

if (empty($postToken)) {
    redirectWithError('index.php', ['token' => 'Invalid post token']);
}

// Get post to verify ownership and get image filename
$stmt = $con->prepare(
    "SELECT image FROM posts WHERE post_token = ? AND user_id = ?"
);
$stmt->bind_param("si", $postToken, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirectWithError('index.php', ['auth' => 'Unauthorized or post not found']);
}

$post = $result->fetch_assoc();
$imageName = $post['image'];

// Delete post from database
$stmt = $con->prepare(
    "DELETE FROM posts WHERE post_token = ? AND user_id = ?"
);
$stmt->bind_param("si", $postToken, $userId);

if (!$stmt->execute()) {
    error_log("Post deletion error: " . $stmt->error);
    redirectWithError('index.php', ['database' => 'Failed to delete post']);
}

// Delete associated image file if exists
if ($imageName) {
    deleteImage($imageName);
}

$stmt->close();

// Clear CSRF token
unset($_SESSION['csrf_token']);

// Redirect with success message
redirectWithSuccess('index.php', 'Post deleted successfully!');
?>