<?php 
$servername = "MySQL-8.2"; 
$username = "root"; 
$password = ""; 
$dbname = "myservice"; 


$conn = new mysqli($servername, $username, $password, $dbname); 


if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
} 

// Запрос для получения песен 
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

        // Кнопка «плюс»
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

        // Уникальные идентификаторы для каждого элемента
        $uniqueId = "audio_" . htmlspecialchars($row['id']);
        
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
    } 

    echo '<script src="js/player.js"></script>';
} else { 
    echo "Нет загруженных песен."; 
} 

$conn->close(); 
?>