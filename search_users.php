<?php
require("connection.php");

header('Content-Type: application/json');

$query = $_GET['query'];
$query = '%' . $query . '%'; // Wildcards for partial matching

// Prepare and execute the SQL statement
$sql = "SELECT user_id, username FROM users WHERE username LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $query);
$stmt->execute();
$result = $stmt->get_result();

// Fetch results and prepare JSON response
$users = array();
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
