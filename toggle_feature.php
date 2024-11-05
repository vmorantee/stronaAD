<?php

require("connection.php");

if (isset($_GET['id'])) {
    $postId = intval($_GET['id']);
    $query = "UPDATE posts SET is_featured = NOT is_featured WHERE post_id = $postId";
    if ($conn->query($query) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

$conn->close();
?>