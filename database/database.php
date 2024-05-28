<?php
$servername = "127.0.0.1"; // Имя хоста базы данных
$username = "root"; // Имя пользователя базы данных
$password = ""; // Пароль базы данных
$globalDB = "repair_service"; // Основная база данных, хранящая информацию о сервисах

// Подключение к базе данных
$conn = new mysqli($servername, $username, $password, $globalDB);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}
?>
