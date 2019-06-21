<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Expense Tracker</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript">
      let toggleCardBody = function(elem) {
        elem.nextElementSibling.classList.toggle('hidden');
      }

      let toggleLoginPopup = function() {
        document.querySelector("#login-popup").classList.toggle('hidden');
      }
    </script>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand mr-auto" href="#">E-Tracker</a>
      <?php
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
          echo '<form action="logout.php" method="post">'
              .'  <button class="btn btn-danger" id="log-out-btn">Log Out</button>'
              .'</form>';
        } else {
          echo '<button type="button" class="btn btn-success" id="log-in-btn" onclick="toggleLoginPopup()">Log In</button>';
        }
      ?>
    </nav>
    <div class="container bg-light" id="container">
      <div class="message text-center bg-dark">
        <?php
          if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false){
            echo '<h2><span class="badge badge-info">Welcome!</span></h2>';
          }
          if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            echo '<h2><span class="badge badge-success">Welcome ' . $_SESSION['user_name'] . "</span></h2>";
          }
          if(isset($_SESSION['log_in_failed']) && $_SESSION['log_in_failed'] === true) {
            echo '<h2><span class="badge badge-danger">Log In Failed</span></h2>';
            $_SESSION['log_in_failed'] = false;
          }
          if(isset($_SESSION['registration_failed']) && $_SESSION['registration_failed'] === true) {
            echo '<h2><span class="badge badge-danger">Email Already Exists!</span></h2>';
            $_SESSION['registration_failed'] = false;
          }
        ?>
      </div>
      <div id="login-popup" class="<?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) echo 'hidden';?>">
        <ul class="nav nav-tabs nav-fill" id="loginTab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="true">Log In</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="false">Register</a>
          </li>
        </ul>
        <div class="tab-content" id="loginTabContent">
          <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="home-tab">
            <div class="row justify-content-center">
              <div class="col-9">
                <form class="" action="login.php" method="post">
                  <div class="form-group">
                    <label for="inputEmail">Email address</label>
                    <input name="email" type="email" class="form-control" id="inputEmail" aria-describedby="emailHelp" placeholder="Enter email" required>
                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                  </div>
                  <div class="form-group">
                    <label for="inputPassword">Password</label>
                    <input name="password" type="password" class="form-control" id="inputPassword" placeholder="Password" required>
                  </div>
                  <button type="submit" class="btn btn-primary">Log In</button>
                </form>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row justify-content-center">
              <div class="col-9">
                <form class="" action="register.php" method="post">
                  <div class="form-group">
                    <label for="inputName">Name</label>
                    <input name="name" type="name" class="form-control" id="inputEmail" placeholder="Enter Name" required>
                  </div>
                  <div class="form-group">
                    <label for="inputEmail">Email address</label>
                    <input name="email" type="email" class="form-control" id="inputEmail" aria-describedby="emailHelp" placeholder="Enter email" required>
                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                  </div>
                  <div class="form-group">
                    <label for="inputPassword">Password</label>
                    <input name="password" type="password" class="form-control" id="inputPassword" placeholder="Password" required>
                  </div>
                  <button type="submit" class="btn btn-primary">Register</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row <?php if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) echo 'hidden';?>">
        <div class="col-lg-4 col-sm-12" id="left-column">
          <h3>Add Expense</h3>
          <form action="addexpense.php" method="post">
            <div class="form-group">
              <label for="inputExpenseTitle">Title</label>
              <input type="text" name="expense_title" maxlength="30" class="form-control" id="inputExpenseTitle" placeholder="Enter Title" required>
            </div>
            <div class="form-group">
              <label for="inputExpenseAmount">Amount</label>
              <input type="number" name="expense_amount" min="0" class="form-control" id="inputExpenseAmount" placeholder="Amount" required>
            </div>
            <div class="form-group">
              <label for="inputExpenseDescription">Description</label>
              <textarea name="expense_description" maxlength="255" rows="4" class="form-control" id="inputExpenseDescription" placeholder="Enter Description (optional)"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>
        </div>
        <div class="col-lg-7 col-sm-11" id="right-column">
          <h3>Expenses</h3>
          <div id="expenses-list">
            <?php
              require 'database.php';
              if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
                $expenses = getDBInstance()->getUserExpenses($_SESSION['user_id']);
                $total = 0;
                foreach($expenses as $expense) {
                  echo '<div class="card">'
                      .'    <div class="card-header" onclick="toggleCardBody(this)">'
                      .'      <h4 class="crd-title">' . $expense['title'] . '</h4>'
                      .'      <h4 class="crd-cost">Rs. ' . $expense['amount'] . '</h4>'
                      .'    </div>'
                      .'    <div class="card-body row hidden">'
                      .'      <p class="card-text col">' . $expense['description'] . '</p>'
                      .'      <div class="float-right">'
                      .'        <form action="removeexpense.php" method="post">'
                      .'          <button name="expense_id" value=' . $expense['expense_id'] . ' class="btn btn-danger">Remove</button>'
                      .'        </form>'
                      .'      </div>'
                      .'    </div>'
                      .'  </div>';
                  $total += $expense['amount'];
                }
                echo '<h4>Total: Rs.' . $total . '</h4>';
              }
            ?>
          </div>
        </div>
      </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>
