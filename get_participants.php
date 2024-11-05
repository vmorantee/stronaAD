<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require("connection.php");

header('Content-Type: application/json');

$eventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

if ($eventId > 0) {
    $query = "SELECT u.username, u.avatar FROM event_participants ep JOIN users u ON ep.user_id = u.user_id WHERE ep.event_id = ? AND ep.status = 'confirmed'";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
        exit;
    }
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    $participants = [];
    while ($row = $result->fetch_assoc()) {
        $participants[] = $row;
    }

    echo json_encode(['success' => true, 'participants' => $participants]);

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
}

$conn->close();
?>
