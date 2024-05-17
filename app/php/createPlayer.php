<?php

use app\database\Checklogin;

// creating an instance of Chekclogin class;
$login = new Checklogin();

// Establish a database connection
$login->connection(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);
$username=$_SESSION['username'];
// Retrieve the user role based on the username from the database
$role = $login->getRoleByUsername($username);

if($role!='admin'){
  header("Location:/home");
}
// Redirect to the homepage if the user is not logged in
if ((!isset($_SESSION['loggedin'])) || ($_SESSION['loggedin'] == false)) {
  header('Location: /login');
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Players</title>
    <link rel="stylesheet" href="../app/styles/createPlayer.css">
</head>
<body>
<div class="container">
    <nav>
      <a class="logo" href="/home"> Innoraft Premier League </a>
      <a href="/home">Home</a>
      
      <a href="/teams">Your Teams</a>
      <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) { ?>
        <a href="/logout">Logout</a>
      <?php } else { ?>
        <a href="/login">Login</a>
      <?php } ?>
    </nav>
    <h1>Add Players</h1>

    <div class="form-container">
    
    <form action="/IplDatabase" method="post">
        <label for="employee_id">Employee ID:</label>
        <input type="text" id="employee_id" name="employee_id" placeholder="Enter employee id" required><br>

        <label for="employee_name">Employee Name:</label>
        <input type="text" id="employee_name" name="employee_name" placeholder="Enter employee name" required><br>

        <label for="type">Type:</label>
        <select id="type" name="type" required>
            <option value="batsman">Batsman</option>
            <option value="bowler">Bowler</option>
            <option value="allrounder">All Rounder</option>
        </select><br>

        <label for="points">Points:</label>
        <input type="number" id="points" name="points" min="2" max="10" required><br>
        <input type="submit" name="submit" value="Add Player"></input>
    </form>
    </div>
    </div>
</body>
</html>