<?php
session_start(); // Начало сессии

// Проверка, установлен ли ID сервиса в сессии
if (!isset($_SESSION['service_id'])) {
    http_response_code(403);
    exit("Unauthorized");
}

// Подключение к основной базе данных
include '../database/database.php';

// Получение информации о сервисе
$service_id = $_SESSION['service_id'] ?? null;
if (!$service_id) {
    http_response_code(400);
    exit("Service ID not found in session.");
}

// Подключение к базе данных сервиса
$stmt = $conn->prepare("SELECT db_name FROM services WHERE id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$db_name = $stmt->get_result()->fetch_assoc()['db_name']; // Убедитесь, что значение не пустое

$service_conn = new mysqli($servername, $username, $password, $db_name);

// Проверка ошибок подключения
if ($service_conn->connect_error) {
    http_response_code(500);
    exit("Connection error: " . $service_conn->connect_error);
}

// Получение всех типов устройств
$deviceTypesResult = $service_conn->query("SELECT * FROM DeviceType");

// Получение всех моделей
$deviceModelsResult = $service_conn->query("
    SELECT DeviceType.device_type, DeviceModel.model_name
    FROM DeviceModel
    JOIN DeviceType ON DeviceModel.device_type_id = DeviceType.id
");

// Подготовка данных для передачи в dashboard.php
$data = [
    'device_types' => [],
    'device_models' => []
];

// Сбор всех типов устройств
while ($row = $deviceTypesResult->fetch_assoc()) {
    $data['device_types'][] = $row['device_type'];
}

// Сбор всех моделей с их типами
while ($row = $deviceModelsResult->fetch_assoc()) {
    $data['device_models'][] = [
        'device_type' => $row['device_type'],
        'model_name' => $row['model_name']
    ];
}

// Передача данных в формате JSON
header('Content-Type: application/json');
echo json_encode($data);
