<?php
session_start(); // Убедитесь, что сессия запущена для доступа к идентификатору пользователя

$servername = "MySQL-8.2";
$username = "root";
$password = "";
$dbname = "myservice";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Проверяем, что пользователь аутентифицирован и у него есть идентификатор
if (!isset($_SESSION['id'])) {
    die("Вы должны быть авторизованы для загрузки песни.");
}

$uploaded_by = $_SESSION['id']; // Получаем ID пользователя из сессии

// Обработка формы добавления песни
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    // Получаем данные из формы
    $title = $conn->real_escape_string($_POST['title']); 
    $artist = $conn->real_escape_string($_POST['artist']); 
    $genre = $conn->real_escape_string($_POST['genre']); 
    $lyrics = isset($_POST['lyrics']) ? $conn->real_escape_string($_POST['lyrics']) : ''; 
    $release_date = date('Y-m-d H:i:s');

    // Загрузка обложки
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

    // Загрузка песни
    $target_dir_songs = 'uploads/songs/';
    $song_file_type = strtolower(pathinfo($_FILES['song']['name'], PATHINFO_EXTENSION));
    $song_path = $target_dir_songs . uniqid('song_', true) . '.' . $song_file_type;

    // Проверка типа файла песни (например, mp3)
    if ($song_file_type != 'mp3' && $song_file_type != 'wav') {
        die("Допустимые форматы песен: mp3, wav.");
    }

    // Проверка и загрузка песни
    if (!move_uploaded_file($_FILES['song']['tmp_name'], $song_path)) {
        die("Ошибка загрузки песни.");
    }

    // Вставка данных в базу данных с использованием подготовленных выражений
    $stmt = $conn->prepare("INSERT INTO songs (title, artist, genre, lyrics, image_path, song_path, release_date, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssssssss", $title, $artist, $genre, $lyrics, $image_path, $song_path, $release_date, $uploaded_by);
        
        if ($stmt->execute()) { 
            echo "<div class='container'>";
            echo "<span style='color: #fff; font-size: 20px;'>Новая песня успешно добавлена!</span>";
            echo "</div>";
        } else { 
            echo "Ошибка: " . $stmt->error; 
        }
        $stmt->close();
    } else {
        echo "Ошибка подготовки запроса: " . $conn->error;
    }
} 

$conn->close(); 
?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MuSeek</title>
    <link rel="icon" href="images/waves.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="css/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
  </head>
  <body>  

<?php include 'enter.php';?>
<?php if (isset($_SESSION['id'])): ?> 
<?php include 'header.php';?>

<div class="container mt-5">
    <h2>Загрузить песню</h2> 
    <form action="add.php" method="post" enctype="multipart/form-data"> 
        <div class="mb-4">
            <input type="text" class="form-control add-input" name="title" placeholder="Название песни" required>
        </div>
        <div class="mb-4">    
            <input type="text" class="form-control add-input" name="artist" placeholder="Исполнитель" required>
        </div>    
        <div class="mb-4">    
            <input type="text" class="form-control add-input" name="genre" placeholder="Жанр" required> 
        </div>    
        <div class="mb-4">    
            <textarea name="lyrics" class="form-control add-input" placeholder="Текст песни (необязательно)"></textarea> 
        </div>    
        <div class="mb-4">  
            <label class="custom-file-upload">  
                <input type="file" class="form-control add-input" name="image" accept="image/*" required> 
                Выберите обложку (обязательно квадратную)
            </label>
        </div>    
        <div class="mb-4">    
            <label class="custom-file-upload">
                <input type="file" class="form-control add-input" name="song" accept="audio/*" required> 
                Выберите музыкальный файл
            </label>
        </div>    
            <button type="submit" class="btn btn-primary">Загрузить</button> 
    </form> 
</div>


<?php else: ?>
<h1>Сначала нужно зарегистрироваться!</h1>
<a href="register.php" style="color: #ffffff; text-decoration: underline;">Регистрация</a>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>


