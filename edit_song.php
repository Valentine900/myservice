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

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['id'])) {
    header('Location: login.php'); 
    exit;
}

// Получение ID песни из URL
if (isset($_GET['id'])) {
    $song_id = intval($_GET['id']);
    // Получения информации о песне
    $stmt = $conn->prepare("SELECT * FROM songs WHERE id = ?");
    $stmt->bind_param("i", $song_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $song = $result->fetch_assoc();
        
        // Проверка прав доступа
        if ($_SESSION['admin'] != 1 && $_SESSION['id'] != $song['uploaded_by']) {
            echo "У вас нет прав для редактирования этой песни.";
            exit;
        }
        
    } else {
        echo "Песня не найдена.";
        exit;
    }
} else {
    echo "Некорректный запрос.";
    exit;
}

// Обработка формы редактирования
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $artist = $_POST['artist'];

    // Переменные для хранения путей к файлам
    $image_path = $song['image_path']; 
    $audio_path = $song['song_path']; 

    // Загрузка обложки
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        // Удаление старой обложки, если она существует
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        $target_dir_covers = 'uploads/covers/';
        $image_file_type = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $image_path = $target_dir_covers . uniqid('cover_', true) . '.' . $image_file_type;

        // Проверка типа файла изображения
        if (getimagesize($_FILES['image']['tmp_name']) === false) {
            die("Файл не является изображением.");
        }

        // Проверка и загрузка изображения
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            die("Ошибка загрузки изображения.");
        }
    }

    // Загрузка песни
    if (isset($_FILES['audio']) && $_FILES['audio']['error'] == UPLOAD_ERR_OK) {
        // Удаление старой песни, если она существует
        if (file_exists($audio_path)) {
            unlink($audio_path);
        }

        $target_dir_songs = 'uploads/songs/';
        $song_file_type = strtolower(pathinfo($_FILES['audio']['name'], PATHINFO_EXTENSION));
        $audio_path = $target_dir_songs . uniqid('song_', true) . '.' . $song_file_type;

        // Проверка типа файла песни 
        if ($song_file_type != 'mp3' && $song_file_type != 'wav') {
            die("Допустимые форматы песен: mp3, wav.");
        }

        // Проверка и загрузка песни
        if (!move_uploaded_file($_FILES['audio']['tmp_name'], $audio_path)) {
            die("Ошибка загрузки песни.");
        }
    }

    // SQL запрос для обновления
    $stmt = $conn->prepare("UPDATE songs SET title = ?, artist = ?, image_path = ?, song_path = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $artist, $image_path, $audio_path, $song_id);

    if ($stmt->execute()) {
        echo "Песня успешно обновлена!";
        header('Location: index.php'); 
        exit;
    } else {
        echo "Ошибка при обновлении песни.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать песню</title>
    <!-- Подключение Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background-color: #272727;">
<div class="container mt-6" style="background-color: #272727;">
    <h1 class="text-center" style="color: #fff">Редактировать песню</h1>
    <form method="post" action="" enctype="multipart/form-data" class="mt-6">
        <div class="form-group">
            <label for="title" style="color: #fff;">Название:</label>
            <input type="text" id="title" name="title" class="form-control add-input" style="border-radius: 15px;" value="<?php echo htmlspecialchars($song['title']); ?>" required>
        </div>

        <div class="form-group">
            <label for="artist" style="color: #fff">Исполнитель:</label>
            <input type="text" id="artist" name="artist" class="form-control" style="border-radius: 15px;" value="<?php echo htmlspecialchars($song['artist']); ?>" required>
        </div>

        <div class="form-group">
            <label for="image" style="color: #fff">Загрузить новое изображение:</label>
            <input type="file" id="image" style="border-radius: 15px;color: #fff" name="image" class="form-control-file">
        </div>

        <div class="form-group">
            <label for="audio" style="color: #fff">Загрузить новый аудиофайл:</label>
            <input type="file" id="audio" style="border-radius: 15px; color: #fff" name="audio" class="form-control-file">
        </div>

        <button type="submit" class="btn btn-primary" style="background-color: #000; border: none; border-radius: 15px;">Сохранить изменения</button>
    </form>

    <a href="index.php" class="btn btn-link mt-3" style="color: #fff">Назад</a>
</div>

<!-- Подключение jQuery и Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>