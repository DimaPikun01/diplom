<?php
session_start(); // Начало сессии

// Проверка авторизации
if (!isset($_SESSION['service_id'])) {
    http_response_code(403);
    exit("Unauthorized");
}

// Подключение к основной базе данных
include '../database/database.php';

// Подключение к базе данных сервиса
$service_id = $_SESSION['service_id'] ?? null;
$stmt = $conn->prepare("SELECT db_name FROM services WHERE id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$db_name = $stmt->get_result()->fetch_assoc()['db_name'];

$service_conn = new mysqli($servername, $username, $password, $db_name);

if ($service_conn->connect_error) {
    http_response_code(500);
    exit("Connection error: " + $service_conn->connect_error);
}

// Получение параметра "id"
$id = $_GET['id'] ?? null;
if ($id === null) {
    http_response_code(400);
    exit("ID not provided.");
}

// Получение информации из таблицы "Reception"
$stmt = $service_conn->prepare("SELECT * FROM Reception WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$record = $stmt->get_result()->fetch_assoc();

if (!$record) {
    http_response_code(404);
    exit("Record not found.");
}

// Возвращение информации для отображения во всплывающем окне
echo "<p>Имя клиента: " . htmlspecialchars($record['client_name']) . "</p>";
echo "<p>Номер телефона: " . htmlspecialchars($record['phone_number']) . "</p>";
echo "<p>Неисправность: " . htmlspecialchars($record['issue']) . "</p>";

// Поля для заметок мастера, услуги, стоимости и выбора мастера
?>
<form>
    <label>Заметки мастера:</label>
    <textarea name="notes"></textarea><br>

    <label>Название выполненной услуги:</label>
    <input type="text" name="service_name"><br>

    <label>Согласованная стоимость:</label>
    <input type="number" name="agreed_cost"><br>

    <label>Гарантия:</label>
    <input type="text" name="warranty"><br>

    <label>Мастер, который выполнял ремонт:</label>
    <select name="technician">
        <option>Мастер 1</option>
        <option>Мастер 2</option>
    </select><br>
</form>
