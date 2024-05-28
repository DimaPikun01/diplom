<?php
session_start(); // Начало сессии

// Проверка, что пользователь авторизован
if (!isset($_SESSION['service_id'])) {
    http_response_code(401);
    exit("Unauthorized");
}

// Подключение к основной базе данных
include '../database/database.php';

// Получение данных из основной таблицы "Reception"
$sql = "SELECT * FROM Reception";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows); // Отправка данных в формате JSON
} else {
    http_response_code(404);
    exit("No data found.");
}
?>
