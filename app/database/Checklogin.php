<?php
/**
 * @file
 * Contains Checklogin class.
 */

namespace app\database;

use app\php\Database;


// Define the Checklogin class
class Checklogin {
  // Use the Database trait
  use Database;
  
  /**
   * Checks user credentials against the database.
   *
   * @param string $username
   *   The username provided by the user.
   * @param string $password
   *   The password provided by the user.
   * @param string $role
   *   The role of the user.
   *
   * @return void
   *   Redirects the user to the appropriate page upon successful login.
   */
  public function checkUserCredentails($username, $password) {
    // Begin a database transaction
    $this->conn->begin_transaction();
    
    // SQL query to retrieve the username and hashed password
    $sql = "SELECT email, hashed_password FROM user WHERE email = ?";
    
    try {
      // Prepare and execute the SQL query
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $stmt->store_result();
      
      // Check if a row was found with the given username
      if ($stmt->num_rows > 0) {
        $hashed_password = NULL;
        
        // Retrieve the hashed password
        $stmt->bind_result($username, $hashed_password);
        $stmt->fetch();
        
        // Verify the password
        if (password_verify($password, $hashed_password)) {
          // Password is correct, set session variables and redirect
          $_SESSION['loggedin'] = true;
          $_SESSION['username'] = $username;
          $role=$this->getRoleByUsername($username);
          if($role=='admin'){
            header("Location:/createPlayer");
          }else{
          header("Location: /home");
          }
        } else {
          // Password is incorrect, display error message and redirect to login page
          echo "<script>alert('Incorrect Password or username');  window.location.href ='/login';</script>";
        }
      } else {
        // Username not found, display error message and redirect to login page
        echo "<script>alert('Incorrect Password or username');  window.location.href ='/login';</script>";
      }
    } catch (\mysqli_sql_exception $e) {
      // Rollback transaction and return error message
      $this->conn->rollback();
    }
  }

   /**
   * Retrieves the role of the user from the database.
   *
   * @param string $username
   *   The username provided by the user.
   * 
   * @return mixed
   *   The role of the user from the database.
   */
  public function getRoleByUsername($username) {
    // Prepare SQL statement
    $stmt = $this->conn->prepare("SELECT role FROM user WHERE email = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['role'];
  }
}

// Create an instance of the Checklogin class
$login = new Checklogin();

// Check if the login form is submitted
if (isset($_POST['submit'])) {
  // Connect to the database
  $login->connection(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);
  
  // Get form data
  $username = $_POST["username"];
  $password = $_POST["password"];
  
  // Check user credentials
  $login->checkUserCredentails($username, $password);

  // Close the database connection
  $login->closeConnection();
}
