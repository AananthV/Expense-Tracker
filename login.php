<?php
  require 'user.php';
  require 'database.php';

  $email = $_POST['email'];
  $password = $_POST['password'];

  $userdata = getDBInstance()->getUser($email, $password);

  if(isset($userdata['id'])) {
    $_SESSION['user_id'] = $userdata['id'];
    $_SESSION['user_email'] = $userdata['email'];
    $_SESSION['user_name'] = $userdata['name'];
    $_SESSION['logged_in'] = true;
    $_SESSION['log_in_failed'] = false;
  } else {
    $_SESSION['log_in_failed'] = true;
  }
  require 'gohome.php';
?>
