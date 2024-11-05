<?php
session_start();
require("connection.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You need to be logged in to join events.']);
    exit;
}

// Get the event ID and user ID
$eventId = $_GET['event_id'];
$userId = $_SESSION['user_id'];

// Insert or update the join status in the `event_participants` table
$query = "INSERT INTO event_participants (event_id, user_id, status) 
          VALUES (?, ?, '1')
          ON DUPLICATE KEY UPDATE status='1'";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $eventId, $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to join the event.']);
}

$stmt->close();
$conn->close();
?>
