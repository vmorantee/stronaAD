<?php
require("navbar.php");
require("connection.php");

if (!isset($_SESSION['user_id'])) {
    echo "<p>Musisz być zalogowany, aby edytować wydarzenie.</p>";
    exit();
}

if (!isset($_GET['event_id']) || !is_numeric($_GET['event_id'])) {
    echo "<p>Nieprawidłowy identyfikator wydarzenia.</p>";
    exit();
}

$eventId = $_GET['event_id'];

$stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ? AND creator_id = ?");
$stmt->bind_param("ii", $eventId, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>Nie znaleziono wydarzenia lub nie masz uprawnień do jego edycji.</p>";
    exit();
}

$event = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventName = $_POST['event_name'];
    $eventType = $_POST['event_type'];
    $beverage = $_POST['beverage'];
    $eventDescription = $_POST['event_description'];
    $eventDate = $_POST['event_date'];

    $bannerPath = $event['banner_url'];
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
            } else {
                echo "<p>Nie udało się przesłać obrazu banera.</p>";
            }
        } else {
            echo "<p>Nieprawidłowe rozszerzenie pliku. Dozwolone rozszerzenia: jpg, jpeg, png, gif.</p>";
        }
    }

    $stmt = $conn->prepare("UPDATE events SET event_date = ?, event_type = ?, beverage = ?, event_name = ?, event_description = ?, banner_url = ? WHERE event_id = ? AND creator_id = ?");
    $stmt->bind_param("ssssssii", $eventDate, $eventType, $beverage, $eventName, $eventDescription, $bannerPath, $eventId, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        echo "<p>Wydarzenie zostało zaktualizowane pomyślnie!</p>";
    } else {
        echo "<p>Błąd: " . $stmt->error . "</p>";
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
    <title>Edytuj wydarzenie</title>
    <link rel="stylesheet" href="createevent.css">
</head>
<body>
    <div id="bodycontainer">
        <div id="titl">Edytuj wydarzenie</div>
        <div id="formcontainer">
            <form id="eventform" action="eventedit.php?event_id=<?php echo htmlspecialchars($eventId); ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="event_name">Nazwa wydarzenia:</label>
                    <input type="text" id="event_name" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="event_type">Typ wydarzenia:</label>
                    <select id="event_type" name="event_type" required>
                        <option value="Tryhard" <?php if ($event['event_type'] == 'Tryhard') echo 'selected'; ?>>Tryhard</option>
                        <option value="Fun" <?php if ($event['event_type'] == 'Fun') echo 'selected'; ?>>Fun</option>
                        <option value="Piwo" <?php if ($event['event_type'] == 'Piwo') echo 'selected'; ?>>Piwo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="beverage">Rodzaj napoju:</label>
                    <select id="beverage" name="beverage" required>
                        <option value="Tyskie" <?php if ($event['beverage'] == 'Tyskie') echo 'selected'; ?>>Tyskie</option>
                        <option value="Halne" <?php if ($event['beverage'] == 'Halne') echo 'selected'; ?>>Halne</option>
                        <option value="Zubr" <?php if ($event['beverage'] == 'Zubr') echo 'selected'; ?>>Zubr</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="event_description">Opis wydarzenia:</label>
                    <textarea id="event_description" name="event_description" rows="10" required><?php echo htmlspecialchars($event['event_description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="event_date">Data wydarzenia:</label>
                    <input type="date" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="bannerfile">Banner:</label>
                    <input type="file" id="bannerfile" name="bannerfile" accept="image/*" onchange="previewImage(event)">
                </div>
                <div id="image-preview-container" style="display: <?php echo $event['banner_url'] ? 'block' : 'none'; ?>;">
                    <img id="image-preview" src="<?php echo htmlspecialchars($event['banner_url']); ?>" alt="Podgląd obrazu" style="max-width: 100%; height: auto;">
                </div>
                <button type="submit" id="submitbtn">Zaktualizuj wydarzenie</button>
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
