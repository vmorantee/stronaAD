<?php
require("connection.php");
header('Content-Type: application/json');

// Read the JSON data from the POST request
$data = json_decode(file_get_contents('php://input'), true);

$action = $data['action'];
$post_id = $data['post_id'];
$user_id = $data['user_id'];

// Assuming you have a database connection $conn

if ($action === 'add') {
    // Insert into the likes table
    $sql = "INSERT INTO likes (post_id, user_id) VALUES ('$post_id', '$user_id')";
} elseif ($action === 'remove') {
    // Remove from the likes table
    $sql = "DELETE FROM likes WHERE post_id='$post_id' AND user_id='$user_id'";
}

// Execute the query and get the result
if (mysqli_query($conn, $sql)) {
    // Get the updated like count
    $result = mysqli_query($conn, "SELECT COUNT(*) AS like_count FROM likes WHERE post_id='$post_id'");
    $row = mysqli_fetch_assoc($result);
    $likeCount = $row['like_count'];
    
    echo json_encode([
        'status' => 'success',
        'newLikeCount' => $likeCount
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error updating like']);
}

mysqli_close($conn);
?>
