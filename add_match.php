<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $match_id = $_POST['match_id'];
    $event_id = $_POST['event_id'];
    $team1_players = json_decode($_POST['team1_players'], true);
    $team2_players = json_decode($_POST['team2_players'], true);

    function addPlayersToMatch($teamId, $players, $matchId, $eventId) {
        global $conn;
        foreach ($players as $player) {
            $playerId = $player['player_id'];
            $finished = $player['finished'] ? 1 : 0;

            $sql = "INSERT INTO match_players (match_id, event_id, team_id, player_id, finished) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssiii', $matchId, $eventId, $teamId, $playerId, $finished);

            if (!$stmt->execute()) {
                echo json_encode(['status' => 'error', 'message' => $stmt->error]);
                exit;
            }
        }
    }

    addPlayersToMatch(1, $team1_players, $match_id, $event_id);

    addPlayersToMatch(2, $team2_players, $match_id, $event_id);

    echo json_encode(['status' => 'success', 'message' => 'Mecz został dodany!']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj Mecz</title>
    <link rel="stylesheet" href="add_match.css">
    <script>
    function addPlayer(teamId) {
        const playerRow = document.createElement('tr');
        playerRow.classList.add('player-row');
        playerRow.innerHTML = `
            <td>
                <input type="text" class="player-search-input" placeholder="Wyszukaj użytkownika..." oninput="filterUsers(this, '${teamId}')">
                <div class="autocomplete-results"></div>
            </td>
            <td>
                <select name="${teamId}-user-select"></select>
            </td>
            <td>
                <label><input type="checkbox" name="${teamId}-finished"> Zakończył</label>
            </td>
            <td>
                <button onclick="removePlayer(this)">Usuń</button>
            </td>
        `;
        document.getElementById(teamId).querySelector('tbody').appendChild(playerRow);
        populateUserSelect(playerRow.querySelector('select'), []);
    }

    function filterUsers(input, teamId) {
        const query = input.value;
        const resultsDiv = input.nextElementSibling;

        if (query.length < 2) {
            return;
        }

        fetch(`search_users.php?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(users => {
                displayAutocompleteResults(resultsDiv, users, input);
            });
    }

    function displayAutocompleteResults(resultsDiv, users, input) {
        users.forEach(user => {
            const div = document.createElement('div');
            div.textContent = user.username;
            div.dataset.userId = user.user;
            div.onclick = function() {
                input.value = user.username;
                resultsDiv.innerHTML = '';
                const selectElement = input.closest('tr').querySelector('select');
                populateUserSelect(selectElement, users);
            };
            resultsDiv.appendChild(div);
        });
    }

    function populateUserSelect(selectElement, userList) {
        selectElement.innerHTML = '';
        userList.forEach(user => {
            const option = document.createElement('option');
            option.value = user.username;
            option.textContent = user.username;
            selectElement.appendChild(option);
        });
    }

    function removePlayer(button) {
        button.closest('tr').remove();
    }

    function addMatch() {
    const team1Players = Array.from(document.querySelectorAll('#team1-players .player-row')).map(row => {
        const playerSelect = row.querySelector('select');
        const finishedCheckbox = row.querySelector('input[name$="-finished"]');
        
        return {
            player_id: playerSelect ? playerSelect.value : null,
            finished: finishedCheckbox ? finishedCheckbox.checked : false
        };
    });
    const team2Players = Array.from(document.querySelectorAll('#team2-players .player-row')).map(row => {
        const playerSelect = row.querySelector('select');
        const finishedCheckbox = row.querySelector('input[name$="-finished"]');
        
        return {
            player_id: playerSelect ? playerSelect.value : null,
            finished: finishedCheckbox ? finishedCheckbox.checked : false
        };
    });

    const eventId = new URLSearchParams(window.location.search).get('event_id');

    fetch('add_match.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            match_id: matchId,
            event_id: eventId,
            team1_players: JSON.stringify(team1Players),
            team2_players: JSON.stringify(team2Players)
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') {
            window.location.href = 'index.php';
        }
    })
    .catch(error => {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas dodawania meczu.');
    });
}

    </script>
</head>
<body>

<div id="bodycontainer">
    <h2>Dodaj Mecz</h2>

    <!-- Team 1 Section -->
    <div class="team-section">
        <h3>Zespół 1</h3>
        <table id="team1-players">
            <thead>
                <tr>
                    <th>Użytkownik</th>
                    <th>Wybierz</th>
                    <th>Zakończył</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                <!-- Player rows will be added here -->
            </tbody>
        </table>
        <button class="add-player-btn" onclick="addPlayer('team1-players')">Dodaj Gracza</button>
    </div>

    <!-- Team 2 Section -->
    <div class="team-section">
        <h3>Zespół 2</h3>
        <table id="team2-players">
            <thead>
                <tr>
                    <th>Użytkownik</th>
                    <th>Wybierz</th>
                    <th>Zakończył</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                <!-- Player rows will be added here -->
            </tbody>
        </table>
        <button class="add-player-btn" onclick="addPlayer('team2-players')">Dodaj Gracza</button>
    </div>

    <button class="add-match-btn" onclick="addMatch()">Dodaj Mecz</button>
</div>

</body>
</html>

