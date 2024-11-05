<?php
include 'navbar.php';
include 'connection.php';

if (!isset($_GET['event_id'])) {
    header('Location: index.php');
    exit();
}

$event_id = intval($_GET['event_id']);
$userId = $_SESSION['user_id'] ?? 0;

$checkJoinQuery = "SELECT 1 FROM event_participants WHERE event_id = ? AND user_id = ? AND status = 'confirmed'";
$stmt = $conn->prepare($checkJoinQuery);
$stmt->bind_param("ii", $event_id, $userId);
$stmt->execute();
$joined = $stmt->get_result()->num_rows > 0;
$stmt->close();

$query = "SELECT e.*, u.username AS creator_name, u.avatar AS creator_avatar
          FROM events e
          JOIN users u ON e.creator_id = u.user_id
          WHERE e.event_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die('Prepare failed: ' . $conn->error . ' | Query: ' . $query);
}
$stmt->bind_param('i', $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

$query = "SELECT c.*, u.username, u.avatar
          FROM event_comments c
          JOIN users u ON c.user_id = u.user_id
          WHERE c.event_id = ?
          ORDER BY c.created_at DESC";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die('Prepare failed: ' . $conn->error . ' | Query: ' . $query);
}
$stmt->bind_param('i', $event_id);
$stmt->execute();
$comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        $user_id = $_SESSION['user_id']; 
        $query = "INSERT INTO event_comments (event_id, user_id, comment) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die('Prepare failed: ' . $conn->error . ' | Query: ' . $query);
        }
        $stmt->bind_param('iis', $event_id, $user_id, $comment);
        $stmt->execute();
        header("Location: eventpage.php?event_id=$event_id"); 
        exit();
    }
}

