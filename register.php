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
  <header class="container-fluid"> 
    <div class="container"> 
        <div class="row"> 
            <div class="col-4"> 
                <h1> 
                    <a href="index.php">MuSeek
                    <img src="images/waves.png" alt="Логотип" class="logo" href="index.php">
                    </a> 
                </h1> 
            </div> 
            <nav class="col-8"> 
                <ul> 
                    <li><a href="index.php">Главная</a></li> 
                    <li><a href="all.php">Вся музыка</a></li> 
                    <li><a href="register.php">Регистрация</a></li> 
                    <li><a href="login.php">Вход</a></li>
                    <?php if (isset($_SESSION['id'])): ?> <!-- Проверка, авторизован ли пользователь -->
                        <li><a href="add.php">Добавить песню</a></li> 
                        <li> 
                            <a href="profile.php"> 
                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30"> 
                                    <path d="M18,19v-2c0.45-0.223,1.737-1.755,1.872-2.952c0.354-0.027,0.91-0.352,1.074-1.635c0.088-0.689-0.262-1.076-0.474-1.198 c0,0,0.528-1.003,0.528-2.214c0-2.428-0.953-4.5-3-4.5c0,0-0.711-1.5-3-1.5c-4.242,0-6,2.721-6,6c0,1.104,0.528,2.214,0.528,2.214 c-0.212,0.122-0.562,0.51-0.474,1.198c0.164,1.283,0.72,1.608,1.074,1.635C10.263,15.245,11.55,16.777,12,17v2c-1,3-9,1-9,8h24 C27,20,19,22,18,19z"></path> 
                                </svg> 
                                Мой профиль 
                            </a> 
                            <ul> 
                                <li><a href="logout.php">Выход</a></li> 
                            </ul> 
                        </li> 
                    <?php endif; ?>
                </ul> 
            </nav> 
        </div> 
    </div> 
</header> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>


<?php 
$servername = "MySQL-8.2"; 
$username = "root"; 
$password = ""; 
$dbname = "myservice"; 




session_start(); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
   
    $conn = new mysqli($servername, $username, $password, $dbname); 

    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    } 

    // Получеаем и очистка данных из формы 
    $username = trim($_POST['username']); 
    $password = $_POST['password']; 
    $email = trim($_POST['email']); 

    // Валидация email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Некорректный адрес электронной почты.";
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); 

    // Проверка, существует ли пользователь или email 
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?"); 
    $stmt->bind_param("ss", $username, $email); 
    $stmt->execute(); 
    $stmt->store_result(); 

    if ($stmt->num_rows > 0) { 
        echo "<div class='container'>";
        echo "<span style='color: #fff; font-size: 20px;'>Пользователь с таким именем или электронной почтой уже существует!</span>";
        echo "</div>";
    } else { 
        $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)"); 
        $stmt->bind_param("sss", $username, $hashedPassword, $email); 

        if ($stmt->execute()) { 
            echo "<div class='container'>";
            echo "<span style='color: #fff; font-size: 20px;'>Регистрация прошла успешно. Теперь вы можете <a href='login.php' style='color: #44318d';>войти!</a></span>";
            echo "</div>";
        } else { 
            echo "<div class='container'>";
            echo "<span style='color: #fff; font-size: 20px;'>Ошибка при регистрации. Пожалуйста, попробуйте еще раз.</span>";
            echo "</div>";
        } 
        
    } 

    $stmt->close(); 
    $conn->close(); 
} 
?> 
<div class="container mt-5">
    <h2>Регистрация</h2>
    <form method="POST" action=""> 
        <div class="mb-4">
            <input class="form-control form-input" type="text" name="username" placeholder="Имя пользователя" required> 
        </div>
        <div class="mb-4">
            <input class="form-control form-input" type="email" name="email" placeholder="Электронная почта" required> 
        </div>    
        <div class="mb-4">    
            <input class="form-control form-input" type="password" name="password" placeholder="Пароль" required>
        </div>     
            <button type="submit" class="btn btn-primary">Зарегистрироваться</button> 
    </form>   
</div>
