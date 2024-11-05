<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <?php require("navbar.php"); ?>

    <div class="admin-container">
        <h1>Administrator Panel</h1>
        <form action="update-featured-posts.php" method="post">
            <h2>Select Featured Posts</h2>
            

            <div class="post-selection">
                <label>
                    <input type="checkbox" name="featured_posts[]" value="1">
                    Post Title 1 (01.09.2024)
                </label>
            </div>
            <div class="post-selection">
                <label>
                    <input type="checkbox" name="featured_posts[]" value="2">
                    Post Title 2 (28.08.2024)
                </label>
            </div>
            <div class="post-selection">
                <label>
                    <input type="checkbox" name="featured_posts[]" value="3">
                    Post Title 3 (25.08.2024)
                </label>
            </div>

            <button type="submit">Update Featured Posts</button>
        </form>
    </div>

    <?php require 'footer.php'?>

    <script src="admin.js"></script>
</body>
</html>
