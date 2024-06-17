<?php
// edit_order.php

// Подключение к базе данных
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы редактирования заказа
    $orderId = $_POST['order_id'];
    $clientFirstName = $_POST['edit_client_first_name'];
    $clientLastName = $_POST['edit_client_last_name'];
    $clientPhone = $_POST['edit_client_phone'];
    $manager = $_POST['edit_manager'];
    $technician = $_POST['edit_technician'];
    $cost = $_POST['edit_cost'];
    $reason = $_POST['edit_reason'];
    $imeiSn = $_POST['edit_imei_sn'];
    $appearance = $_POST['edit_appearance'];
    $deviceType = $_POST['edit_device_type'];
    $deviceModel = $_POST['edit_device_model'];

    // Подготовка SQL-запроса для обновления заказа
    $sql = "UPDATE orders 
            SET client_first_name = '$clientFirstName',
                client_last_name = '$clientLastName',
                client_phone = '$clientPhone',
                manager = '$manager',
                technician = '$technician',
                cost = '$cost',
                reason = '$reason',
                imei_sn = '$imeiSn',
                appearance = '$appearance',
                device_type = '$deviceType',
                device_model = '$deviceModel'
            WHERE id = $orderId";

    // Выполнение SQL-запроса
    if ($conn->query($sql) === TRUE) {
        // Редирект на страницу со списком заказов после успешного обновления
        header('Location: ../index.php');
        exit();
    } else {
        echo "Ошибка при обновлении заказа: " . $conn->error;
    }

    // Закрытие соединения с базой данных
    $conn->close();
} else {
    // Если скрипт был вызван не методом POST, выводим ошибку
    echo "Ошибка: Доступ к скрипту разрешен только методом POST.";
}
?>
