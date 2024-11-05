<?php
session_start();
require 'connection.php'; // Upewnij się, że masz połączenie z bazą danych

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id']) && isset($_POST['post_id'])) {
        $post_id = intval($_POST['post_id']);
        $user_id = $_SESSION['user_id'];

        // Pobierz ID użytkownika, który dodał post
        $query = "SELECT user_id FROM posts WHERE post_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $post = $result->fetch_assoc();

        if ($post) {
            // Sprawdź, czy aktualny użytkownik jest właścicielem posta lub administratorem
            if ($post['user_id'] === $user_id || $_SESSION['user_role'] == 1) {
                $delete_query = "DELETE FROM posts WHERE post_id = ?";
                $delete_stmt = $conn->prepare($delete_query);
                $delete_stmt->bind_param('i', $post_id);
                if ($delete_stmt->execute()) {
                    // Przekieruj na stronę główną lub inną stronę po usunięciu
                    header('Location: index.php');
                    exit();
                } else {
                    echo 'Błąd podczas usuwania posta. Proszę spróbować ponownie.';
                }
            } else {
                echo 'Nie masz uprawnień do usunięcia tego posta.';
            }
        } else {
            echo 'Post nie został znaleziony.';
        }
    } else {
        echo 'Nieprawidłowe żądanie.';
    }
} else {
    echo 'Nieprawidłowa metoda żądania.';
}
?>
