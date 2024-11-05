<?php
require("navbar.php");
require("connection.php");

if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    
    $stmt = $conn->prepare("SELECT username, nickname, profile_picture, email, avatar, description FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($username, $nickname, $profile_picture, $email, $avatar, $description);
        $stmt->fetch();
    } else {
        echo "<p>Użytkownik nie znaleziony.</p>";
        exit();
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo "<p>Niepoprawne żądanie.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <img src="avatars/<?php echo $avatar; ?>" alt="Avatar" class="profile-avatar">
            <h1><?php echo htmlspecialchars($username); ?></h1>
            <p><?php echo htmlspecialchars($nickname); ?></p>
        </div>
        <div class="profile-info">
         <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Opis:</strong> <?php echo nl2br(htmlspecialchars($description)); ?></p>
        </div>
    </div>
</body>
</html>
