<?php

require("navbar.php");
require("connection.php");
if (isset($_SESSION['user_id']))
    $isAdmin = $_SESSION['isAdmin'];
else {
    $isAdmin = false;
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="style.css">
    <script>
        function toggleFeature(postId, event) {
            fetch('toggle_feature.php?id=' + postId, {
                method: 'GET'
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const starIcon = document.querySelector(`.star-icon[data-post-id="${postId}"]`);
                        if (starIcon.classList.contains('star-icon-empty')) {
                            starIcon.classList.remove('star-icon-empty');
                            starIcon.classList.add('star-icon-filled');
                        } else {
                            starIcon.classList.remove('star-icon-filled');
                            starIcon.classList.add('star-icon-empty');
                        }
                    } else {
                        console.error('Failed to toggle feature.');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                });
        }
        function toggleFollow(postId, event) {

            fetch('toggle_follow.php?id=' + postId, {
                method: 'GET'
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const followButton = document.querySelector(`.follow-btn[data-post-id="${postId}"]`);
                        const followCount = followButton.nextElementSibling;
                        followCount.textContent = data.newFollowCount;
                    } else {
                        console.error('Failed to toggle follow.');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                });
        }

        function toggleLike(postId, event) {

            fetch('toggle_like.php?id=' + postId, {
                method: 'GET'
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const likeButton = document.querySelector(`.like-btn[data-post-id="${postId}"]`);
                        const likeCount = likeButton.nextElementSibling;
                        likeCount.textContent = data.newLikeCount;
                    } else {
                        console.error('Failed to toggle like.');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>
</head>

<body>
    <div id="bodycontainer">
        <div id="titl">Ostatnie wpisy</div>
        <div id="maincontainer">
            <div id="postcontainer">
                <?php
                if (isset($_GET['search']) && !empty($_GET['search']) && $_GET['sort'] == 'all') {
                    $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);
                    $query = "SELECT * FROM posts";

                } else if (isset($_GET['search']) && !empty($_GET['search']) && $_GET['sort'] == 'title') {
                    $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);
                    $query = "SELECT * FROM posts WHERE title LIKE '%$searchTerm%'";

                } else if (isset($_GET['search']) && !empty($_GET['search']) && $_GET['sort'] == 'description') {
                    $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);
                    $query = "SELECT * FROM posts WHERE description LIKE '%$searchTerm%'";
                } else {
                    $query = "SELECT * FROM posts ORDER BY is_featured DESC, created_at ";

                }
                $result = $conn->query($query);

                while ($row = $result->fetch_assoc()) {
                    $postId = $row['post_id'];
                    $checkQuery = "SELECT is_featured FROM posts WHERE post_id = $postId";
                    $checkResult = $conn->query($checkQuery);
                    if (isset($_SESSION['user_id'])) {
                        $userId = $_SESSION['user_id'];

                        $isFeatured = $checkResult->fetch_assoc()['is_featured'];
                        $starClass = $isFeatured ? 'star-icon-filled' : 'star-icon-empty';
                        $isLikedResult = $conn->query("SELECT * FROM likes WHERE post_id = $postId AND user_id = $userId");
                        $isLiked = $isLikedResult->num_rows > 0;
                        if ($isLiked)
                            $class = "liked-btn";
                        else
                            $class = "like-btn";
                    } else {
                        $class = "like-btn";
                    }
                    $likeCountResult = $conn->query("SELECT COUNT(*) AS like_count FROM likes WHERE post_id = $postId");
                    $likeCount = $likeCountResult->fetch_assoc()['like_count'];


                    echo '
                
                <div class="post-card">
                    <div class="image-container">
                        <img src="' . $row['banner'] . '" alt="' . $row['title'] . '" class="post-banner">
                        <div class="post-feature">';
                    if ($isAdmin) {
                        echo '
                    <div class="star-icon ' . $starClass . '" data-post-id="' . $postId . '" onclick="toggleFeature(' . $postId . ', event)">
                        <!-- SVG for star icon -->
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 .587l3.668 7.568L24 9.432l-6 5.85 1.416 8.385L12 19.771l-7.416 3.896L6 15.282l-6-5.85 8.332-1.276L12 .587z"/>
                        </svg>
                    </div>';
                    }
                    echo '
                        </div>
                    </div>
                    <div class="post-content">
                        <a class="posta" href="postpage.php?post_id=' . $postId . '"><div class="post-title">' . htmlspecialchars($row['title']) . '</div>
                        <div class="post-meta">
                            <span class="views">👁️ ' . intval($row['views']) . '</span>
                            <span class="date">📅 ' . date("d.m.Y", strtotime($row['created_at'])) . '</span>
                        </div>
                        <div class="post-description">
                            ' . htmlspecialchars($row['description']) . '
                        </div>
                        <div class="action-buttons">
                            <button class="icon-btn ' . $class . '" aria-label="Like" data-post-id="' . $postId . '" onclick="toggleLike(' . $postId . ', event)">
                                <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 6.00019C10.2006 3.90317 7.19377 3.2551 4.93923 5.17534C2.68468 7.09558 2.36727 10.3061 4.13778 12.5772C5.60984 14.4654 10.0648 18.4479 11.5249 19.7369C11.6882 19.8811 11.7699 19.9532 11.8652 19.9815C11.9483 20.0062 12.0393 20.0062 12.1225 19.9815C12.2178 19.9532 12.2994 19.8811 12.4628 19.7369C13.9229 18.4479 18.3778 14.4654 19.8499 12.5772C21.6204 10.3061 21.303 7.09558 19.0485 5.17534C16.793 3.2551 13.7862 3.90317 12 6.00019Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <span class="like-count">' . intval($likeCount) . '</span>
                        </div>
                    </div>
                </div></a>';

                }
                ?>
            </div>
            <div id="searchcontainer">
                <form action="index.php" method="get">
                    <label for="search">Wyszukaj:</label>
                    <input type="text" name="search" id="srch"
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <label for="sort">Filtruj:</label>
                    <select name="sort" id="sort">
                        <option value="all" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'all' ? 'selected' : ''; ?>>Wszystkie</option>
                        <option value="title" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'title' ? 'selected' : ''; ?>>Tytuł</option>
                        <option value="description" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'description' ? 'selected' : ''; ?>>Opis</option>
                    </select>
                    <br>
                    <div id="btndiv">
                        <button type="submit" id="confirm">Zastosuj</button>
                    </div>
                </form>
                <?php if ($isAdmin)
                    echo '<a href="createpost.php"><div>Dodaj post</div></a>'; ?>
            </div>

        </div>
    </div>

    <?php require("footer.php") ?>
    <script src="navbar.js"></script>
    <script src="script.js"></script>
</body>

</html>