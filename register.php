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
<?php include 'header.php';?>
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
