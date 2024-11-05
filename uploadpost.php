<?php
require("navbar.php");
require("connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $postTitle = $_POST['posttitle'];
    $postContent = $_POST['postcontent'];
    $postDate = $_POST['postdate'];

    // Handle image upload and cropping
    $croppedImage = $_POST['cropped-image'];

    if (!empty($croppedImage)) {
        // Decode base64 image data
        $imageData = explode(',', $croppedImage)[1];
        $imageData = base64_decode($imageData);

        // Generate a unique file name
        $imageFileName = uniqid('post_', true) . '.jpg';
        $imageFilePath = 'uploads/' . $imageFileName;

        // Save the image to the uploads directory
        if (file_put_contents($imageFilePath, $imageData) === false) {
            echo "Error saving image.";
            exit;
        }
    } else {
        $imageFileName = ''; // No image uploaded
    }

    // Insert post data into the database
    $sql = "INSERT INTO posts (title, content, image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $postTitle, $postContent,  $imageFileName);

    if ($stmt->execute()) {
        echo "<script>alert('Post został dodany!'); window.location.href = 'createpost.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
