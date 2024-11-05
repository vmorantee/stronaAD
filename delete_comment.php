<?php
session_start();
require 'connection.php'; // Ensure you have a connection to the database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id']) && isset($_POST['comment_id'])) {
        $comment_id = intval($_POST['comment_id']);

        // Check if the user is an admin or the comment owner
        $user_id = $_SESSION['user_id'];
        $query = "SELECT user_id FROM comments WHERE comment_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $comment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $comment = $result->fetch_assoc();

        if ($comment) {
            // Check if the current user is the owner or an admin
            if ($comment['user_id'] === $user_id || $_SESSION['user_role'] == 1) {
                $delete_query = "DELETE FROM comments WHERE comment_id = ?";
                $delete_stmt = $conn->prepare($delete_query);
                $delete_stmt->bind_param('i', $comment_id);
                if ($delete_stmt->execute()) {
                    // Redirect to the previous page or another page after deletion
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                    exit();
                } else {
                    echo 'Error deleting comment. Please try again.';
                }
            } else {
                echo 'You do not have permission to delete this comment.';
            }
        } else {
            echo 'Comment not found.';
        }
    } else {
        echo 'Invalid request.';
    }
} else {
    echo 'Invalid request method.';
}
?>
