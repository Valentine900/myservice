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
// Проверьте наличие user_id в сессии
if (!isset($_SESSION['id'])) {
    $_SESSION['error_message'] = "Вы не авторизовались"; // Сохраняем сообщение
    header("Location: " . $_SERVER['HTTP_REFERER']); // На предыдущую страницу
    exit();
}

$user_id = $_SESSION['id'];

if (isset($_POST['song_id'])) {
    $song_id = htmlspecialchars($_POST['song_id']);
    $action = isset($_POST['action']) ? htmlspecialchars($_POST['action']) : 'add'; // Добавить или удалить песню

    // Проверка на существование пользователя и получение favorite_song_ids
    $sql = "SELECT favorite_song_ids FROM users WHERE id = '$user_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $favorite_song_ids = $row['favorite_song_ids'];

        $favorite_songs_array = $favorite_song_ids ? explode(',', $favorite_song_ids) : [];

        if ($action === 'add') {
            // Проверяем, не была ли добавлена песня ранее
            if (!in_array($song_id, $favorite_songs_array)) {
                // Добавляем новую песню в массив
                $favorite_songs_array[] = $song_id;

                // Обновляем строку в базе данных
                $new_favorite_song_ids = implode(',', $favorite_songs_array);
                $update_sql = "UPDATE users SET favorite_song_ids = '$new_favorite_song_ids' WHERE id = '$user_id'";

                if ($conn->query($update_sql) === TRUE) {
                    // На предыдущую страницу
                    $previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
                    header("Location: $previous_page");
                    exit();
                } else {
                    die("Error updating record: " . $conn->error);
                }
            } else {
                $_SESSION['error_message'] = "Песня уже добавлена в <<Любимые>>"; // Сохраняем сообщение в сессии
                header("Location: " . $_SERVER['HTTP_REFERER']); // Перенаправляем на предыдущую страницу
                exit();
            }
        } elseif ($action === 'remove') {
            // Проверяем, существует ли песня в избранном
            if (in_array($song_id, $favorite_songs_array)) {
                // Удаляем песню из массива
                $favorite_songs_array = array_diff($favorite_songs_array, [$song_id]);

                // Обновляем строку в базе данных
                $new_favorite_song_ids = implode(',', $favorite_songs_array);
                $update_sql = "UPDATE users SET favorite_song_ids = '$new_favorite_song_ids' WHERE id = '$user_id'";

                if ($conn->query($update_sql) === TRUE) {
                    // Перенаправляем на предыдущую страницу
                    $previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
                    header("Location: $previous_page");
                    exit();
                } else {
                    die("Error updating record: " . $conn->error);
                }
            } else {
                $_SESSION['error_message'] = "Песня не была добавлена в <<Любимые>>"; // Сохраняем сообщение
                header("Location: " . $_SERVER['HTTP_REFERER']); // На предыдущую страницу
                exit();
            }
        } else {
            die("Invalid action.");
        }
    } else {
        die("Пользователь не найден");
    }
} else {
    die("идентификатор песни не указан");
}

$conn->close();