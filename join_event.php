<?php
require("connection.php");

if (isset($_GET['event_id']) && isset($_SESSION['user_id'])) {
    $eventId = intval($_GET['event_id']);
    $userId = intval($_SESSION['user_id']);

    // Check if the user already joined the event
    $checkQuery = "SELECT * FROM event_participants WHERE event_id = $eventId AND user_id = $userId";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult->num_rows == 0) {
        // Add the user to the event participants
        $joinQuery = "INSERT INTO event_participants (event_id, user_id) VALUES ($eventId, $userId)";
        if ($conn->query($joinQuery)) {
            echo json_encode(['success' => true, 'message' => 'Joined event']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to join event']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'You have already joined this event']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
