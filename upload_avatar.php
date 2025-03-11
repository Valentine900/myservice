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

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['id'])) {
    die("Вы не авторизованы.");
}

$user_id = $_SESSION['id']; // Получаем ID пользователя

// Получаем текущий аватар пользователя
$stmt = $conn->prepare("SELECT avatar FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$current_avatar = null;

if ($row = $result->fetch_assoc()) {
    $current_avatar = $row['avatar'];
}

$stmt->close();

// Проверяем, была ли загружена форма
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar'])) {
    // Папка для загрузки
    $target_dir = "uploads/userpics/";
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));

    // Проверка, является ли загруженный файл изображением
    $check = getimagesize($_FILES["avatar"]["tmp_name"]);
    if ($check !== false) {
        echo "Файл является изображением - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "Файл не является изображением.";
        $uploadOk = 0;
    }

    // Проверка размера файла 
    if ($_FILES["avatar"]["size"] > 2000000) {
        echo "Извините, ваш файл слишком большой.";
        $uploadOk = 0;
    }

    // Только определенные форматы файлов
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Извините, только JPG, JPEG, PNG и GIF файлы разрешены.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Извините, файл не был загружен.";
    } else {
        // Генерация уникального имени файла
        $new_file_name = uniqid('avatar_', true) . '.' . $imageFileType; // Создаем уникальное имя
        $target_file = $target_dir . $new_file_name; // Полный путь к файлу

        // Загружаем файл, если прошли все проверки
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
            echo "Файл " . htmlspecialchars(basename($new_file_name)) . " был загружен.";

            // Обновляем путь к аватару в базе данных
            $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->bind_param("si", $target_file, $user_id);

            if ($stmt->execute()) {
                echo "Запись успешно обновлена.";

                // Удаляем старый аватар, если он существует
                if ($current_avatar && file_exists($current_avatar)) {
                    unlink($current_avatar);
                }

                header("Location: profile.php");
                exit();
            } else {
                echo "Ошибка обновления записи: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Извините, произошла ошибка при загрузке вашего файла.";
        }
    }
} else {
    echo "Нет файла для загрузки.";
}

$conn->close(); 
?>