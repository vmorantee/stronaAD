<?php
require("connection.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        $reg_username = $_POST['reg-username'];
        $reg_email = $_POST['reg-email'];
        $reg_password = password_hash($_POST['reg-password'], PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (username, email, password_hash, avatar,role_id) VALUES (?, ?, ?,'default.jfif',2)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            echo "Error preparing statement: " . $conn->error;
        } else {
            $stmt->bind_param("sss", $reg_username, $reg_email, $reg_password);

            if ($stmt->execute()) {
                echo "<script>alert('Registration successful!');</script>";
            } else {
                echo "<script>alert('Error: " . $stmt->error . "');</script>";
            }

            $stmt->close();
        }
    }
}
if(isset($_SESSION['user_id'])) $userId=$_SESSION['user_id'];
$conn->close();
?>

<link rel="stylesheet" href="navbar.css">
<div id="navbar">
    <img src="icons/images.png" width="50px" alt="">
    <a href="index.php">Strona główna</a>
    <a href="eventposts.php">Wydarzenia</a>
    <a href="aboutus.php">O nas</a>
    <div id="login-container">
        <?php if(isset($_SESSION["username"])): ?>
            <div id="user-menu" class="user-menu">
    <?php echo "<span id='username'>{$_SESSION['username']}</span>"; ?>
    <div class="dropdown-menu">
        <a href="profile.php?user_id=<?=$userId?>">Profil</a>
        <a href="settings.php">Ustawienia</a>
        <a href="logout.php">Wyloguj</a>
    </div>
</div>
        <?php else: ?>
            <button id="loginBtn">Login</button>
            <button id="registerBtn">Register</button>
        <?php endif; ?>
    </div>
</div>

<div id="overlay"></div>

<div id="login-popup" class="popup">
    <span class="close-btn" id="closePopup">&times;</span>
    <h2>Login</h2>
    <form method="POST" action="login.php">
        <label for="username">Nazwa użytkownika</label>
        <input type="text" id="username" name="username" placeholder="Login" required>

        <label for="password">Hasło</label>
        <input type="password" id="password" name="password" placeholder="Hasło" required>

        <button type="submit" name="login">Zaloguj</button>
    </form>
</div>


<div id="register-popup" class="popup">
    <span class="close-btn" id="closeRegisterPopup">&times;</span>
    <h2>Rejestracja</h2>
    <form method="POST" action="">
        <label for="reg-username">Nazwa użytkownika</label>
        <input type="text" id="reg-username" name="reg-username" placeholder="Login" required>

        <label for="reg-email">Email</label>
        <input type="email" id="reg-email" name="reg-email" placeholder="E-mail" required>

        <label for="reg-password">Hasło</label>
        <input type="password" id="reg-password" name="reg-password" placeholder="Hasło" required>

        <button type="submit" name="register">Zarejestruj</button>
    </form>
</div>

<script src="navbar.js"></script>
