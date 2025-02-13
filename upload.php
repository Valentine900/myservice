<?php 
$servername = "MySQL-8.2"; 
$username = "root"; 
$password = ""; 
$dbname = "myservice"; 

// Подключение к базе данных 
$conn = new mysqli($servername, $username, $password, $dbname); 

// Проверка подключения 
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
} 

// SQL-запрос для получения песен 
$sql = "SELECT * FROM songs"; 
$result = $conn->query($sql); 

if ($result->num_rows > 0) { 
    while ($row = $result->fetch_assoc()) { 
        echo '<div class="post row">'; 
        echo '<div class="img col-12 col-md-4">'; 
        echo '<a href="song.php?id=' . htmlspecialchars($row['id']) . '">';
        echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="" class="img-fluid" style="width: 35%; height: auto;">'; 
        echo '</a>'; 
        echo '</div>'; 
        echo '<div class="post_text col-12 col-md-4">'; 
        echo '<h3>'; 
        echo '<a href="song.php?id=' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['title']) . '</a>';  
        echo '</h3>'; 
        echo '<i class="user">' . htmlspecialchars($row['artist']) . '</i>'; 

        echo '<div class="heart-icon" style="cursor: pointer;" title="Добавить в избранное">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff" class="bi bi-heart" viewBox="0 0 24 24">';
        echo '<path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>';
        echo '</svg>';
        echo '</div>';

        // Уникальные идентификаторы для каждого элемента
        $uniqueId = "audio_" . htmlspecialchars($row['id']);
        
        echo '<div class="audio-player">';  
        echo '<div class="custom-audio">';  
        echo '<button class="play-btn" data-audio-id="' . $uniqueId . '">Play</button>'; 
        echo '<div class="progress-bar">';
        echo '<div class="progress"></div>'; 
        echo '</div>'; 
        echo '<span class="current-time">0:00</span>';  
        echo '<span class="duration">0:00</span>'; 
        echo '</div>'; 
        echo '<audio id="' . $uniqueId . '" src="' . htmlspecialchars($row['song_path']) . '" preload="metadata"></audio>'; 
        echo '</div>'; 
        echo '</div>'; 
        echo '</div>'; 
    } 

    // Переместите скрипт в конец, чтобы он загружался после всех элементов
    echo '<script src="js/player.js"></script>';
} else { 
    echo "Нет загруженных песен."; 
} 

$conn->close(); 
?>