<?php
session_start();

if (!isset($_SESSION['service_id'])) {
    http_response_code(401);
    exit("Unauthorized");
}

include '../database/database.php';

$service_id = $_SESSION['service_id'] ?? null;

$stmt = $conn->prepare("SELECT db_name FROM services WHERE id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$db_name_result = $stmt->get_result();

if ($db_name_result->num_rows === 0) {
    http_response_code(404);
    exit("Service data not found.");
}

$db_name_row = $db_name_result->fetch_assoc();
$db_name = $db_name_row['db_name'];

$service_conn = new mysqli($servername, $username, $password, $db_name);

if ($service_conn->connect_error) {
    http_response_code(500);
    exit("Connection error: " . $service_conn->connect_error);
}

// Получаем данные из запроса
$recordId = $_POST['record_id'] ?? null;
$newStatus = $_POST['new_status'] ?? null;

if ($recordId === null || $newStatus === null) {
    http_response_code(400);
    exit("Missing parameters");
}

// Подготавливаем запрос для обновления статуса
$stmt = $service_conn->prepare("UPDATE Reception SET status = ? WHERE id = ?");
$stmt->bind_param("si", $newStatus, $recordId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    http_response_code(200);
    exit("Status updated successfully");
} else {
    http_response_code(500);
    exit("Failed to update status");
}
?>
