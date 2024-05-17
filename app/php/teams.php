<?php
// Redirect to the homepage if the user is not logged in
if ((!isset($_SESSION['loggedin'])) || ($_SESSION['loggedin'] == false)) {
  header('Location: /login');
  exit;
}
// using IPL databse class
use app\database\IplDatabase;

// creating instance of the IPL Database class
$ipl = new IplDatabase();

// creating databas connection 
$ipl->connection(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);


// retrieving the teams data;
$teams = $ipl->getTeamPlayersByUserName($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>IPL Dashboard</title>
  <link rel="stylesheet" href="../app/styles/teams.css">
</head>

<body>
  <div class="container">
    <nav>
      <a class="logo" href="/home"> Innoraft Premier League</a>
      <a href="/home">Home</a>

      <a href="/teams">Your Teams</a>
      <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) { ?>
        <a href="/logout">Logout</a>
      <?php } else { ?>
        <a href="/login">Login</a>
      <?php } ?>
    </nav>
    <h1>Teams and Players</h1>

    <?php
    foreach ($teams as $teamName => $players) {
      echo '<div class="team-container">';
      echo '<div class="team-name">' . htmlspecialchars($teamName) . '</div>';
      echo '<ul class="player-list">';
      foreach ($players as $player) {
        echo '<li class="player">';
        echo '<span class="player-name">' . htmlspecialchars($player['employee_name']) . '</span>';
        echo '<div class="player-details">';
        echo 'ID: ' . htmlspecialchars($player['employee_id']) . ', ';
        echo 'Type: ' . htmlspecialchars($player['type']) . ', ';
        echo 'Points: ' . htmlspecialchars($player['points']);
        echo '</div>';
        echo '</li>';
      }
      echo '</ul>'; ?>
      <form action="/IplDatabase" method="post">
        <input type="hidden" name="teamName" value="<?= htmlspecialchars($teamName) ?>">
        <input type="submit" name="delete" value="Delete Team">
      </form>
      <?php echo '</div>';
    }
    ?>
  </div>
</body>

</html>