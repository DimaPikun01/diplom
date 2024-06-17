<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['device_type'])) {
    $deviceType = $_POST['device_type'];

    // Подготовка запроса для добавления типа устройства
    $sql = "INSERT INTO device_types (type_name) VALUES ('$deviceType')";

    if ($conn->query($sql) === TRUE) {
        echo "Тип устройства успешно добавлен";
    } else {
        echo "Ошибка при добавлении типа устройства: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Некорректный запрос";
}
?>
