<?php
// Подключение к основной базе данных для получения имени базы данных сервиса
include '../database/database.php';
session_start();

if (isset($_POST['record_id'])) {
    $record_id = intval($_POST['record_id']);

    // Получение ID текущего сервиса из сессии
    $service_id = $_SESSION['service_id'] ?? null;

    // Проверка, что ID сервиса существует
    if (!$service_id) {
        echo "Service ID is not set.";
        exit;
    }

    // Подготовка запроса для получения данных о базе данных текущего сервиса
    $stmt = $conn->prepare("SELECT db_name FROM services WHERE id = ?");
    if (!$stmt) {
        echo "Prepare statement failed: " . $conn->error;
        exit;
    }
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $db_name_result = $stmt->get_result();

    // Проверка наличия данных о базе данных текущего сервиса
    if ($db_name_result->num_rows === 0) {
        echo "Service data not found.";
        exit;
    }

    $db_name_row = $db_name_result->fetch_assoc();
    $db_name = $db_name_row['db_name'];

    // Подключение к базе данных текущего сервиса
    $service_conn = new mysqli($servername, $username, $password, $db_name);

    // Проверка подключения к базе данных текущего сервиса
    if ($service_conn->connect_error) {
        http_response_code(500);
        echo "Connection error: " . $service_conn->connect_error;
        exit;
    }

    // Подготовка запроса для получения данных из таблицы additional
    $stmt = $service_conn->prepare("SELECT * FROM additional WHERE record_id = ?");
    if (!$stmt) {
        echo "Prepare statement failed: " . $service_conn->error;
        exit;
    }
    $stmt->bind_param("i", $record_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Проверка наличия данных в таблице additional
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<p>Описание: " . htmlspecialchars($row['description']) . "</p>";
            echo "<p>Окончательная стоимость: " . htmlspecialchars($row['final_cost']) . "</p>";
            echo "<p>Гарантия: " . htmlspecialchars($row['warranty']) . "</p>";
            echo "<p>Мастер: " . htmlspecialchars($row['technician']) . "</p>";
        }
    } else {
        echo "Нет данных для отображения.";
    }

    $stmt->close();
    $service_conn->close();
} else {
    echo "Invalid request.";
}
?>
