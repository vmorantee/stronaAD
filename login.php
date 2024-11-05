<?php
session_start();
require("connection.php");

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Połączenie nie powiodło się: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $login_username = $_POST['username'];
        $login_password = $_POST['password'];

        // Przygotowanie zapytania SQL
        $sql = "SELECT password_hash, role_id, user_id FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Błąd podczas przygotowywania zapytania: " . $conn->error);
        }

        // Powiązanie parametrów i wykonanie zapytania
        $stmt->bind_param("s", $login_username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_object()) {
            $hashed_password = $row->password_hash;
            $role = $row->role_id;
            $user_id = $row->user_id;
            if (password_verify($login_password, $hashed_password)) {
                // Ustawienie zmiennych sesyjnych
                $_SESSION['username'] = $login_username;
                if($role == 1) $_SESSION['isAdmin'] = true;
                else  $_SESSION['isAdmin'] =false;
                $_SESSION['user_id'] = $user_id;

                // Debugowanie: Wyświetlenie user_id
                echo "<script>alert('ID użytkownika: $user_id');</script>";

                header("Location: index.php");
                exit(); // Zapewnienie, że żaden dalszy kod nie jest wykonywany
            } else {
                echo "<script>alert('Nieprawidłowa nazwa użytkownika lub hasło');</script>";
                header("Location: index.php");
                exit(); // Zapewnienie, że żaden dalszy kod nie jest wykonywany
            }
        } else {
            echo "<script>alert('Nieprawidłowa nazwa użytkownika lub hasło');</script>";
            header("Location: index.php");
            exit(); // Zapewnienie, że żaden dalszy kod nie jest wykonywany
        }

    }
}

$conn->close();
?>
