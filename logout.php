<?php
session_start();
session_destroy(); // Уничтожаем сессию
header("Location: login.php"); // Перенаправление на страницу входа
exit();
?>