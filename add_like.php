<?php
require('connection.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = intval($_POST['post_id']);
    $userId = $_SESSION['user_id'];

    $query = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $postId, $userId);

    if ($stmt->execute()) {
        $query = "SELECT COUNT(*) AS like_count FROM likes WHERE post_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $likeCount = $result->fetch_assoc()['like_count'];

        echo json_encode(['success' => true, 'newLikeCount' => $likeCount]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
