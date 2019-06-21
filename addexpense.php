<?php
  require "database.php";

  if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    getDBInstance()->addExpense($_SESSION['user_id'], $_POST['expense_amount'], $_POST['expense_title'], $_POST['expense_description']);
    $_SESSION['expense_added'] = true;
  }

  require 'gohome.php';
?>
