<?php
require 'navbar.php';
require 'connection.php'; 

if (!isset($_GET['post_id'])) {
    die('Nie podano post_id');
}

$post_id = intval($_GET['post_id']);

$view = "UPDATE posts SET views = views + 1 WHERE post_id = ?";
$stmt = $conn->prepare($view);
$stmt->bind_param('i', $post_id);
$stmt->execute();

$query = "SELECT posts.*, users.username AS creator_username, users.avatar AS creator_avatar
          FROM posts
          JOIN users ON posts.user_id = users.user_id
          WHERE post_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    die('Post nie znaleziony');
}


$likeCountResult = $conn->query("SELECT COUNT(*) AS like_count FROM likes WHERE post_id = $post_id");
$likeCount = $likeCountResult->fetch_assoc()['like_count'] ?? 0;
if(isset($_SESSION['user_id'])){
$isLikedResult = $conn->query("SELECT * FROM likes WHERE post_id = $post_id AND user_id = {$_SESSION['user_id']}");
$isLiked = $isLikedResult ? $isLikedResult->num_rows > 0 : false;
$class = $isLiked ? "liked-btn" : "like-btn";
}
else{
    $class = "like-btn";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_comment'])) {
        $comment_text = $_POST['comment_text'];
        $user_id = $_SESSION['user_id']; 

        if (empty($comment_text)) {
            echo '<p style="color: red;">Komentarz nie może być pusty.</p>';
        } else {
            $query = "INSERT INTO comments (user_id, comment_text, post_id) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("isi", $user_id, $comment_text, $post_id);

            if ($stmt->execute()) {

                $_SESSION['comment_added'] = true;
                header("Location: postpage.php?post_id=$post_id"); 
                exit();
            } else {
                echo '<p style="color: red;">Nie udało się dodać komentarza. Spróbuj ponownie.</p>';
            }
        }
    } elseif (isset($_POST['delete_post'])) {
        if (isset($_SESSION['user_id']) && $post['user_id'] == $_SESSION['user_id']) {
            $delete_query = "DELETE FROM posts WHERE post_id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param('i', $post_id);
            $delete_stmt->execute();
            header('Location: index.php'); // Redirect to homepage or appropriate page after deletion
            exit();
        }
    } elseif (isset($_POST['delete_comment'])) {
        if (isset($_SESSION['user_id']) && ($_SESSION['isAdmin'] || $_SESSION['user_id'] == $_POST['user_id'])) {
            $comment_id = $_POST['comment_id'];
            $delete_comment_query = "DELETE FROM comments WHERE comment_id = ? AND post_id = ?";
            $delete_comment_stmt = $conn->prepare($delete_comment_query);
            $delete_comment_stmt->bind_param('ii', $comment_id, $post_id);
            $delete_comment_stmt->execute();
            header("Location: postpage.php?post_id=$post_id"); // Redirect back to the same post
            exit();
        }
    }
}

$comments_query = "SELECT comments.*, users.username AS commenter_username, users.avatar AS commenter_avatar
                   FROM comments
                   JOIN users ON comments.user_id = users.user_id
                   WHERE post_id = ? ORDER BY comment_date DESC";
$comments_stmt = $conn->prepare($comments_query);
$comments_stmt->bind_param('i', $post_id);
$comments_stmt->execute();
$comments = $comments_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (isset($_SESSION['comment_added'])) {
    unset($_SESSION['comment_added']);
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="pagepost.css">
</head>

<body>
    <div id="bodycontainer">
        <div id="postcontainer">
            <div class="post-card">
                <div class="image-container">
                    <img src="<?php echo htmlspecialchars($post['banner']); ?>" alt="Baner posta" class="post-banner">
                </div>
                <div class="post-content">
                    <div class="post-header">
                        <img src="avatars/<?php echo htmlspecialchars($post['creator_avatar']); ?>" alt="Avatar twórcy" class="avatar">
                        <a href="profile.php?user_id=<?php echo htmlspecialchars($post['user_id']); ?>" class="creator-username"><?php echo htmlspecialchars($post['creator_username']); ?></a>
                    </div>
                    <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                    <div class="post-meta">
                        <span><?php echo htmlspecialchars($post['post_date']); ?></span>
                        <div class="action-buttons">
                            <button class="icon-btn <?php echo $class; ?>" aria-label="Like" data-post-id="<?php echo $post_id; ?>" onclick="toggleLike(<?php echo $post_id; ?>, event)">
                                <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 6.00019C10.2006 3.90317 7.19377 3.2551 4.93923 5.17534C2.68468 7.09558 2.36727 10.3061 4.13778 12.5772C5.60984 14.4654 10.0648 18.4479 11.5249 19.7369C11.6882 19.8811 11.7699 19.9532 11.8652 19.9815C11.9483 20.0062 12.0393 20.0062 12.1225 19.9815C12.2178 19.9532 12.2994 19.8811 12.4628 19.7369C13.9229 18.4479 18.3778 14.4654 19.8499 12.5772C21.6204 10.3061 21.303 7.09558 19.0485 5.17534C16.794 3.2551 13.7872 3.90317 12 6.00019Z" />
                                </svg>
                            </button>
                            <span class="like-count"><?php echo $likeCount; ?></span>
                        </div>
                        <span>Wyświetlenia: <?php echo htmlspecialchars($post['views']); ?></span>
                    </div>
                    <!-- Post Actions -->
                    <div class="post-actions">
                        <?php if (isset($_SESSION['user_id']) && $post['user_id'] === $_SESSION['user_id']): ?>
                            <a href="editpost.php?post_id=<?php echo $post['post_id']; ?>" class="btn">Edytuj</a>
                            <form action="postpage.php?post_id=<?php echo $post['post_id']; ?>" method="post" style="display: inline;">
                                <button type="submit" name="delete_post" class="btn btn-danger">Usuń</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="comments-section">
                    <h2>Komentarze</h2>

                    <!-- Comments List -->
                    <div id="comments-list">
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment">
                                <a href="profile.php?user_id=<?php echo htmlspecialchars($comment['user_id']); ?>">
                                    <img src="avatars/<?php echo htmlspecialchars($comment['commenter_avatar']); ?>" alt="Avatar komentatora" class="avatar">
                                    <span class="commenter-username"><?php echo htmlspecialchars($comment['commenter_username']); ?></span>
                                </a>
                                <p><?php echo htmlspecialchars($comment['comment_text']); ?></p>
                                <p><?php echo htmlspecialchars($comment['comment_date']); ?></p>
                                <div class="comment-actions">
                                    <?php if (isset($_SESSION['user_id']) && ($comment['user_id'] === $_SESSION['user_id'] || $_SESSION['isAdmin'])): ?>
                                        <form action="postpage.php?post_id=<?php echo htmlspecialchars($post_id); ?>" method="post" style="display: inline;">
                                            <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['comment_id']); ?>">
                                            <button type="submit" name="delete_comment" class="btn btn-danger">Usuń</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="postpage.php?post_id=<?php echo $post_id; ?>" method="post">
                            <textarea name="comment_text" rows="4" placeholder="Dodaj komentarz..."></textarea>
                            <button type="submit" name="submit_comment" class="btn">Dodaj komentarz</button>
                        </form>
                    <?php else: ?>
                        <p>Musisz być zalogowany, aby dodać komentarz.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
