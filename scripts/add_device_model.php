<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['device_type_id']) && isset($_POST['model_name'])) {
    $deviceTypeId = $_POST['device_type_id'];
    $modelName = $_POST['model_name'];

    // Подготовка запроса для добавления модели устройства
    $sql = "INSERT INTO device_models (device_type_id, model_name) VALUES ($deviceTypeId, '$modelName')";

    if ($conn->query($sql) === TRUE) {
        echo "Модель устройства успешно добавлена";
    } else {
        echo "Ошибка при добавлении модели устройства: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Некорректный запрос";
}
?>
