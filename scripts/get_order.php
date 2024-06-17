<?php
// scripts/get_order.php

include '../includes/db.php';

if (isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];

    // Здесь вам нужно выполнить SQL-запрос для получения данных заказа по $orderId
    $sql = "SELECT * FROM orders WHERE id = $orderId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Формируем HTML для редактирования данных заказа
        echo "
            <div class='form-row'>
                <div class='form-group col-md-6'>
                    <label for='edit_client_first_name'>Имя клиента</label>
                    <input type='text' class='form-control' id='edit_client_first_name' name='edit_client_first_name' value='{$row['client_first_name']}' required>
                </div>
                <div class='form-group col-md-6'>
                    <label for='edit_client_last_name'>Фамилия клиента</label>
                    <input type='text' class='form-control' id='edit_client_last_name' name='edit_client_last_name' value='{$row['client_last_name']}' required>
                </div>
            </div>
            <div class='form-group'>
                <label for='edit_client_phone'>Телефон клиента</label>
                <input type='text' class='form-control' id='edit_client_phone' name='edit_client_phone' value='{$row['client_phone']}' required>
            </div>
            <div class='form-group'>
                <label for='edit_manager'>Менеджер</label>
                <input type='text' class='form-control' id='edit_manager' name='edit_manager' value='{$row['manager']}' required>
            </div>
            <div class='form-group'>
                <label for='edit_technician'>Мастер</label>
                <input type='text' class='form-control' id='edit_technician' name='edit_technician' value='{$row['technician']}' required>
            </div>
            <div class='form-group'>
                <label for='edit_cost'>Стоимость</label>
                <input type='number' class='form-control' id='edit_cost' name='edit_cost' value='{$row['cost']}' required>
            </div>
            <div class='form-group'>
                <label for='edit_reason'>Причина обращения</label>
                <input type='text' class='form-control' id='edit_reason' name='edit_reason' value='{$row['reason']}' required>
            </div>
            <div class='form-group'>
                <label for='edit_imei_sn'>IMEI/SN</label>
                <input type='text' class='form-control' id='edit_imei_sn' name='edit_imei_sn' value='{$row['imei_sn']}' required>
            </div>
            <div class='form-group'>
                <label for='edit_appearance'>Внешний вид</label>
                <input type='text' class='form-control' id='edit_appearance' name='edit_appearance' value='{$row['appearance']}' required>
            </div>
            <div class='form-group'>
                <label for='edit_device_type'>Тип устройства</label>
                <select class='form-control' id='edit_device_type' name='edit_device_type' required>
                    <option value=''>Выберите тип устройства</option>";

        $sqlDeviceTypes = "SELECT id, type_name FROM device_types";
        $resultDeviceTypes = $conn->query($sqlDeviceTypes);
        if ($resultDeviceTypes->num_rows > 0) {
            while ($rowDeviceType = $resultDeviceTypes->fetch_assoc()) {
                $selected = ($rowDeviceType['id'] == $row['device_type']) ? 'selected' : '';
                echo "<option value='{$rowDeviceType['id']}' $selected>{$rowDeviceType['type_name']}</option>";
            }
        }

        echo "</select>
            </div>
            <div class='form-group'>
                <label for='edit_device_model'>Модель устройства</label>
                <select class='form-control' id='edit_device_model' name='edit_device_model' required>
                    <option value=''>Выберите модель устройства</option>";

        $sqlDeviceModels = "SELECT id, model_name FROM device_models";
        $resultDeviceModels = $conn->query($sqlDeviceModels);
        if ($resultDeviceModels->num_rows > 0) {
            while ($rowDeviceModel = $resultDeviceModels->fetch_assoc()) {
                $selected = ($rowDeviceModel['id'] == $row['device_model']) ? 'selected' : '';
                echo "<option value='{$rowDeviceModel['id']}' $selected>{$rowDeviceModel['model_name']}</option>";
            }
        }

        echo "</select>
            </div>";

        // Скрытое поле с ID заказа для передачи в обработчик редактирования
        echo "<input type='hidden' name='order_id' value='{$row['id']}'>";

        echo "<button type='submit' class='btn btn-primary'>Сохранить изменения</button>";

    } else {
        echo "Данные заказа не найдены.";
    }
} else {
    echo "Ошибка: Не указан ID заказа для загрузки данных.";
}
?>
