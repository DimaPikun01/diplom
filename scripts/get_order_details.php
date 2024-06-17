<?php
// Подключение к базе данных
include '../includes/db.php';

// Проверка наличия POST параметра order_id
if (isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];

    // Подготовка SQL запроса для получения данных о заказе
    $sql = "SELECT o.id, CONCAT(o.client_first_name, ' ', o.client_last_name) AS client_name, o.reason, dt.type_name, dm.model_name
            FROM orders o
            LEFT JOIN device_types dt ON o.device_type = dt.id
            LEFT JOIN device_models dm ON o.device_model = dm.id
            WHERE o.id = ?";
    
    // Подготовка выражения SQL
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    
    // Выполнение запроса
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $orderData = $result->fetch_assoc();
            // Возвращаем успешный ответ с данными о заказе в формате JSON
            echo json_encode(array('status' => 'success', 'data' => $orderData));
        } else {
            // Если заказ не найден, возвращаем ошибку
            echo json_encode(array('status' => 'error', 'message' => 'Заказ не найден'));
        }
    } else {
        // Если произошла ошибка выполнения запроса
        echo json_encode(array('status' => 'error', 'message' => 'Ошибка при выполнении запроса к базе данных'));
    }

    // Закрываем подготовленное выражение
    $stmt->close();
} else {
    // Если параметр order_id не был передан, возвращаем ошибку
    echo json_encode(array('status' => 'error', 'message' => 'Не передан обязательный параметр order_id'));
}

// Закрываем соединение с базой данных
$conn->close();
?>
