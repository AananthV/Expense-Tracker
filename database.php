<?php
  /**
   *
   */
  class Database {
    protected $db;

    public function __construct(PDO $db) {
      $this->db = $db;
      $this->setUpDatabase();
    }

    public function setUpDatabase() {
      // Create Users Table
      $this->db->query('CREATE TABLE IF NOT EXISTS users('
        .'id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,'
        .'email VARCHAR(255) NOT NULL UNIQUE,'
        .'name VARCHAR(30) NOT NULL,'
        .'password VARCHAR(255) NOT NULL'
        .')'
      );

      // Create Expenses table
      $this->db->query('CREATE TABLE IF NOT EXISTS expenses('
        .'expense_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,'
        .'title VARCHAR(80) NOT NULL,'
        .'description VARCHAR(255),'
        .'amount REAL NOT NULL,'
        .'user_id INT)'
      );
    }

    public function getAllRows() {
      return $this->db->query('SELECT * from users');
    }

    private function checkEmail($email) {
      // Check if email exists
      $emailQuery = $this->db->prepare('SELECT email FROM users WHERE email = :email');
      $emailQuery->bindParam(':email', $email, PDO::PARAM_STR);
      $emailQuery->execute();
      if($emailQuery->fetch()) {
        return true;
      }
      return false;
    }

    private function checkUserID($id) {
      $checkUserQuery = $this->db->prepare('SELECT id FROM users WHERE id = :id');
      $checkUserQuery->bindParam(':id', $id, PDO::PARAM_INT);
      $checkUserQuery->execute();
      if($checkUserQuery->fetch()) {
        return true;
      } else {
        return false;
      }
    }

    private function checkPassword($email, $password) {
      // Check if email exists
      if(!$this->checkEmail($email)) return false;

      // Retrieve hash from DB
      $passwordQuery = $this->db->prepare('SELECT password FROM users WHERE email = :email');
      $passwordQuery->bindParam(':email', $email, PDO::PARAM_STR);
      $passwordQuery->execute();

      // Check password
      return password_verify($password, $passwordQuery->fetch(PDO::FETCH_ASSOC)['password']);
    }

    public function addUser($name, $email, $password) {
      // Check if email exists
      if($this->checkEmail($email)) return false;

      // Hash The Password
      $hash = password_hash($password, PASSWORD_DEFAULT);

      // Add to database
      $addUserQuery = $this->db->prepare('INSERT INTO users(email, name, password) VALUES (:email, :name, :password)');
      $addUserQuery->bindParam(':email', $email, PDO::PARAM_STR);
      $addUserQuery->bindParam(':name', $name, PDO::PARAM_STR);
      $addUserQuery->bindParam(':password', $hash, PDO::PARAM_STR);
      $addUserQuery->execute();
    }

    public function deleteUser($email, $password) {
      // Check if password matches
      if($this->checkPassword($email, $password)) {
        // Delete Row from users table
        $deleteRowQuery = $this->db->prepare('DELETE FROM users WHERE email = :email');
        $deleteRowQuery->bindParam(':email', $email, PDO::PARAM_STR);
        return $deleteRowQuery->execute();
      }

      return false;
    }

    public function getUser($email, $password) {
      if($this->checkPassword($email, $password)) {
        $fetchUserQuery = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $fetchUserQuery->bindParam(':email', $email, PDO::PARAM_STR);
        $fetchUserQuery->execute();
        return $fetchUserQuery->fetch(PDO::FETCH_ASSOC);
      }
      return false;
    }

    public function addExpense($user_id, $expense, $expense_title, $expense_description){
      if($this->checkUserID($user_id)) {
        $addExpenseQuery = $this->db->prepare('INSERT INTO expenses(title, description, amount, user_id) VALUES (:title, :description, :amount, :user_id)');
        $addExpenseQuery->bindParam(':title', $expense_title, PDO::PARAM_STR);
        $addExpenseQuery->bindParam(':description', $expense_description, PDO::PARAM_STR);
        $addExpenseQuery->bindParam(':amount', $expense, PDO::PARAM_STR);
        $addExpenseQuery->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $addExpenseQuery->execute();
      }
      return false;




    }

    public function removeExpense($user_id, $expense_id) {
      $deleteExpenseQuery = $this->db->prepare('DELETE FROM expenses WHERE expense_id = :expense_id AND user_id = :user_id');
      $deleteExpenseQuery->bindParam(':expense_id', $expense_id, PDO::PARAM_INT);
      $deleteExpenseQuery->bindParam(':user_id', $user_id, PDO::PARAM_INT);
      return $deleteExpenseQuery->execute();
    }

    public function getUserExpenses($user_id) {
      if($this->checkUserID($user_id)) {
        $getExpensesQuery = $this->db->prepare('SELECT * FROM expenses WHERE user_id = :user_id');
        $getExpensesQuery->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $getExpensesQuery->execute();
        return $getExpensesQuery->fetchAll(PDO::FETCH_ASSOC);
      }
      return false;
    }
  }

  function getDBInstance() {
    $host = '127.0.0.1';
    $db   = 'test';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
         $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
         throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }

    $db = new Database($pdo);

    return $db;
  }
?>
