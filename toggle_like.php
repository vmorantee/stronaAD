<?php
require("connection.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$postId = intval($_GET['id']);

// Check if the post is already liked
$checkLikeQuery = "SELECT * FROM likes WHERE post_id = $postId AND user_id = $userId";
$checkLikeResult = $conn->query($checkLikeQuery);

if ($checkLikeResult->num_rows > 0) {
    // Already liked, so we need to remove the like
    $deleteLikeQuery = "DELETE FROM likes WHERE post_id = $postId AND user_id = $userId";
    $conn->query($deleteLikeQuery);
    $liked = false;
} else {
    // Not liked, so we need to add the like
    $insertLikeQuery = "INSERT INTO likes (user_id, post_id) VALUES ($userId, $postId)";
    $conn->query($insertLikeQuery);
    $liked = true;
}

// Get the updated like count
$likeCountQuery = "SELECT COUNT(*) AS like_count FROM likes WHERE post_id = $postId";
$likeCountResult = $conn->query($likeCountQuery);
$likeCount = $likeCountResult->fetch_assoc()['like_count'];

// Return response
echo json_encode([
    'success' => true,
    'liked' => $liked,
    'newLikeCount' => $likeCount
]);
?>
