<?php
// Подключение к основной базе данных для получения имени базы данных сервиса
include '../database/database.php';
session_start();

if (isset($_GET['record_id'])) {
    $record_id = intval($_GET['record_id']);

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

    // Получение данных из таблицы Reception
    $stmt = $service_conn->prepare("SELECT * FROM Reception WHERE id = ?");
    if (!$stmt) {
        echo "Prepare statement failed: " . $service_conn->error;
        exit;
    }
    $stmt->bind_param("i", $record_id);
    $stmt->execute();
    $reception_result = $stmt->get_result();

    // Проверка наличия данных в таблице Reception
    if ($reception_result->num_rows === 0) {
        echo "Reception data not found.";
        exit;
    }

    $reception_data = $reception_result->fetch_assoc();

    // Получение данных из таблицы additional
    $stmt = $service_conn->prepare("SELECT * FROM additional WHERE record_id = ?");
    if (!$stmt) {
        echo "Prepare statement failed: " . $service_conn->error;
        exit;
    }
    $stmt->bind_param("i", $record_id);
    $stmt->execute();
    $additional_result = $stmt->get_result();

    // Проверка наличия данных в таблице additional
    if ($additional_result->num_rows === 0) {
        echo "Additional data not found.";
        exit;
    }

    $additional_data = $additional_result->fetch_assoc();

    // Закрытие подключения
    $stmt->close();
    $service_conn->close();
} else {
    echo "Invalid request.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Акт по клиенту</title>
    <style>
        body{
            margin: 0;
            padding: 0;
        }
        .print-button {
            margin-top: 20px;
        }
        h2{
            text-align:center;
            margin:0;
        }
        h4{
            text-align:center;
            margin:0;
        }
        p{
            margin:0;
            font-size: 13px;
        }
        
    </style>
</head>
<body>
    <div class="container">
        <h2>Акт по клиенту</h2>
        <h2>Квитанция №<?php echo htmlspecialchars($reception_data['id']); ?></h2>
        <p>Имя клиента: <?php echo htmlspecialchars($reception_data['client_name']); ?></p>
        <p>Номер телефона: <?php echo htmlspecialchars($reception_data['phone_number']); ?></p>
        <table border="1">
            <tr>
                <td>Тип устройства</td>
                <td>Модель</td>
                <td>Неисправность</td>
                <td>Статус</td>
            </tr>
            <tr>
                <td><?php echo htmlspecialchars($reception_data['device_type']); ?></td>
                <td><?php echo htmlspecialchars($reception_data['model']); ?></td>
                <td><?php echo htmlspecialchars($reception_data['issue']); ?></td>
                <td><?php echo htmlspecialchars($reception_data['status']); ?></td>
            </tr>
        </table>

        <h2>Дополнительная информация</h2>
        <h4>Согласованная с клиентом</h4>
        <p>Описание: <?php echo htmlspecialchars($additional_data['description']); ?></p>
        <p>Окончательная стоимость: <?php echo htmlspecialchars($additional_data['final_cost']); ?></p>
        <p>Гарантия: <?php echo htmlspecialchars($additional_data['warranty']); ?></p>
        <p>Мастер: <?php echo htmlspecialchars($additional_data['technician']); ?></p>
        <p>Подпись: _________________</p>

        <p>Приемочный контроль отремонтированного ОАУ</p>
            <p>1. Требования к выполняемым функциям (п. 4.2 СТБ 1881-2008)
            <br>
            - установление телефонного соединения (набор номера) ________________________
            <br>
            - прием входящего телефонного вызова ________________________
            <br>
            - ведение телефонного разговора ________________________
            <br>
            - выполнение предусмотренных эксплуатационной документацией изготовителя дополнительных функций (при выполнении ремонта по требованию заказчика) ________________________
            </p>
            <p>2. Требования к конструкции (п. 4.3 СТб 1881-2008) ________________________</p>
            <p>3. Требования безопасности (п. 4.5 СТб 1881-2008) ________________________</p>
            <p>Отремонтированный ОАУ соответствует требованиям СТБ 1881-2008. Приемочный контроль провел ____________ мастер</p>
            <p>При нарушении гарантийных пломб на ОАУ, все гарантийные обязательства перед заказчиком утрачивают свою силу.</p>
            <p>Претензий к объему, качеству и срокам оказания услуг не имею, замененные детали получил</p>
            <p>Заказчик:_____________________ <?php echo htmlspecialchars($reception_data['client_name']); ?></p>
            <p>Дата выдачи (ставить в ручную):_____________________</p>
            <p>----------------------------------------------------------------------------------------------------------------------------------</p>

    </div>
    <div class="container">
        <h2>Акт по клиенту</h2>
        <h2>Квитанция №<?php echo htmlspecialchars($reception_data['id']); ?></h2>
        <p>Имя клиента: <?php echo htmlspecialchars($reception_data['client_name']); ?></p>
        <p>Номер телефона: <?php echo htmlspecialchars($reception_data['phone_number']); ?></p>
        <table border="1">
            <tr>
                <td>Тип устройства</td>
                <td>Модель</td>
                <td>Неисправность</td>
                <td>Статус</td>
            </tr>
            <tr>
                <td><?php echo htmlspecialchars($reception_data['device_type']); ?></td>
                <td><?php echo htmlspecialchars($reception_data['model']); ?></td>
                <td><?php echo htmlspecialchars($reception_data['issue']); ?></td>
                <td><?php echo htmlspecialchars($reception_data['status']); ?></td>
            </tr>
        </table>

        <h2>Дополнительная информация</h2>
        <h4>Согласованная с клиентом</h4>
        <p>Описание: <?php echo htmlspecialchars($additional_data['description']); ?></p>
        <p>Окончательная стоимость: <?php echo htmlspecialchars($additional_data['final_cost']); ?></p>
        <p>Гарантия: <?php echo htmlspecialchars($additional_data['warranty']); ?></p>
        <p>Мастер: <?php echo htmlspecialchars($additional_data['technician']); ?></p>
        <p>Подпись: _________________</p>

        <p>Приемочный контроль отремонтированного ОАУ</p>
            <p>1. Требования к выполняемым функциям (п. 4.2 СТБ 1881-2008)
            <br>
            - установление телефонного соединения (набор номера) ________________________
            <br>
            - прием входящего телефонного вызова ________________________
            <br>
            - ведение телефонного разговора ________________________
            <br>
            - выполнение предусмотренных эксплуатационной документацией изготовителя дополнительных функций (при выполнении ремонта по требованию заказчика) ________________________
            </p>
            <p>2. Требования к конструкции (п. 4.3 СТб 1881-2008) ________________________</p>
            <p>3. Требования безопасности (п. 4.5 СТб 1881-2008) ________________________</p>
            <p>Отремонтированный ОАУ соответствует требованиям СТБ 1881-2008. Приемочный контроль провел ____________ мастер</p>
            <p>При нарушении гарантийных пломб на ОАУ, все гарантийные обязательства перед заказчиком утрачивают свою силу.</p>
            <p>Претензий к объему, качеству и срокам оказания услуг не имею, замененные детали получил</p>
            <p>Заказчик:_____________________ <?php echo htmlspecialchars($reception_data['client_name']); ?></p>
            <p>Дата выдачи (ставить в ручную):_____________________</p>
    </div>
    <button class="btn btn-primary print-button" onclick="window.print()">Распечатать</button>
</body>
</html>
