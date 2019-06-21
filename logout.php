<?php
  if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    session_destroy();
  }

  require 'gohome.php'
?>
