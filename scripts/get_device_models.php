<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['device_type_id'])) {
    $deviceTypeId = $_POST['device_type_id'];

    $sql = "SELECT id, model_name FROM device_models WHERE device_type_id = $deviceTypeId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<option value=''>Выберите модель устройства</option>";
        while($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['model_name']}</option>";
        }
    } else {
        echo "<option value=''>Нет доступных моделей</option>";
    }

    $conn->close();
} else {
    echo "<option value=''>Некорректный запрос</option>";
}
?>