if (isset($_GET['delete_comment_id']) && isset($_SESSION['user_id'])) {
    $delete_comment_id = intval($_GET['delete_comment_id']);
    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['user_role']; 
    $query = "SELECT user_id FROM event_comments WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error . ' | Query: ' . $query);
    }
    $stmt->bind_param('i', $delete_comment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $comment_owner = $result->fetch_assoc()['user_id'];

        if ($user_role == 'admin' || $user_id == $comment_owner) {
            $query = "DELETE FROM event_comments WHERE id = ?";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                die('Prepare failed: ' . $conn->error . ' | Query: ' . $query);
            }
            $stmt->bind_param('i', $delete_comment_id);
            $stmt->execute();
            header("Location: eventpage.php?event_id=$event_id");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona Wydarzenia</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="eventpage.css">
    <script>
        function toggleJoinEvent(eventId) {
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
                    alert(data.message || "Failed to join/unjoin the event.");
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function showParticipants(eventId) {
            fetch('get_participants.php?event_id=' + eventId, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const popup = document.getElementById('participants-popup');
                    const list = document.getElementById('participants-list');
                    list.innerHTML = ''; 
                    data.participants.forEach(participant => {
                        const item = document.createElement('li');
                        item.classList.add('participant-list-item');
                        item.innerHTML = `\
                            <img src="avatars/${participant.avatar}" alt="${participant.username} avatar" class="participant-avatar">\
                            <span>${participant.username}</span>\
                        `;
                        list.appendChild(item);
                    });
                    popup.style.display = 'flex'; 
                } else {
                    alert(data.message || "Failed to fetch participants.");
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</head>
    <div id="bodycontainer">
        <h1><?php echo htmlspecialchars($event['event_name']); ?></h1>
        <div class="event-details">
            <img src="<?php echo htmlspecialchars($event['banner_url']); ?>" alt="Baner wydarzenia" class="event-banner">
            <p><?php echo nl2br(htmlspecialchars($event['event_description'])); ?></p>
            <p>Typ napoju: <?php echo htmlspecialchars($event['beverage']); ?></p>
            <p>Typ wydarzenia: <?php echo htmlspecialchars($event['event_type']); ?></p>
            <div class="event-meta">
                <a href="profile.php?username=<?php echo urlencode($event['creator_name']); ?>">
                    <img src="avatars/<?php echo htmlspecialchars($event['creator_avatar']); ?>" alt="<?php echo htmlspecialchars($event['creator_name']); ?>" class="creator-avatar">
                    <span>Stworzone przez <?php echo htmlspecialchars($event['creator_name']); ?></span>
                </a>
            </div>
            <div class="event-actions">
                <button class="icon-btn join-btn <?php echo $joined ? 'joined' : 'not-joined'; ?>" onclick="toggleJoinEvent(<?php echo $event['event_id']; ?>)">
                    <svg class="icon" viewBox="0 0 489.224 489.224" aria-hidden="true">
                        <path d="M448.575,0H186.226c-21.673,0-39.298,17.622-39.298,39.297v106.248H40.65c-21.677,0-39.293,17.623-39.293,39.297v262.381c0,21.67,17.616,39.293,39.293,39.293h197.353c-0.239-1.199-0.548-2.351-0.694-3.582l-5.382-46.219H51.158V195.348h95.771v106.293c0,21.678,17.625,39.295,39.298,39.295h34.564l-5.804-49.796h-18.257v-95.792h95.796V225.4l49.801,24.417v-64.976c0-21.674-17.622-39.297-39.299-39.297H196.729V49.801h241.339V291.14H426.59l48.424,23.747c2.92,1.443,5.561,3.243,8.042,5.221c2.965-5.529,4.81-11.752,4.81-18.467V39.297C487.867,17.622,470.249,0,448.575,0z"/>
                        <path d="M460.361,344.666l-199.334-97.735c-1.607-0.778-3.326-1.168-5.042-1.168c-2.334,0-4.598,0.661-6.558,1.876l-33.875,22.121c-0.473,0.324-0.872,0.758-1.153,1.261L124.91,380.897c-11.903,10.774-15.016,30.026-8.96,44.535c3.299,7.454,8.827,13.917,15.917,18.291c8.56,5.641,18.697,7.066,27.697,4.426c6.032-1.582,11.431-4.511,16.24-8.379l77.878-51.229l60.211,29.859c7.012,3.485,14.812,5.573,22.877,5.573c6.285,0,12.585-1.609,18.277-4.639c2.642-1.181,5.041-2.845,7.264-4.833c4.76-3.716,8.773-8.244,11.745-13.257c1.992-2.993,2.974-6.48,2.874-9.998l3.767-50.059c0.112-1.501,0.118-3.004,0.026-4.527c-0.628-8.488-5.803-15.567-13.603-18.983L460.361,344.666z"/>
                    </svg>
                </button>
                <button class="icon-btn show-participants" onclick="showParticipants(<?php echo $event['event_id']; ?>)">
                    <svg class="icon" viewBox="0 0 1024 1024" aria-hidden="true">
                        <path d="M128 128h768v768H128z"/>
                    </svg>
                </button>
            </div>
            <div id="participants-popup" class="popup">
                <div class="popup-content">
                    <span class="popup-close" onclick="document.getElementById('participants-popup').style.display='none'">&times;</span>
                    <ul id="participants-list" class="participants-list"></ul>
                </div>
            </div>
        </div>
        <div class="comments-section">
            <h2>Komentarze</h2>
            <?php if (isset($_SESSION['user_id'])): ?>
            <form action="eventpage.php?event_id=<?php echo $event_id; ?>" method="POST" class="comment-form">
                <textarea name="comment" rows="4" placeholder="Dodaj komentarz..." required></textarea>
                <button type="submit">Wyślij</button>
            </form>
            <?php else: ?>
                        <p>Musisz być zalogowany, aby dodać komentarz.</p>
                    <?php endif; ?>
            <div class="comments-list">
                
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <img src="avatars/<?php echo htmlspecialchars($comment['avatar']); ?>" alt="<?php echo htmlspecialchars($comment['username']); ?> avatar" class="comment-avatar">
                        <div class="comment-body">
                            <p class="comment-author"><?php echo htmlspecialchars($comment['username']); ?></p>
                            <p class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                            <p class="comment-date"><?php echo htmlspecialchars($comment['created_at']); ?></p>
                            <?php if (isset($_SESSION['user_id'])&&($_SESSION['user_id'] == $comment['user_id'] || $_SESSION['isAdmin'])): ?>
                                <a href="eventpage.php?event_id=<?php echo $event_id; ?>&delete_comment_id=<?php echo $comment['id']; ?>" class="delete-comment-btn">Usuń</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($comments)): ?>
                    <p>Brak komentarzy.</p>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
    <?php require 'footer.php'?>
</body>
</html>
