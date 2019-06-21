<?php
  require 'database.php';

  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  /* Insert Code to Validate email and password */
  if(getDBInstance()->addUser($name, $email, $password) !== false) {
    $userdata = getDBInstance()->getUser($email, $password);

    $_SESSION['user_id'] = $userdata['id'];
    $_SESSION['user_email'] = $userdata['email'];
    $_SESSION['user_name'] = $userdata['name'];
    $_SESSION['logged_in'] = true;
    $_SESSION['registration_failed'] = false;
  } else {
    $_SESSION['registration_failed'] = true;
  }

  require 'gohome.php';
?>
