<?php
require("navbar.php");
require("connection.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['posttitle'];
    $description = $_POST['postdescription'];
    echo "<p>Description before inserting: " . htmlspecialchars($description) . "</p>";

    $date = date("Y-m-d");

    $bannerPath = '';
    if (isset($_FILES['bannerfile']) && $_FILES['bannerfile']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['bannerfile']['tmp_name'];
        $fileName = $_FILES['bannerfile']['name'];
        $fileSize = $_FILES['bannerfile']['size'];
        $fileType = $_FILES['bannerfile']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = './uploads/';
            $bannerPath = $uploadFileDir . $newFileName;
            move_uploaded_file($fileTmpPath, $bannerPath);
        }
    }

    $stmt = $conn->prepare("INSERT INTO posts (title, banner, description, is_featured, likes, created_at, user_id) VALUES (?, ?, ?, 0, 0, NOW(), ?)");
    $stmt->bind_param("sssi", $title, $bannerPath, $description, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        echo "<p>Post has been added successfully!</p>";

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
    <title>Create Post</title>
    <link rel="stylesheet" href="createpost.css">
</head>
<body>
    <div id="bodycontainer">
        <div id="titl">Utwórz post</div>
        <div id="formcontainer">
            <form id="postform" action="createpost.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="posttitle">Tytuł:</label>
                    <input type="text" id="posttitle" name="posttitle" required>
                </div>
                <div class="form-group">
                    <label for="bannerfile">Banner:</label>
                    <input type="file" id="bannerfile" name="bannerfile" accept="image/*" onchange="previewImage(event)">
                </div>
                <div id="image-preview-container" style="display: none;">
                    <img id="image-preview" alt="Image Preview" style="max-width: 100%; height: auto;">
                </div>
                <div class="form-group">
                    <label for="postdescription">Opis:</label>
                    <textarea id="postdescription" name="postdescription" rows="10" required></textarea>
                </div>
                <input type="hidden" id="postdate" name="postdate" value="<?php echo date('Y-m-d'); ?>">
                <button type="submit" id="submitbtn">Utwórz post</button>
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
