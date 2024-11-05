<?php
include 'connection.php';

session_start();
$user_id = $_SESSION['user_id'];

$sql = "SELECT username, description, avatar, password_hash FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $description = $_POST['description'];
        $avatar = $user['avatar'];

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
            $avatar = basename($_FILES['avatar']['name']);
            $target = 'avatars/' . $avatar;

            // Ensure the directory exists and has write permissions
            if (!is_dir('avatars')) {
                mkdir('avatars', 0755, true);
            }

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
                // Update avatar URL in the database
                $sql = "UPDATE users SET description = ?, avatar = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $description, $avatar, $user_id);
                $stmt->execute();
            } else {
                echo "<script>alert('Wystąpił problem z przesyłaniem pliku.');</script>";
            }
        } else {
            // Update description only if no new file is uploaded
            $sql = "UPDATE users SET description = ?, avatar = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $description, $avatar, $user_id);
            $stmt->execute();
        }

        header("Location: index.php");
        exit;
    } elseif (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];

        $sql = "SELECT password_hash FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $hashed_password = $user['password_hash'];

        if (password_verify($old_password, $hashed_password)) {
            $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $sql = "UPDATE users SET password_hash = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_hashed_password, $user_id);
            $stmt->execute();
            
            header("Location: index.php");
            exit;
        } else {
            echo "<script>alert('Stare hasło jest niepoprawne');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="settings.css">
    <title>Ustawienia</title>
</head>
<body>
    <div id="bodycontainer">
        <h1>Ustawienia</h1>
        
        <form action="settings.php" method="post" enctype="multipart/form-data">
            <h2>Aktualizacja profilu</h2>
            <p>
                <label for="description">Opis:</label><br>
                <textarea id="description" name="description"><?php echo htmlspecialchars($user['description']); ?></textarea>
            </p>
            <p>
                <label for="avatar">Avatar:</label><br>
                <input type="file" id="avatar" name="avatar">
                <?php if ($user['avatar']): ?>
                    <br><img src="avatars/<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" width="100">
                <?php endif; ?>
            </p>
            <p>
                <input type="submit" name="update_profile" value="Aktualizuj profil">
            </p>
        </form>
        
        <form action="settings.php" method="post">
            <h2>Zmiana hasła</h2>
            <p>
                <label for="old_password">Stare hasło:</label><br>
                <input type="password" id="old_password" name="old_password" required>
            </p>
            <p>
                <label for="new_password">Nowe hasło:</label><br>
                <input type="password" id="new_password" name="new_password" required>
            </p>
            <p>
                <input type="submit" name="change_password" value="Zmień hasło">
            </p>
        </form>
    </div>
</body>
</html>
