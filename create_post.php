<?php
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
            if (move_uploaded_file($fileTmpPath, $bannerPath)) {
                echo "<p>File uploaded successfully.</p>";
            } else {
                echo "<p>Error uploading file.</p>";
            }
        } else {
            echo "<p>Unsupported file extension.</p>";
        }
    }
    $stmt = $conn->prepare("INSERT INTO posts (title, banner, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $bannerPath, $description);
    
    if ($stmt->execute()) {
        echo "<p>Post has been added successfully!</p>";
        echo "<p>Description before inserting: " . htmlspecialchars($description) . "</p>";

    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    
    if ($stmt->execute()) {
        echo "<p>Post has been added successfully!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

