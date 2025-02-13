
<?php  
    session_start(); // Переместить сюда для доступа к сессии в заголовке

    $servername = "MySQL-8.2";  
    $username = "root";  
    $password = "";  
    $dbname = "myservice";  

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
        // Подключение к базе данных  
        $conn = new mysqli($servername, $username, $password, $dbname);  

        // Проверка соединения  
        if ($conn->connect_error) {  
            die("Connection failed: " . $conn->connect_error);  
        }  

        // Получение и очистка данных из формы  
        $username = trim($_POST['username']);  
        $password = $_POST['password'];  

        // Подготовленный запрос для предотвращения SQL-инъекций  
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
                // Можно перенаправить пользователя на защищенную страницу 
                // header("Location: protected_page.php"); 
                // exit(); 
            } else {  
                echo "<div class='alert alert-danger'>Неверное имя пользователя или пароль.</div>";  
            }  
        } else {  
            echo "<div class='alert alert-danger'>Неверное имя пользователя или пароль.</div>";  
        }  

        $stmt->close();  
        $conn->close();  
    }  
    ?>  
