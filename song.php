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

  <?php
  session_start();

if (isset($_SESSION['error_message'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']); 
}
?>

<?php include 'enter.php';?>
<?php include 'header.php';?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>

<?php
$servername = "MySQL-8.2";
$username = "root";
$password = "";
$dbname = "myservice";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получаем ID песни 
$song_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Получаем информацию о песне
$sql = "SELECT * FROM songs WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $song_id);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo '<div class="container">';
    echo '<div class="row">';
    echo '<h1 style="margin-top: 40px; font-size: 50px;">' . htmlspecialchars($row['title']) . '</h1>';
    echo '<p style="color: #fff; font-size: 25px"><strong>Исполнитель:</strong> ' . htmlspecialchars($row['artist']) . '</p>';
    echo '<p style="color: #fff; font-size: 25px;"><strong>Жанр:</strong> ' . htmlspecialchars($row['genre']) . '</p>';
    echo '<p style="color: #fff; font-size: 25px;"><strong>Дата публикации:</strong> ' . htmlspecialchars($row['release_date']) . '</p>';

    // Кнопка «плюс»
    echo '<div>';
    echo '<form method="post" action="add_to_favorites.php" style="display:inline;">'; 
    echo '<input type="hidden" name="song_id" value="' . htmlspecialchars($row['id']) . '">';
    echo '<button type="submit" class="favorite-button" title="Добавить в избранное">+</button>';
    echo '</form>';

    // Кнопка «минус»
    echo '<form method="post" action="add_to_favorites.php" style="display:inline;">'; 
    echo '<input type="hidden" name="song_id" value="' . htmlspecialchars($row['id']) . '">';
    echo '<input type="hidden" name="action" value="remove">'; 
    echo '<button type="submit" class="favorite-button" title="Удалить из избранного">-</button>';
    echo '</form>';
    echo '</div>';

    // Проверка прав доступа
    if ((isset($_SESSION['admin']) && $_SESSION['admin'] == 1) || 
    (isset($_SESSION['id']) && $_SESSION['id'] == $row['uploaded_by'])) {
    // Кнопка «Редактировать»
    echo '<a href="edit_song.php?id=' . htmlspecialchars($row['id']) . '" class="edit-button" title="Редактировать">✎</a>';
    }

    // Кнопка для показа/скрытия текста песни
    echo '<div>';
    echo '<button class="btn btn-primary" id="toggleLyrics" style="margin-top: 20px; font-size: 16px;">Показать текст песни</button>';
    echo '</div>';
    
    // Текст песни, который изначально скрыт
    echo '<div id="lyrics" class="hidden" style="margin-top: 20px;">';
    echo '<p style="color: #fff; font-size: 20px;">' . nl2br(htmlspecialchars($row['lyrics'])) . '</p>';
    echo '</div>';

    echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="" style="width: 35%; height: auto; margin-top: 20px;  aspect-ratio: 1; object-fit: contain;">';
    
    $uniqueId = "audio_" . htmlspecialchars($row['id']);
    // Аудиоплеер
    echo '<div class="audio-player">';  
        echo '<div class="custom-audio">';  
        echo '<button class="play-btn" data-audio-id="' . $uniqueId . '">Play</button>'; 
        echo '<div class="progress-bar">';
        echo '<div class="progress"></div>'; 
        echo '</div>'; 
        echo '<span class="current-time">0:00</span> / <span class="duration">.duration</span>'; 
        echo '</div>'; 
        echo '<audio id="' . $uniqueId . '" src="' . htmlspecialchars($row['song_path']) . '" preload="metadata"></audio>'; 
        echo '</div>'; 
    
    echo '</div>'; 
    echo '</div>'; 
    
    echo '<script src="js/text.js"></script>';
    echo '<script src="js/player.js"></script>';
} else {
    echo "<p>Песня не найдена.</p>";
}



$stmt->close();
$conn->close();
?>

<?php include 'footer.php'?>

