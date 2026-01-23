<?php
/**
 * Validation Helper Functions
 */

/**
 * Validate registration data
 */
function validateRegistration($data) {
    $errors = [];
    
    // Username validation
    $username = trim($data['username'] ?? '');
    if (empty($username)) {
        $errors['username'] = "Username is required";
    } elseif (strlen($username) < 3) {
        $errors['username'] = "Username must be at least 3 characters long";
    } elseif (strlen($username) > 50) {
        $errors['username'] = "Username must be less than 50 characters";
    } elseif (!preg_match("/^[a-zA-Z0-9_-]+$/", $username)) {
        $errors['username'] = "Username can only contain letters, numbers, underscores, and hyphens";
    }
    
    // Email validation
    $email = trim($data['email'] ?? '');
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email address";
    }
    
    // Mobile validation
    $mobile = trim($data['mobile'] ?? '');
    if (empty($mobile)) {
        $errors['mobile'] = "Mobile number is required";
    } elseif (!preg_match("/^[0-9]{10,15}$/", $mobile)) {
        $errors['mobile'] = "Mobile number must be 10-15 digits";
    }
    
    // Password validation
    $password = $data['password'] ?? '';
    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters long";
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $errors['password'] = "Password must include at least one uppercase letter";
    } elseif (!preg_match("/[a-z]/", $password)) {
        $errors['password'] = "Password must include at least one lowercase letter";
    } elseif (!preg_match("/[0-9]/", $password)) {
        $errors['password'] = "Password must include at least one number";
    } elseif (!preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password)) {
        $errors['password'] = "Password must include at least one special character";
    }
    
    // Confirm password
    $confirmPassword = $data['confirm_password'] ?? '';
    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = "Passwords do not match";
    }
    
    return $errors;
}

/**
 * Validate login data
 */
function validateLogin($data) {
    $errors = [];
    
    // Email validation
    $email = trim($data['email'] ?? '');
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email address";
    }
    
    // Password validation
    if (empty($data['password'] ?? '')) {
        $errors['password'] = "Password is required";
    }
    
    return $errors;
}

/**
 * Validate post data
 */
function validatePost($data) {
    $errors = [];
    
    // Title validation
    $title = trim($data['title'] ?? '');
    if (empty($title)) {
        $errors['title'] = "Title is required";
    } elseif (strlen($title) < 3) {
        $errors['title'] = "Title must be at least 3 characters";
    } elseif (strlen($title) > 255) {
        $errors['title'] = "Title must be less than 255 characters";
    }
    
    // Body validation
    $body = trim($data['body'] ?? '');
    if (empty($body)) {
        $errors['body'] = "Body is required";
    } elseif (strlen($body) < 10) {
        $errors['body'] = "Body must be at least 10 characters";
    } elseif (strlen($body) > 10000) {
        $errors['body'] = "Body must be less than 10000 characters";
    }
    
    return $errors;
}

/**
 * Check if email exists in database
 */
function emailExists($con, $email, $excludeUserId = null) {
    if ($excludeUserId) {
        $stmt = $con->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->bind_param("si", $email, $excludeUserId);
    } else {
        $stmt = $con->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

/**
 * Check if username exists in database
 */
function usernameExists($con, $username, $excludeUserId = null) {
    if ($excludeUserId) {
        $stmt = $con->prepare("SELECT user_id FROM users WHERE name = ? AND user_id != ?");
        $stmt->bind_param("si", $username, $excludeUserId);
    } else {
        $stmt = $con->prepare("SELECT user_id FROM users WHERE name = ?");
        $stmt->bind_param("s", $username);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

/**
 * Sanitize and validate integer
 */
function validateInt($value, $min = null, $max = null) {
    $value = filter_var($value, FILTER_VALIDATE_INT);
    
    if ($value === false) {
        return null;
    }
    
    if ($min !== null && $value < $min) {
        return null;
    }
    
    if ($max !== null && $value > $max) {
        return null;
    }
    
    return $value;
}

/**
 * Validate URL
 */
function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}
?>