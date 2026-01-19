<?php
$con = mysqli_connect("localhost", "root", "", "blog");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
