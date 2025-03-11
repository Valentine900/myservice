<?php
$servername = "MySQL-8.2"; 
$username = "root";
$password = "";
$dbname = "myservice";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Проверяем, был ли отправлен запрос и существует ли ключ "search-term"
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search-term'])) {
    $searchTerm = trim($_POST['search-term']); // Получаем значение из формы


    // Проверяем, что поле не пустое
    if (!empty($searchTerm)) {
        $stmt = $conn->prepare("
            SELECT * FROM songs 
            WHERE LOWER(title) LIKE LOWER(?) 
            OR LOWER(artist) LIKE LOWER(?) 
            OR LOWER(genre) LIKE LOWER(?) 
            OR LOWER(lyrics) LIKE LOWER(?)
        ");
        
        // Создаем параметры для запроса
        $likeTerm = "%" . $searchTerm . "%";
        $stmt->bind_param("ssss", $likeTerm, $likeTerm, $likeTerm, $likeTerm);

        $stmt->execute();
        
        // Результаты
        $result = $stmt->get_result();
        $results = $result->fetch_all(MYSQLI_ASSOC);

        // Проверка наличия результатов
        if ($results) {
            echo "<h3 style='font-size: 20px;'>Результаты поиска для: " . htmlspecialchars($searchTerm) . "</h3>";
            echo "<ul>";
            foreach ($results as $row) {
                echo "<li style='color: #fff; font-size: 13px;margin-right: 10px;';><strong>Название:</strong> <a href='song.php?id=" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['title']) . "</a>" . 
                     ", <strong>Исполнитель:</strong> " . htmlspecialchars($row['artist']) . 
                     ", <strong>Жанр:</strong> " . htmlspecialchars($row['genre']) . 
                     ", <strong>Дата публикации:</strong> " . htmlspecialchars($row['release_date']) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: #fff';>Ничего не найдено для: " . htmlspecialchars($searchTerm) . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color: #fff';>Пожалуйста, введите поисковый запрос.</p>";
    }
}

$conn->close();
?>

