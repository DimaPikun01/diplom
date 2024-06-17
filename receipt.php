<?php
include 'includes/db.php'; // Подключение к базе данных

// Получаем order_id из GET параметров
if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // Запрос для получения информации о заказе из таблицы orders
    $sqlOrder = "SELECT o.*, dt.type_name AS device_type_name, dm.model_name AS device_model_name 
                 FROM orders o 
                 LEFT JOIN device_types dt ON o.device_type = dt.id
                 LEFT JOIN device_models dm ON o.device_model = dm.id
                 WHERE o.id = $order_id";
    $resultOrder = $conn->query($sqlOrder);

    // Проверяем, найден ли заказ с указанным ID
    if ($resultOrder && $resultOrder->num_rows > 0) {
        $rowOrder = $resultOrder->fetch_assoc();

        // Отображаем информацию о заказе
        echo <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Квитанция о приемке заказа</title>
    <!-- Подключение стилей для красивого оформления -->
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }
        .signature {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Квитанция о приемке заказа</h2>
        <p><strong>ID заказа:</strong> {$rowOrder['id']}</p>
        <p><strong>Дата создания:</strong> {$rowOrder['creation_date']}</p>
        <p><strong>Клиент:</strong> {$rowOrder['client_first_name']} {$rowOrder['client_last_name']}</p>
        <p><strong>Причина обращения:</strong> {$rowOrder['reason']}</p>
        <p><strong>Статус:</strong> {$rowOrder['status']}</p>
        <p><strong>Менеджер:</strong> {$rowOrder['manager']}</p>
        <p><strong>Стоимость:</strong> {$rowOrder['cost']}</p>
        <p><strong>IMEI/SN:</strong> {$rowOrder['imei_sn']}</p>
        <p><strong>Внешний вид:</strong> {$rowOrder['appearance']}</p>
        <p><strong>Тип устройства:</strong> {$rowOrder['device_type_name']}</p>
        <p><strong>Модель устройства:</strong> {$rowOrder['device_model_name']}</p>
        <div class="signature">
            <p>_________________________{$rowOrder['manager']}</p>
            <p>Подпись менеджера</p>
            <p>_________________________{$rowOrder['client_first_name']} {$rowOrder['client_last_name']}</p>
            <p>Подпись клиента</p>
        </div>
    </div>
</body>
</html>
HTML;

    } else {
        echo "<p>Заказ с указанным ID не найден.</p>";
    }

} else {
    echo "<p>Не указан ID заказа для печати информации о приемке.</p>";
}

$conn->close(); // Закрытие соединения с базой данных
?>
