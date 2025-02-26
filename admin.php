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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>

<?php 
session_start(); 
include 'enter.php'; 

// Проверяем, есть ли активная сессия и является ли пользователь администратором
if (!isset($_SESSION['id']) || $_SESSION['username'] !== 'Admin') {
    echo '<h1>Вы не авторизовались или не являетесь админом!</h1>';
    exit(); 
}
?>

<header class="container-fluid"> 
    <div class="container">
        <div class="row">
            <div class="col-4">
                <h1>
                    <a href="index.php" style='text-decoration: none;'>MuSeek
                    <img src="images/waves.png" alt="Логотип" class="logo" href="index.php">
                    </a> 
                </h1>
            </div>
            <nav class="col-8">
                <ul>
                    <li><a href="index.php">Главная</a></li>
                    <li><a href="all.php">Вся музыка</a></li>
                    <li><a href="add.php">Добавить песню</a></li>
                    <li>
                    <a href="<?php echo ($_SESSION['username'] === 'Admin') ? 'admin.php' : 'profile.php'; ?>">  
                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30"> 
                                    <path d="M18,19v-2c0.45-0.223,1.737-1.755,1.872-2.952c0.354-0.027,0.91-0.352,1.074-1.635c0.088-0.689-0.262-1.076-0.474-1.198 c0,0,0.528-1.003,0.528-2.214c0-2.428-0.953-4.5-3-4.5c0,0-0.711-1.5-3-1.5c-4.242,0-6,2.721-6,6c0,1.104,0.528,2.214,0.528,2.214 c-0.212,0.122-0.562,0.51-0.474,1.198c0.164,1.283,0.72,1.608,1.074,1.635C10.263,15.245,11.55,16.777,12,17v2c-1,3-9,1-9,8h24 C27,20,19,22,18,19z"></path> 
                                    </svg> 
                                <?php echo ($_SESSION['username'] === 'Admin') ? 'Админ панель' : 'Мой профиль'; ?>
                            </a> 
                        <ul>
                            <li><a href="logout.php">Выход</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>

<?php 
$servername = "MySQL-8.2"; 
$username = "root"; 
$password = ""; 
$dbname = "myservice"; 

$conn = new mysqli($servername, $username, $password, $dbname); 

if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}

// Обработка удаления пользователя
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM users WHERE id = ?";
    
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Ошибка при удалении пользователя: " . $stmt->error;
    }
}

// Обработка удаления песни
if (isset($_GET['delete_song_id'])) {
    $delete_song_id = intval($_GET['delete_song_id']);
    
    // Получаем пути к файлам перед удалением записи, чтобы в дальнейшем удалить их из папки uploads
    $get_file_query = "SELECT song_path, image_path FROM songs WHERE id = ?";
    $stmt = $conn->prepare($get_file_query);
    $stmt->bind_param("i", $delete_song_id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $song = $result->fetch_assoc();
            $song_path = $song['song_path'];
            $image_path = $song['image_path'];
            
            // Удаляем запись о песне из базы данных
            $delete_song_query = "DELETE FROM songs WHERE id = ?";
            $stmt_delete = $conn->prepare($delete_song_query);
            $stmt_delete->bind_param("i", $delete_song_id);
            
            if ($stmt_delete->execute()) {
                // Удаляем файл песни из папки uploads
                if (file_exists($song_path)) {
                    unlink($song_path); // Удаляем файл песни
                }
                
                // Удаляем изображение из папки uploads, если оно существует
                if (!empty($image_path) && file_exists($image_path)) {
                    unlink($image_path);
                }

                header("Location: admin.php"); 
                exit();
            } else {
                echo "Ошибка при удалении песни: " . $stmt_delete->error;
            }
        } else {
            echo "Песня не найдена.";
        }
    } else {
        echo "Ошибка при получении путей к файлам: " . $stmt->error;
    }
}


// Получаем всех пользователей
$sql_users = "SELECT * FROM users";
$result_users = $conn->query($sql_users);

$users = [];
if ($result_users->num_rows > 0) {
    while($row = $result_users->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    echo "Нет пользователей.";
}

// Получаем всех песен
$sql_songs = "SELECT * FROM songs";
$result_songs = $conn->query($sql_songs);

$songs = [];
if ($result_songs->num_rows > 0) {
    while($row = $result_songs->fetch_assoc()) {
        $songs[] = $row;
    }
} else {
    echo "Нет песен.";
}

$conn->close();
?>

<div class="container mt-5">
    <h1>Список пользователей</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Имя пользователя</th>
                <th>Email</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <a href="?delete_id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить этого пользователя?');">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h1 class="mt-5">Список песен</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название песни</th>
                <th>Исполнитель</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($songs as $song): ?>
                <tr>
                    <td><?php echo htmlspecialchars($song['id']); ?></td>
                    <td><?php echo htmlspecialchars($song['title']); ?></td>
                    <td><?php echo htmlspecialchars($song['artist']); ?></td>
                    <td>
                        <a href="?delete_song_id=<?php echo $song['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить эту песню?');">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
