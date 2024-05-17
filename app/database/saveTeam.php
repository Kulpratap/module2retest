<?php

/**
 * @file
 * Contains SaveTeam class for managing team data insertion into the database using AJAX.
 */

require 'dbconn.php';
session_start();

/**
 * Class SaveTeam
 *
 * Manages the insertion of players into a team in the database.
 */
class SaveTeam {
    private $servername = SERVER_NAME;
    private $username = USER_NAME;
    private $password = PASSWORD;
    private $dbname = DB_NAME;
    private $conn;

    /**
     * SaveTeam constructor.
     *
     * Establishes a database connection upon object creation.
     */
    public function __construct() {
        // Create connection
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    /**
     * Saves players to a team in the database.
     *
     * @param array $players
     *   An array containing player data (each player should have an 'id' key).
     * @param string $teamName
     *   The name of the team to which players are being added.
     * @param string $username
     *   The username associated with the team.
     */
    public function savePlayersToTeam($players, $teamName, $username) {
        // Prepare the insert statement for team table
        $teamStmt = $this->conn->prepare("INSERT INTO team (player_id, username, team_name) VALUES (?, ?, ?)");

        if (!$teamStmt) {
            die("Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error);
        }

        // Bind parameters and execute for each player ID
        foreach ($players as $player) {
            $playerId = $player['id'];
            $teamStmt->bind_param("iss", $playerId, $username, $teamName);
            if (!$teamStmt->execute()) {
                die("Execute failed: (" . $teamStmt->errno . ") " . $teamStmt->error);
            }
        }

        // Close team statement
        $teamStmt->close();
    }

    /**
     * Closes the database connection.
     */
    public function closeConnection() {
        // Close connection
        $this->conn->close();
    }
}

// Check if the players data and team name are sent via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['players']) && isset($_POST['teamName'])) {
    // Initialize SaveTeam object
    $saveTeam = new SaveTeam();

    // Get username from session
    $username = $_SESSION['username'];

    // Save players to team
    $saveTeam->savePlayersToTeam($_POST['players'], $_POST['teamName'], $username);

    // Close connection
    $saveTeam->closeConnection();

    echo "Players added to team successfully";
} else {
    echo "Error: Method not allowed or missing data";
}
