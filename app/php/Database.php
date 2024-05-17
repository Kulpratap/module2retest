<?php
namespace app\php;
/**
 * Trait Database
 * 
 * This trait provides methods for establishing and closing database connections.
 */
trait Database {
  /** @var string The server name */
  private $servername;
  /** @var string The database username */
  private $username;
  /** @var string The database password */
  private $password;
  /** @var string The database name */
  private $dbname;
  /** @var \mysqli The database connection */
  private $conn;

  /**
   * Establishes a database connection.
   *
   * @param string $servername The server name
   * @param string $username The database username
   * @param string $password The database password
   * @param string $dbname The database name
   * 
   * @return void
   */
  public function connection($servername, $username, $password, $dbname) {
    $this->servername = $servername;
    $this->username = $username;
    $this->password = $password;
    $this->dbname = $dbname;
    
    // Create connection
    $this->conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
    
    // Check connection
    if ($this->conn->connect_error) {
      die("Connection failed: " . $this->conn->connect_error);
    }
  }

  /**
   * Closes the database connection.
   *
   * @return void
   */
  public function closeConnection() {
    $this->conn->close();
  }
}
