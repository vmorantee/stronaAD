<?php
// Function to handle file uploads
function uploadFile($file) {
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Check if file is an actual image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return "File is not an image.";
    }

    // Check file size (limit to 5MB)
    if ($file["size"] > 5000000) {
        return "Sorry, your file is too large.";
    }

    // Allow certain file formats
    $allowedFormats = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedFormats)) {
        return "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    }

    // Check if $targetFile already exists
    if (file_exists($targetFile)) {
        return "Sorry, file already exists.";
    }

    // Try to upload file
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return $targetFile;
    } else {
        return "Sorry, there was an error uploading your file.";
    }
}

// Function to handle URL uploads
function uploadFromURL($url) {
    $targetDir = "uploads/";
    $imageContent = file_get_contents($url);
    if ($imageContent === false) {
        return "Failed to fetch image from URL.";
    }
    
    $imageFileType = strtolower(pathinfo($url, PATHINFO_EXTENSION));
    $targetFile = $targetDir . uniqid() . "." . $imageFileType;
    
    // Allow certain file formats
    $allowedFormats = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedFormats)) {
        return "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    }
    
    if (file_put_contents($targetFile, $imageContent)) {
        return $targetFile;
    } else {
        return "Sorry, there was an error saving your file.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['posttitle'];
    $content = $_POST['postcontent'];
    $date = $_POST['postdate'];

    $uploadResult = "";

    // Handle file upload
    if (isset($_FILES['postimagefile']) && $_FILES['postimagefile']['error'] == 0) {
        $uploadResult = uploadFile($_FILES['postimagefile']);
    } 
    // Handle URL upload
    elseif (!empty($_POST['postimageurl'])) {
        $uploadResult = uploadFromURL($_POST['postimageurl']);
    } 

    // Save post details (e.g., to a database) here...

    // Provide feedback
    if (strpos($uploadResult, "uploads/") === 0) {
        echo "Post uploaded successfully. Image URL: " . $uploadResult;
    } else {
        echo "Error: " . $uploadResult;
    }
}
?>
