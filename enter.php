<?php  
session_start(); 
$servername = "MySQL-8.2";  
$db_username = "root";  
$db_password = "";  
$dbname = "myservice";  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
  
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);  

    if ($conn->connect_error) {  
        die("Connection failed: " . $conn->connect_error);  
    }  

    // Проверка на существования полей формы
    if (isset($_POST['username']) && isset($_POST['password'])) {
        // Получение и очистка данных из формы  
        $username = trim($_POST['username']);  
        $password = $_POST['password'];  

        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");  
        $stmt->bind_param("s", $username);  
        $stmt->execute();  
        $stmt->store_result();  

        // Проверка, существует ли пользователь  
        if ($stmt->num_rows > 0) {  
            $stmt->bind_result($id, $hashedPassword);  
            $stmt->fetch();  

            // Проверка пароля  
            if (password_verify($password, $hashedPassword)) {  
                // Успешный вход, установка сессии  
                $_SESSION['id'] = $id;  
                $_SESSION['username'] = $username;

                echo "<div class='alert alert-success'>Добро пожаловать, " . htmlspecialchars($username) . "!</div>";  
                header("Location: index.php");
                exit;

            } else {  
                echo "<div class='alert alert-danger'>Неверное имя пользователя или пароль.</div>";  
            }  
        } else {  
            echo "<div class='alert alert-danger'>Неверное имя пользователя или пароль.</div>";  
        }  

        $stmt->close();  
    } 
    
    $conn->close();  
}  
?>
