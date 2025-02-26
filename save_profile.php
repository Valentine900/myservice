<?php
session_start();

$servername = "MySQL-8.2"; 
$username = "root"; 
$password = ""; 
$dbname = "myservice"; 


$conn = new mysqli($servername, $username, $password, $dbname); 

if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}

// Проверяем, установлен ли user_id в сессии
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['id'])) {
        $userId = $_SESSION['id']; // Получаем ID пользователя из сессии

        // Проверяем наличие жанра в POST-запросе
        if (isset($_POST['favorite_genre'])) {
            $favoriteGenre = trim($_POST['favorite_genre']); /

            // Валидация длины жанра
            if (strlen($favoriteGenre) > 50) {
                echo "Жанр не должен превышать 50 символов.";
                exit;
            }

            // Обновляем информацию о пользователе в базе данных
            $stmt = $conn->prepare("UPDATE users SET favorite_genre = ? WHERE id = ?");
            
            if ($stmt) {
                // Привязываем параметры и выполняем запрос
                $stmt->bind_param("si", $favoriteGenre, $userId); 
                
                if ($stmt->execute()) {
                    header("Location: profile.php");
                    exit;
                } else {
                    echo "Ошибка выполнения запроса: " . $stmt->error;
                }
            } else {
                echo "Ошибка подготовки запроса: " . $conn->error;
            }
        } else {
            echo "Не указан любимый жанр.";
        }
    } else {
        echo "Пользователь не авторизован.";
    }
}

$conn->close();
?>