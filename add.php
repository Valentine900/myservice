<?php
$servername = "MySQL-8.2";
$username = "root";
$password = "";
$dbname = "myservice";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Обработка формы загрузки 
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    // Получаем данные из формы
    $title = $conn->real_escape_string($_POST['title']); 
    $artist = $conn->real_escape_string($_POST['artist']); 
    $genre = $conn->real_escape_string($_POST['genre']); 
    $lyrics = isset($_POST['lyrics']) ? $conn->real_escape_string($_POST['lyrics']) : ''; // Текст песни (необязательно)
    $release_date = date('Y-m-d H:i:s'); // Дата выкладки

    // Загрузка обложки
    $image_path = 'uploads/' . basename($_FILES['image']['name']); 
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
        die("Ошибка загрузки изображения.");
    }

    // Загрузка песни
    $song_path = 'uploads/' . basename($_FILES['song']['name']); 
    if (!move_uploaded_file($_FILES['song']['tmp_name'], $song_path)) {
        die("Ошибка загрузки песни.");
    }

    // Вставка данных в базу данных
    $sql = "INSERT INTO songs (title, artist, genre, lyrics, image_path, song_path, release_date) VALUES ('$title', '$artist', '$genre', '$lyrics', '$image_path', '$song_path', '$release_date')"; 

    if ($conn->query($sql) === TRUE) { 
        echo "Новая запись успешно добавлена"; 
    } else { 
        echo "Ошибка: " . $sql . "<br>" . $conn->error; 
    } 
} 

$conn->close(); 
?> 



<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="css/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
  </head>
  <body>
<header class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-4">
                <h1>
                    <a href="/">MuSeek</a>
                </h1>
            </div>
            <nav class="col-8">
                <ul>
                    <li><a href="index.php">Главная</a></li>
                    <li><a href="all.php">Вся музыка</a></li>
                    <li><a href="add.php">Добавить песню</a></li>

                    <li>
                        <a href="profile.php">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                                <path d="M18,19v-2c0.45-0.223,1.737-1.755,1.872-2.952c0.354-0.027,0.91-0.352,1.074-1.635c0.088-0.689-0.262-1.076-0.474-1.198 c0,0,0.528-1.003,0.528-2.214c0-2.428-0.953-4.5-3-4.5c0,0-0.711-1.5-3-1.5c-4.242,0-6,2.721-6,6c0,1.104,0.528,2.214,0.528,2.214 c-0.212,0.122-0.562,0.51-0.474,1.198c0.164,1.283,0.72,1.608,1.074,1.635C10.263,15.245,11.55,16.777,12,17v2c-1,3-9,1-9,8h24 C27,20,19,22,18,19z"></path>
                            </svg>
                            Мой профиль
                        </a>
                        <ul>
                            <li><a href="logout.php">Выход</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

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
            <input type="text" class="form-control add-input" name="genre" placeholder="Жанр" required> <!-- Поле для жанра -->
        </div>    
        <div class="mb-4">    
            <textarea name="lyrics" class="form-control add-input" placeholder="Текст песни (необязательно)"></textarea> <!-- Поле для текста песни -->
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
</header> 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>


