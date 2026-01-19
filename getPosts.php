<?php
require ('config.php');
$errors = [];
$result = null;

$stmt = $con->prepare(
    "SELECT posts.*, users.name
     FROM posts
     JOIN users ON posts.user_id = users.user_id
     ORDER BY posts.Post_id DESC"
);

if (!$stmt) {
    $errors['db'] = "Database error";
} else {
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $errors['result'] = "No posts to view";
    }
}
?>
