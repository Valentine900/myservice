<?php 
$message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "MySQL-8.2"; 
    $username = "root"; 
    $password = ""; 
    $dbname = "myservice"; 
    $conn = new mysqli($servername, $username, $password, $dbname); 

    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    }

    // Получение данных из формы 
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $messageContent = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

    // Проверка на пустые поля
    if (empty($email) || empty($messageContent)) {
        $message = "Пожалуйста, заполните все поля.";
    } else {
        $stmt = $conn->prepare("INSERT INTO feedback (email, message) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $messageContent);

        if ($stmt->execute()) {
            $message = "Сообщение успешно отправлено!";
        } else {
            $message = "Ошибка: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>

