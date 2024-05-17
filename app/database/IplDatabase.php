<?php

/**
 * @file
 * Contains \app\database\IplDatabase.
 */

namespace app\database;

use app\php\Database;

/**
 * Provides methods to interact with IPL players and teams in the database.
 */
class IplDatabase {
  use Database;

  /**
   * Inserts a new player into the database.
   *
   * @param string $employee_id
   *   The employee ID of the player.
   * @param string $employee_name
   *   The name of the player.
   * @param string $type
   *   The type of player (e.g., batsman, bowler).
   * @param int $points
   *   The points associated with the player.
   *
   * @return bool|string
   *   TRUE on successful insertion, error message on failure.
   */
  public function insertPlayer($employee_id, $employee_name, $type, $points) {
    try {
      $sql = "INSERT INTO players (employee_id, employee_name, type, points) VALUES (?, ?, ?, ?)";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("sssi", $employee_id, $employee_name, $type, $points);

      if ($stmt->execute()) {
        return true; // Successful insertion
      } else {
        return "Error: " . $stmt->error;
      }
    } catch (\mysqli_sql_exception $e) {
      if ($e->getCode() == 1062) { // MySQL error code for duplicate entry
        echo "<script>alert('Error: This employee ID is already registered.'); window.history.back();</script>";
        exit();
      } else {
        return "Error: " . $e->getMessage(); // Other database-related errors
      }
    }
  }

  /**
   * Retrieves all players from the database.
   *
   * @return array
   *   An array containing all player records as associative arrays.
   */
  public function getPlayers() {
    $sql = "SELECT * FROM players";
    $result = $this->conn->query($sql);

    if ($result->num_rows > 0) {
      return $result->fetch_all(MYSQLI_ASSOC);
    } else {
      return [];
    }
  }

  /**
   * Retrieves teams and their players for a given username.
   *
   * @param string $username
   *   The username to fetch teams for.
   *
   * @return array
   *   An associative array where keys are team names and values are arrays of players.
   */
  public function getTeamPlayersByUserName($username) {
    // Prepare statement to fetch distinct team names
    $stmt = $this->conn->prepare("SELECT DISTINCT team_name FROM team WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $teams = [];
    while ($row = $result->fetch_assoc()) {
      $teamName = $row['team_name'];

      // Prepare statement to fetch players for each team
      $stmtPlayers = $this->conn->prepare("SELECT t.team_name, p.* FROM team t JOIN players p ON t.player_id = p.id WHERE t.username = ? AND t.team_name = ?");
      $stmtPlayers->bind_param("ss", $username, $teamName);
      $stmtPlayers->execute();
      $resultPlayers = $stmtPlayers->get_result();

      $players = [];
      while ($player = $resultPlayers->fetch_assoc()) {
        $players[] = $player;
      }
      $stmtPlayers->close();

      $teams[$teamName] = $players;
    }
    $stmt->close();

    return $teams;
  }

  /**
     * Deletes a team from the database based on the team name.
     *
     * @param string $teamName
     *   The name of the team to delete.
     *
     * @return bool|string
     *   TRUE if the team was deleted successfully, or an error message string if deletion failed.
     */
    public function deleteTeam($teamName) {
        try {
            // Prepare the delete statement
            $stmt = $this->conn->prepare("DELETE FROM team WHERE team_name = ?");
            
            // Bind the parameter
            $stmt->bind_param("s", $teamName);
            
            // Execute the statement
            if ($stmt->execute()) {
                return true; // Successful deletion
            } else {
                return "Error: " . $stmt->error; // Return error if execution fails
            }
        } catch (\mysqli_sql_exception $e) {
            return "Error: " . $e->getMessage(); // Return any exceptions or errors
        } finally {
            $stmt->close(); // Always close the statement
        }
    }
}

$ipl = new IplDatabase();

// Process form submission to insert new player
if (isset($_POST['submit'])) {
  $employee_id = $_POST['employee_id'];
  $employee_name = $_POST['employee_name'];
  $type = $_POST['type'];
  $points = $_POST['points'];

  // Validate inputs
  if (!preg_match('/^\d+$/', $employee_id)) {
    echo "<script>alert('Invalid employee ID. It should be a number.'); window.history.back();</script>";
    exit();
  }

  if (!preg_match('/^[a-zA-Z\s]+$/', $employee_name)) {
    echo "<script>alert('Invalid name only contains alphabets.'); window.history.back();</script>";
    exit();
  }

  if (!preg_match('/^\d+$/', $points)) {
    echo "<script>alert('Invalid points only contains number.'); window.history.back();</script>";
    exit();
  }

  // Perform database insertion
  $ipl->connection(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);
  $result = $ipl->insertPlayer($employee_id, $employee_name, $type, $points);
  if ($result === true) {
    header("Location:/home");
    exit();
  }
}else if(isset($_POST['delete'])){
  $teamName=$_POST['teamName'];
  $ipl->connection(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);
  $result=$ipl->deleteTeam($teamName);
  if ($result === true) {
    header("Location:/teams");
    exit();
  }
}
