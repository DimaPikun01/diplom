<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Акт выполненных работ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
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
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .invoice-info {
            margin-bottom: 20px;
        }
        .invoice-info p {
            margin: 5px 0;
        }
        .signature {
            margin-top: 50px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
    <h2>Акт выполненных работ</h2>

    <?php
    include 'includes/db.php';

    // Получаем order_id из GET параметров
    if (isset($_GET['order_id'])) {
        $order_id = intval($_GET['order_id']);

        // Запрос для получения информации о заказе из таблицы orders
        $sqlOrder = "SELECT * FROM orders WHERE id = $order_id";
        $resultOrder = $conn->query($sqlOrder);

        // Проверяем, найден ли заказ с указанным ID
        if ($resultOrder && $resultOrder->num_rows > 0) {
            $rowOrder = $resultOrder->fetch_assoc();

            // Получаем тип устройства из таблицы device_types
            $device_type_id = $rowOrder['device_type'];
            $sqlDeviceType = "SELECT type_name FROM device_types WHERE id = $device_type_id";
            $resultDeviceType = $conn->query($sqlDeviceType);
            $device_type_name = ($resultDeviceType && $resultDeviceType->num_rows > 0) ? $resultDeviceType->fetch_assoc()['type_name'] : '';

            // Получаем модель устройства из таблицы device_models
            $device_model_id = $rowOrder['device_model'];
            $sqlDeviceModel = "SELECT model_name FROM device_models WHERE id = $device_model_id";
            $resultDeviceModel = $conn->query($sqlDeviceModel);
            $device_model_name = ($resultDeviceModel && $resultDeviceModel->num_rows > 0) ? $resultDeviceModel->fetch_assoc()['model_name'] : '';

            // Отображаем информацию о заказе
            echo "<div class='invoice-info'>";
            echo "<p><strong>ID заказа:</strong> {$rowOrder['id']}</p>";
            echo "<p><strong>Дата создания:</strong> {$rowOrder['creation_date']}</p>";
            echo "<p><strong>Клиент:</strong> {$rowOrder['client_first_name']} {$rowOrder['client_last_name']}</p>";
            echo "<p><strong>Причина обращения:</strong> {$rowOrder['reason']}</p>";
            echo "<p><strong>Тип устройства:</strong> {$device_type_name}</p>";
            echo "<p><strong>Модель устройства:</strong> {$device_model_name}</p>";
            echo "</div>";

            // Получаем информацию о завершенном заказе из таблицы finish_orders
            $sqlFinish = "SELECT * FROM finish_orders WHERE order_id = $order_id";
            $resultFinish = $conn->query($sqlFinish);

            // Проверяем, найдена ли информация о завершенном заказе
            if ($resultFinish && $resultFinish->num_rows > 0) {
                $rowFinish = $resultFinish->fetch_assoc();

                // Получаем названия типа ремонта, типа услуги и срока гарантии
                $repair_type_name = $rowFinish['repair_type'];
                $service_type_name = $rowFinish['service_type'];
                $warranty_term_name = $rowFinish['warranty_term'];

                // Отображаем информацию о завершенном заказе
                echo "<div class='invoice-info'>";
                echo "<h3>Информация о завершенном заказе</h3>";
                echo "<p><strong>ID завершенного заказа:</strong> {$rowFinish['id']}</p>";
                echo "<p><strong>Тип ремонта:</strong> {$repair_type_name}</p>";
                echo "<p><strong>Тип услуги:</strong> {$service_type_name}</p>";
                echo "<p><strong>Срок гарантии:</strong> {$warranty_term_name}</p>";
                echo "</div>";

                // Добавляем место для подписи мастера
                echo "<div class='signature'>";
                echo "<p>Мастер: ______________________<strong></strong> {$rowOrder['technician']}</p><p>Клиент: ______________________<strong></strong> {$rowOrder['client_first_name']} {$rowOrder['client_last_name']}</p>"; // Здесь место для подписи
                echo "</div>";

            } else {
                echo "<p>Для этого заказа нет данных о завершенном заказе.</p>";
            }

        } else {
            echo "<p>Заказ с указанным ID не найден.</p>";
        }

    } else {
        echo "<p>Не указан ID заказа для печати информации о приемке.</p>";
    }

    $conn->close();
    ?>
</div>
</body>
</html>
