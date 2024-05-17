<?php
use app\database\Checklogin;
use app\database\IplDatabase;

// Redirect to the homepage if the user is not logged in
if ((!isset($_SESSION['loggedin'])) || ($_SESSION['loggedin'] == false)) {
  header('Location: /login');
  exit;
}

// Retrieve the username from the session variable
$username = $_SESSION['username'];

// Create a new instance of the Checklogin class for login verification
$login = new Checklogin();

// Establish a database connection
$login->connection(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);

// Retrieve the user role based on the username from the database
$role = $login->getRoleByUsername($username);

// Get players data from database
$db = new IplDatabase();
$db->connection(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);
$players = $db->getPlayers();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>IPL Dashboard</title>
  <link rel="stylesheet" href="../app/styles/home.css">
</head>

<body>
  <div class="container">
    <nav>
      <a class="logo" href="/home"> Innoraft Premier League</a>
      <a href="/home">Home</a>
      <?php if ($role == 'admin') { ?>
        <a href="/createPlayer">Create +</a>
      <?php } ?>
      <a href="/teams">Your Teams</a>
      <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) { ?>
        <a href="/logout">Logout</a>
      <?php } else { ?>
        <a href="/login">Login</a>
      <?php } ?>
    </nav>
    <div class="players-container">
      <h2>Players List</h2>
      <div class="players-list">
        <?php foreach ($players as $player) { ?>
          <div class="player-card" data-player-id="<?php echo $player['id']; ?>"
            data-points="<?php echo $player['points']; ?>" data-type="<?php echo $player['type']; ?>">
            <h3 class="player-name"><?php echo $player['employee_name']; ?></h3>
            <p>Type: <?php echo ucfirst($player['type']); ?></p>
            <p>Points: <?php echo $player['points']; ?></p>
            <button class="add-to-team">Add to team</button>
          </div>
        <?php } ?>
      </div>
      <div class="team-summary">
        <h2>Team Summary</h2>
        <label for="team-name-input">Team Name:</label>
        <input type="text" id="team-name-input" placeholder="Enter team name" required>
        <p>Total Players: <span id="total-players">0</span></p>
        <p>Total Points Used: <span id="total-points">0</span></p>
        <button id="save-team" disabled>Save Team</button>
        <ul class="selected-players-list">
        </ul>
      </div>
    </div>
  </div>
</body>

</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../app/script/home.js"></script>