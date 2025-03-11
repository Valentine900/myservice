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
if (!isset($_SESSION['id']) || $_SESSION['admin'] != 1) {
    echo '<h1>Вы не авторизовались или не являетесь админом!</h1>';
    exit(); 
}
?>

<?php include 'header.php';?>

<?php 
$servername = "MySQL-8.2"; 
$username = "root"; 
$password = ""; 
$dbname = "myservice"; 

$conn = new mysqli($servername, $username, $password, $dbname); 

if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}

// Назначение администратора
if (isset($_GET['assign_id'])) {
    $userId = intval($_GET['assign_id']);
    $query = "UPDATE users SET admin = 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        echo "Пользователь назначен администратором.";
    } else {
        echo "Ошибка при назначении администратора.";
    }
    $stmt->close();
}

// Разжалование администратора
if (isset($_GET['revoke_id'])) {
    $userId = intval($_GET['revoke_id']);
    $query = "UPDATE users SET admin = 0 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        echo "Администратор разжалован.";
    } else {
        echo "Ошибка при разжаловании администратора.";
    }
    $stmt->close();
}

// Удаление пользователя
if (isset($_GET['delete_id'])) {
    $userId = intval($_GET['delete_id']);
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        echo "Пользователь удален.";
    } else {
        echo "Ошибка при удалении пользователя.";
    }
    $stmt->close();
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

// Получаем все песни
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

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список пользователей и песен</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mt-5 mb-4">Список пользователей</h1>
    <table class="table table-bordered table-responsive">
        <thead>
            <tr>
                <th>ID</th>
                <th  class="text-truncate" style="max-width: 80px;">Имя пользователя</th>
                <th>Email</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td class="text-truncate" style="max-width: 50px;"><?php echo htmlspecialchars($user['username']); ?></td>
                    <td class="text-truncate" style="max-width: 50px;"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="text-truncate" style="max-width: 50px;"><?php echo $user['admin'] ? 'Администратор' : 'Пользователь'; ?></td>
                    <td>
                        <?php if (!$user['admin']): ?>
                            <a href="?assign_id=<?php echo $user['id']; ?>" class="btn btn-success btn-sm">Назначить администратором</a>
                        <?php else: ?>
                            <a href="?revoke_id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm" onclick="return confirm('Вы уверены, что хотите разжаловать этого пользователя?');">Разжаловать</a>
                        <?php endif; ?>
                        <a href="?delete_id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить этого пользователя?');">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h1 class="mt-5 mb-4">Список песен</h1>
    <table class="table table-bordered table-responsive">
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
                        <a href="edit_song.php?id=<?php echo $song['id']; ?>" class="btn btn-warning btn-sm">Редактировать</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
