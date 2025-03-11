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


// Получение id из сессии
$id = $_SESSION['id'] ?? null; 

// Получение admin из сессии
$admin = $_SESSION['admin'] ?? null; 

// Получаю данные о пользователе
$query = "SELECT users.username, users.email, users.avatar, users.favorite_genre, users.favorite_song_ids
          FROM users 
          WHERE users.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Если пользователь не найден
    echo "Пользователь не найден.";
    exit();
}
?>

<?php
  session_start(); 

if (isset($_SESSION['error_message'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']); 
}
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
</head>
<body>
    
<?php include 'header.php';?>

<div class="container mt-5">
    <h2>Профиль пользователя</h2>
    <?php if (isset($user)): ?>
        <div class="row">
            <div class="col-md-4">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?php echo htmlspecialchars($user['avatar']) . '?t=' . time(); ?>" alt="Аватар" class="img-fluid avatar" style="width: 50%; height: auto; border: 2px solid #fff; border-radius: 25px;">
                <?php else: ?>
                    <img src="images/profile.png" alt="Аватар по умолчанию" class="img-fluid rounded-circle" style="width: 50%; height: auto;">
                <?php endif; ?>
            </div>
            <div class="col-md-8">
                <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                <p style='color: #ffffff; font-size: 20px;'><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p style='color: #ffffff; font-size: 20px;'><strong>Любимый жанр:</strong> 
                <?php echo htmlspecialchars($user['favorite_genre'] ?? 'Не указан'); ?>
                </p>
                <!-- Форма для загрузки аватарки -->
                <form action="upload_avatar.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="avatar" class="custom-file-upload" style='color: #ffffff;'>Загрузить новый аватар:
                        <input type="file" class="form-control add-input" id="avatar" name="avatar" accept="image/*" required>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary">Загрузить</button>
                </form>
            </div>
        </div>
        <div class="mt-4">          
            <h4>Редактировать любимый жанр</h4>
            <form method="POST" action="save_profile.php">                
                <div class="mb-3">
                   <label for="genre" class="form-label choose-category" style='color: #ffffff'>Выберите любимый жанр:                   
                   <select name="favorite_genre" id="genre"class="form-select genre-select">
                       <option value="" disabled <?php echo empty($user['favorite_genre']) ? 'selected' : ''; ?>>Выберите жанр</option>                        
                       <option value="Рок" <?php echo (isset($user['favorite_genre']) && $user['favorite_genre'] === 'Рок') ? 'selected' : ''; ?>>Рок</option>
                       <option value="Поп" <?php echo (isset($user['favorite_genre']) && $user['favorite_genre'] === 'Поп') ? 'selected' : ''; ?>>Поп</option>                        
                       <option value="Джаз" <?php echo (isset($user['favorite_genre']) && $user['favorite_genre'] === 'Джаз') ? 'selected' : ''; ?>>Джаз</option>
                       <option value="Классическая музыка" <?php echo (isset($user['favorite_genre']) && $user['favorite_genre'] === 'Классическая музыка') ? 'selected' : ''; ?>>Классическая музыка</option>                       
                       <option value="Хип-хоп" <?php echo (isset($user['favorite_genre']) && $user['favorite_genre'] === 'Хип-хоп') ? 'selected' : ''; ?>>Хип-хоп</option>
                       <option value="Альтернатива" <?php echo (isset($user['favorite_genre']) && $user['favorite_genre'] === 'Альтернатива') ? 'selected' : ''; ?>>Альтернатива</option>
                       <option value="Гранж" <?php echo (isset($user['favorite_genre']) && $user['favorite_genre'] === 'Гранж') ? 'selected' : ''; ?>>Гранж</option>
                       <option value="Регги" <?php echo (isset($user['favorite_genre']) && $user['favorite_genre'] === 'Регги') ? 'selected' : ''; ?>>Регги</option>
                       <option value="Электронная музыка" <?php echo (isset($user['favorite_genre']) && $user['favorite_genre'] === 'Электронная музыка') ? 'selected' : ''; ?>>Электронная музыка</option>
                       <option value="Эмбиент" <?php echo (isset($user['favorite_genre']) && $user['favorite_genre'] === 'Эмбиент') ? 'selected' : ''; ?>>Эмбиент</option>
                       </select>
                       </label> 
                </div>               
                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            </form>        
        </div>
    </div>
        <div class="container" style='margin-top: 25px;'>
            <div class="content row">
                <div class="main-content col-md-9 col-12">
                    <h4>Любимые песни</h4>

                    <div class="container mt-4">
                <div id="songs-list">
                <?php
                if (!empty($user['favorite_song_ids'])) {
                    $favoriteSongs = explode(',', $user['favorite_song_ids']); 
                    $placeholders = implode(',', array_fill(0, count($favoriteSongs), '?'));

                    $stmt = $conn->prepare("SELECT id, title, artist, image_path, song_path FROM songs WHERE id IN ($placeholders)");
                    
                    if ($stmt) {
                        $stmt->execute($favoriteSongs); 
                        $result = $stmt->get_result();

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
                            echo '<li class="list-group-item">Нет загруженных песен.</li>'; 
                        } 
                        $stmt->close(); 
                    } else {
                        echo '<li class="list-group-item">Ошибка при подготовке запроса.</li>';
                    }
                } else {
                    echo '<li class="list-group-item" style=\'color: #fff;\'>Нет любимых песен</li>';
                }
                ?>
                </div>
            </div>
        </div>
                   
    <?php else: ?>
       <p>Пользователь не найден.</p>   
</div>
    <?php endif; ?>
</div>


</body>
</html>

<?php

$conn->close();
?>