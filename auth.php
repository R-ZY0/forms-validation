<?php 

require('config.php');
if (!isset($_SESSION['login']) && isset($_COOKIE['cookie_token'])){
    $cookies =  $_COOKIE['remember_token'];

    $query =  mysqli_prepare($con,"SELECT  *  FROM  users  WHERE cookie =?");

    mysqli_stmt_bind_param($query,'s',$cookies);
    mysqli_stmt_execute($query);
   $data= mysqli_stmt_get_result($query);
   if (mysqli_num_rows($data)>0) {
   $user= mysqli_fetch_assoc($data);
    $_SESSION['login']='true';
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username']=$user['username'];
   }

}