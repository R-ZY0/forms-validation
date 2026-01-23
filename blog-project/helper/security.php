<?php
/**
 * Security Helper Functions
 */

/**
 * Generate CSRF Token
 */
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize input data
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect with error messages
 */
function redirectWithError($location, $errors = [], $oldData = []) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    }
    
    if (!empty($oldData)) {
        $_SESSION['old'] = $oldData;
    }
    
    header("Location: $location");
    exit;
}

/**
 * Redirect with success message
 */
function redirectWithSuccess($location, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['success'] = $message;
    header("Location: $location");
    exit;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['login']) && $_SESSION['login'] === true;
}

/**
 * Require authentication
 */
function requireAuth($redirectTo = 'login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirectTo");
        exit;
    }
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return $_SESSION['user_id'] ?? null;
}

/**
 * Validate image upload
 */
function validateImage($file, $maxSize = 2097152) { // 2MB default
    $errors = [];
    
    if (empty($file['name'])) {
        return $errors; // No file uploaded - not an error
    }
    
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $allowed_mime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "File upload error occurred";
        return $errors;
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        $errors[] = "Image size must be less than " . ($maxSize / 1024 / 1024) . "MB";
    }
    
    // Check extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext)) {
        $errors[] = "Image must be jpg, jpeg, png, webp, or gif";
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, $allowed_mime)) {
        $errors[] = "Invalid image file type";
    }
    
    // Verify it's actually an image
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        $errors[] = "File is not a valid image";
    }
    
    return $errors;
}

/**
 * Upload image safely
 */
function uploadImage($file, $uploadDir = 'uploads/posts/') {
    if (empty($file['name'])) {
        return null;
    }
    
    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $newFileName = uniqid('img_', true) . '.' . $ext;
    $targetPath = $uploadDir . $newFileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $newFileName;
    }
    
    return null;
}

/**
 * Delete image file
 */
function deleteImage($filename, $uploadDir = 'uploads/posts/') {
    if (empty($filename)) {
        return false;
    }
    
    $filePath = $uploadDir . $filename;
    
    if (file_exists($filePath) && is_file($filePath)) {
        return unlink($filePath);
    }
    
    return false;
}

/**
 * Rate limiting for login attempts
 */
function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 900) { // 15 minutes
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $key = 'rate_limit_' . md5($identifier);
    $now = time();
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['attempts' => 1, 'first_attempt' => $now];
        return true;
    }
    
    $data = $_SESSION[$key];
    
    // Reset if time window has passed
    if ($now - $data['first_attempt'] > $timeWindow) {
        $_SESSION[$key] = ['attempts' => 1, 'first_attempt' => $now];
        return true;
    }
    
    // Increment attempts
    $_SESSION[$key]['attempts']++;
    
    // Check if exceeded limit
    if ($_SESSION[$key]['attempts'] > $maxAttempts) {
        return false;
    }
    
    return true;
}

/**
 * Get remaining lockout time
 */
function getRateLimitRemaining($identifier, $timeWindow = 900) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $key = 'rate_limit_' . md5($identifier);
    
    if (!isset($_SESSION[$key])) {
        return 0;
    }
    
    $data = $_SESSION[$key];
    $elapsed = time() - $data['first_attempt'];
    $remaining = $timeWindow - $elapsed;
    
    return max(0, $remaining);
}

/**
 * Clear rate limit for user (after successful login)
 */
function clearRateLimit($identifier) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $key = 'rate_limit_' . md5($identifier);
    unset($_SESSION[$key]);
}
?>