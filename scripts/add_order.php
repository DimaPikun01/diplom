<?php
include '../includes/db.php';

// Получаем данные из формы
$client_first_name = $_POST['client_first_name'];
$client_last_name = $_POST['client_last_name'];
$client_phone = $_POST['client_phone'];
$manager = $_POST['manager'];
$technician = $_POST['technician'];
$cost = $_POST['cost'];
$reason = $_POST['reason'];
$imei_sn = $_POST['imei_sn'];
$appearance = $_POST['appearance'];
$device_type = $_POST['device_type'];
$device_model = $_POST['device_model'];

// Подготовка запроса
$sql = "INSERT INTO orders (client_first_name, client_last_name, client_phone, manager, technician, cost, reason, imei_sn, appearance, device_type, device_model) 
        VALUES ('$client_first_name', '$client_last_name', '$client_phone', '$manager', '$technician', '$cost', '$reason', '$imei_sn', '$appearance', '$device_type', '$device_model')";

if ($conn->query($sql) === TRUE) {
    header("Location: ../index.php");
} else {
    echo "Ошибка: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
