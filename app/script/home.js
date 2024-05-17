// Reusable AJAX function for saving team
function saveTeamAjax(postData) {
  $.ajax({
    url: '../app/database/saveTeam.php',
    method: 'POST',
    data: postData,
    success: function(response) {
      alert(response); // Display success or error message
      // Optionally, redirect or perform other actions after saving team
    },
    error: function(xhr, status, error) {
      console.error(xhr.responseText); // Log any errors to console
      alert('Error occurred while saving team.');
    }
  });
}

$(document).ready(function() {
  var maxPlayers = 11;
  var maxPoints = 100;
  var maxBatsmen = 5;
  var maxAllrounders = 2;
  var maxBowlers = 4;

  var selectedPlayers = [];
  var teamName = '';

  // Function to update the UI based on selected players
  function updateUI() {
    var totalPointsUsed = selectedPlayers.reduce((acc, curr) => acc + curr.points, 0);
    $('#total-players').text(selectedPlayers.length);
    $('#total-points').text(totalPointsUsed);

    // Enable or disable save button based on conditions
    if (selectedPlayers.length === maxPlayers && totalPointsUsed <= maxPoints && teamName.trim() !== '') {
      $('#save-team').prop('disabled', false);
    } else {
      $('#save-team').prop('disabled', true);
    }

    // Update selected players list display
    $('.selected-players-list').empty();
    selectedPlayers.forEach(function(player) {
      var playerItem = '<li class="selected-player">';
      playerItem += '<span class="selected-player-name">' + player.name + '</span>';
      playerItem += ' (Type: ' + player.type + ')';
      playerItem += '<button class="remove-from-team" data-player-id="' + player.id + '">Remove</button>';
      playerItem += '</li>';
      $('.selected-players-list').append(playerItem);
    });
  }

  // Event delegation to handle click events for dynamically added elements
  $('.players-list').on('click', '.add-to-team', function() {
    var playerId = $(this).closest('.player-card').data('player-id');
    var playerName = $(this).closest('.player-card').find('.player-name').text().trim(); // Get player full name
    var playerPoints = parseInt($(this).closest('.player-card').data('points'));
    var playerType = $(this).closest('.player-card').data('type');

    // Check if player is already selected
    if (selectedPlayers.some(player => player.id === playerId)) {
      alert('This player is already selected.');
      return;
    }

    // Check if maximum players limit is reached
    if (selectedPlayers.length >= maxPlayers) {
      alert('You have already selected the maximum number of players.');
      return;
    }

    // Check if adding this player exceeds the maximum points allowed
    var totalPointsUsed = selectedPlayers.reduce((acc, curr) => acc + curr.points, 0);
    if (totalPointsUsed + playerPoints > maxPoints) {
      alert('Adding this player exceeds the maximum points allowed.');
      return;
    }

    // Check maximum counts based on player type
    var batsmenCount = selectedPlayers.filter(player => player.type === 'batsman').length;
    var allroundersCount = selectedPlayers.filter(player => player.type === 'allrounder').length;
    var bowlersCount = selectedPlayers.filter(player => player.type === 'bowler').length;

    if (playerType === 'batsman' && batsmenCount >= maxBatsmen) {
      alert('You have already selected the maximum number of batsmen.');
      return;
    }

    if (playerType === 'allrounder' && allroundersCount >= maxAllrounders) {
      alert('You have already selected the maximum number of allrounders.');
      return;
    }

    if (playerType === 'bowler' && bowlersCount >= maxBowlers) {
      alert('You have already selected the maximum number of bowlers.');
      return;
    }

    // Add player to the selectedPlayers array
    selectedPlayers.push({
      id: playerId,
      name: playerName,
      points: playerPoints,
      type: playerType
    });

    // Update UI
    updateUI();
  });

  // Remove player from selected team
  $('.selected-players-list').on('click', '.remove-from-team', function() {
    var playerId = $(this).data('player-id');
    selectedPlayers = selectedPlayers.filter(player => player.id !== playerId);
    updateUI();
  });

  // Handle input in team name field to enable/disable save button
  $('#team-name-input').on('input', function() {
    teamName = $(this).val().trim();
    updateUI(); // Update UI based on team name input
  });

  // Save team functionality (Form submission)
  $('#save-team').on('click', function() {
    // Validate if all players are selected and within points limit
    var totalPointsUsed = selectedPlayers.reduce((acc, curr) => acc + curr.points, 0);
    if (selectedPlayers.length !== maxPlayers || totalPointsUsed > maxPoints || teamName.trim() === '') {
      alert('Please select exactly 11 players, enter a team name, and ensure points are within the limit of 100.');
      return;
    }

    // Prepare data to send to server
    var postData = {
      players: selectedPlayers,
      teamName: teamName // Include team name in postData
    };

    // Call reusable AJAX function to save team
    saveTeamAjax(postData);
  });

  // Clear selections (for testing purposes)
  $('#clear-selection').on('click', function() {
    selectedPlayers = [];
    teamName = '';

    // Clear UI
    updateUI();
    $('#team-name-input').val(''); // Clear team name input
  });

});
