<?php
  require 'database.php';

  if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    if(isset($_POST['expense_id'])) {
      getDBInstance()->removeExpense($_SESSION['user_id'], $_POST['expense_id']);
    }
  }

  require 'gohome.php';
?>
