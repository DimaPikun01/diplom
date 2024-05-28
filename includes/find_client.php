<?php
session_start(); // Начало сессии

// Проверка, установлен ли ID сервиса в сессии
if (!isset($_SESSION['service_id'])) {
    http_response_code(403);
    die("Unauthorized");
}

// Подключение к основной базе данных
include '../database/database.php';

// Получаем ID сервиса из сессии
$service_id = $_SESSION['service_id'];

// Получаем название базы данных для текущего сервиса
$stmt = $conn->prepare("SELECT db_name FROM services WHERE id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();
$service = $result->fetch_assoc();
$db_name = $service['db_name'];

// Подключение к базе данных сервиса
$service_conn = new mysqli($servername, $username, $password, $db_name);

if ($service_conn->connect_error) {
    die("Ошибка подключения к базе данных сервиса: " . $service_conn->connect_error);
}

// Получаем ФИО из GET-запроса
$client_name = $_GET['client_name'];

// Поиск последней записи клиента по ФИО
$stmt = $service_conn->prepare("SELECT * FROM Reception WHERE client_name = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("s", $client_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $client = $result->fetch_assoc();
    // Возвращаем данные в формате JSON
    echo json_encode($client);
} else {
    echo json_encode([]);
}
?>
