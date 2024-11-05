<?php
require("navbar.php");
require("connection.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>You must be logged in to create an event.</p>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventName = $_POST['event_name'];
    $eventType = $_POST['event_type'];
    $beverage = $_POST['beverage'];
    $eventDescription = $_POST['event_description'];
    $eventDate = $_POST['event_date'];

    // Initialize banner path
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
            if (move_uploaded_file($fileTmpPath, $bannerPath)) {
                // Success message or logging can be done here
            } else {
                echo "<p>Failed to upload banner image.</p>";
            }
        } else {
            echo "<p>Invalid file extension. Allowed extensions: jpg, jpeg, png, gif.</p>";
        }
    }

    // Insert event into database
    $stmt = $conn->prepare("INSERT INTO events (event_date, views, followers, likes, comments, event_type, beverage, created_at, event_name, event_description, creator_id, banner_url) VALUES (?, 0, 0, 0, '', ?, ?, NOW(), ?, ?, ?, ?)");

    // Bind parameters: the last parameter is the creator_id
    $stmt->bind_param("sssssss", $eventDate, $eventType, $beverage, $eventName, $eventDescription, $_SESSION['user_id'], $bannerPath);
    
    if ($stmt->execute()) {
        echo "<p>Event has been created successfully!</p>";
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
    <title>Create Event</title>
    <link rel="stylesheet" href="createevent.css">
</head>
<body>
    <div id="bodycontainer">
        <div id="titl">Utwórz wydarzenie</div>
        <div id="formcontainer">
            <form id="eventform" action="createevent.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="event_name">Nazwa wydarzenia:</label>
                    <input type="text" id="event_name" name="event_name" required>
                </div>
                <div class="form-group">
                    <label for="event_type">Typ wydarzenia:</label>
                    <select id="event_type" name="event_type" required>
                        <option value="Tryhard">Tryhard</option>
                        <option value="Fun">Fun</option>
                        <option value="Piwo">Piwo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="beverage">Rodzaj napoju:</label>
                    <select id="beverage" name="beverage" required>
                        <option value="Tyskie">Tyskie</option>
                        <option value="Halne">Halne</option>
                        <option value="Zubr">Zubr</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="event_description">Opis wydarzenia:</label>
                    <textarea id="event_description" name="event_description" rows="10" required></textarea>
                </div>
                <div class="form-group">
                    <label for="event_date">Data wydarzenia:</label>
                    <input type="date" id="event_date" name="event_date" required>
                </div>
                <div class="form-group">
                    <label for="bannerfile">Banner:</label>
                    <input type="file" id="bannerfile" name="bannerfile" accept="image/*" onchange="previewImage(event)">
                </div>
                <div id="image-preview-container" style="display: none;">
                    <img id="image-preview" alt="Image Preview" style="max-width: 100%; height: auto;">
                </div>
                <button type="submit" id="submitbtn">Utwórz wydarzenie</button>
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
