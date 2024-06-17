<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "repair_shop";

// Создание соединения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
