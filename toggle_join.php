<?php
require('connection.php');
session_start();

$eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

if (!$eventId || !$userId) {
    echo json_encode(['success' => false, 'message' => 'Zaloguj się lub załóż konto']);
    exit;
}

$checkQuery = "SELECT * FROM event_participants WHERE event_id = ? AND user_id = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("ii", $eventId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $unjoinQuery = "DELETE FROM event_participants WHERE event_id = ? AND user_id = ?";
    $stmt = $conn->prepare($unjoinQuery);
    $stmt->bind_param("ii", $eventId, $userId);
    $stmt->execute();
    $isJoined = false;
} else {
    $joinQuery = "INSERT INTO event_participants (event_id, user_id, status) VALUES (?, ?, 'confirmed')";
    $stmt = $conn->prepare($joinQuery);
    $stmt->bind_param("ii", $eventId, $userId);
    $stmt->execute();
    $isJoined = true;
}

echo json_encode(['success' => true, 'isJoined' => $isJoined]);
?>
