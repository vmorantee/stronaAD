<?php
require("navbar.php");
require("connection.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

// Fetch post details
$stmt = $conn->prepare("SELECT title, banner, description FROM posts WHERE post_id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['posttitle'];
    $description = $_POST['postdescription'];

    // Handle banner image upload
    $bannerPath = $post['banner'];
    if (isset($_FILES['bannerfile']) && $_FILES['bannerfile']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['bannerfile']['tmp_name'];
        $fileName = $_FILES['bannerfile']['name'];
        $fileSize = $_FILES['bannerfile']['size'];
        $fileType = $_FILES['bannerfile']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Check if file is an image
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = './uploads/';
            $bannerPath = $uploadFileDir . $newFileName;
            move_uploaded_file($fileTmpPath, $bannerPath);
        }
    }

    // Update post in database
    $stmt = $conn->prepare("UPDATE posts SET title = ?, banner = ?, description = ? WHERE post_id = ? AND user_id = ?");
    $stmt->bind_param("sssii", $title, $bannerPath, $description, $post_id, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        echo "<p>Post has been updated successfully!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="createpost.css">
</head>
<body>
    <div id="navbar">
        <!-- Navigation links -->
    </div>
    <div id="bodycontainer">
        <div id="titl">Edytuj post</div>
        <div id="formcontainer">
            <form id="postform" action="editpost.php?post_id=<?php echo $post_id; ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="posttitle">Tytuł:</label>
                    <input type="text" id="posttitle" name="posttitle" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="bannerfile">Banner:</label>
                    <input type="file" id="bannerfile" name="bannerfile" accept="image/*" onchange="previewImage(event)">
                </div>
                <div id="image-preview-container" style="display: <?php echo $post['banner'] ? 'block' : 'none'; ?>;">
                    <img id="image-preview" src="<?php echo htmlspecialchars($post['banner']); ?>" alt="Image Preview" style="max-width: 100%; height: auto;">
                </div>
                <div class="form-group">
                    <label for="postdescription">Opis:</label>
                    <textarea id="postdescription" name="postdescription" rows="10" required><?php echo htmlspecialchars($post['description']); ?></textarea>
                </div>
                <button type="submit" id="submitbtn">Zaktualizuj post</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.previewImage = function(event) {
                const file = event.target.files[0];
                const previewContainer = document.getElementById('image-preview-container');
                const imgPreview = document.getElementById('image-preview');

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        imgPreview.src = e.target.result;
                        previewContainer.style.display = 'block';
                    };

                    reader.readAsDataURL(file);
                } else {
                    previewContainer.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>
