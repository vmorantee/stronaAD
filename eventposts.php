<?php
require("navbar.php");
require("connection.php");

$eventsQuery = "SELECT * FROM events";
$eventsResult = $conn->query($eventsQuery);

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wydarzenia</title>
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="events.css">

    <script>
        function toggleJoinEvent(eventId, event) {
            event.preventDefault();

            fetch('toggle_join.php?event_id=' + eventId, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || "Nie udało się dołączyć/opuścić wydarzenia.");
                    }
                })
                .catch(error => {
                    console.error('Błąd:', error);
                });
        }

        function showParticipants(eventId) {
            fetch('get_participants.php?event_id=' + eventId, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
                .then(response => response.text())
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            const popup = document.getElementById('participants-popup');
                            const list = document.getElementById('participants-list');
                            list.innerHTML = '';
                            data.participants.forEach(participant => {
                                const item = document.createElement('li');
                                item.classList.add('participant-list-item');
                                item.innerHTML = ` 
                                <img src="avatars/${participant.avatar}" alt="${participant.username}'s avatar" class="participant-avatar">
                                <span>${participant.username}</span>
                            `;
                                list.appendChild(item);
                            });
                            popup.style.display = 'block';
                        } else {
                            alert(data.message || "Nie udało się pobrać uczestników.");
                        }
                    } catch (error) {
                        console.error('Błąd parsowania JSON:', error);
                        alert("Nie udało się załadować uczestników.");
                    }
                })
                .catch(error => {
                    console.error('Błąd:', error);
                });
        }

        function closePopup() {
            document.getElementById('participants-popup').style.display = 'none';
        }
    </script>
</head>

<body>
    <div id="bodycontainer">
        <div class="page-header">
            <h1>Dostępne Wydarzenia</h1>
            <a href="createevent.php" class="create-event-btn">Utwórz Wydarzenie</a>
        </div>

        <div id="event-container">
            <?php while ($event = $eventsResult->fetch_assoc()): ?>
                <?php
                $joined = false;
                if ($userId) {
                    $checkJoinQuery = "SELECT * FROM event_participants WHERE event_id = ? AND user_id = ? AND status = 'confirmed'";
                    $stmt = $conn->prepare($checkJoinQuery);
                    $stmt->bind_param("ii", $event['event_id'], $userId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $joined = $result->num_rows > 0;
                    $stmt->close();
                }
                ?>

                <div class="event-card">
                    <div class="event-banner">
                        <img src="<?php echo htmlspecialchars($event['banner_url']); ?>" alt="Baner Wydarzenia"
                            class="banner-img">
                    </div>
                    <div class="event-details">
                        <a href="eventpage.php?event_id=<?php echo $event['event_id']; ?>" class="event-card-link">
                            <h2 class="title"><?php echo htmlspecialchars($event['event_name']); ?></h2>
                            <p class="desc"><?php echo htmlspecialchars($event['event_description']); ?></p>
                            </a>
                            <p><strong>Napoje:</strong> <?php echo htmlspecialchars($event['beverage']); ?></p>
                    </div>
                    <div class="event-actions">
                        <button class="icon-btn join-btn" data-event-id="<?php echo $event['event_id']; ?>"
                            onclick="toggleJoinEvent(<?php echo $event['event_id']; ?>, event)">
                            <svg class="icon" viewBox="0 0 489.224 489.224" aria-hidden="true">
                                <path
                                    d="M448.575,0H186.226c-21.673,0-39.298,17.622-39.298,39.297v106.248H40.65c-21.677,0-39.293,17.623-39.293,39.297v262.381   c0,21.67,17.616,39.293,39.293,39.293h197.353c-0.239-1.199-0.548-2.351-0.694-3.582l-5.382-46.219H51.158V195.348h95.771v106.293   c0,21.678,17.625,39.295,39.298,39.295h34.564l-5.804-49.796h-18.257v-95.792h95.796V225.4l49.801,24.417v-64.976   c0-21.674-17.622-39.297-39.299-39.297H196.729V49.801h241.339V291.14H426.59l48.424,23.747c2.92,1.443,5.561,3.243,8.042,5.221   c2.965-5.529,4.81-11.752,4.81-18.467V39.297C487.867,17.622,470.249,0,448.575,0z" />
                                <path
                                    d="M460.361,344.666l-199.334-97.735c-1.607-0.778-3.326-1.168-5.042-1.168c-2.334,0-4.65,0.698-6.613,2.091   c-3.423,2.413-5.235,6.518-4.754,10.684l25.664,220.556c0.538,4.587,3.78,8.41,8.221,9.677c1.034,0.308,2.107,0.454,3.16,0.454   c3.421,0,6.744-1.558,8.932-4.311l67.064-83.959l101.404-35.194c4.374-1.509,7.391-5.498,7.686-10.117   C467.022,351.038,464.51,346.709,460.361,344.666z" />
                            </svg>
                            <?php echo $joined ? 'Dołączono' : 'Dołącz do Wydarzenia'; ?>
                        </button>
                        <button class="icon-btn participants-btn" data-event-id="<?php echo $event['event_id']; ?>"
                            onclick="showParticipants(<?php echo $event['event_id']; ?>)">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M12 12c2.67 0 4-2.5 4-4s-1.33-4-4-4-4 2.5-4 4 1.33 4 4 4zm-1 2H7.5c-1.11 0-2 .9-2 2v.5c0 1.1.89 2 2 2h9c1.11 0 2-.9 2-2v-.5c0-1.1-.89-2-2-2H11zm6-6h-2V4h-2v2H8V4H6v2H4v2h2v2H4v2h2v2H4v2h2v2h2v-2h8v2h2v-2h-2v-2h2v-2h-2V8zm0 4h-2v-2h2v2z"
                                    fill="currentColor"></path>
                            </svg>
                            Pokaż Uczestników
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <div id="participants-popup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <h2>Uczestnicy</h2>
            <ul id="participants-list"></ul>
        </div>
    </div>

    <?php include("footer.php"); ?>
</body>

</html>