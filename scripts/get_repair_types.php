<?php
// Подключение к базе данных
include '../includes/db.php';

// SQL запрос для выборки типов ремонта
$sql = "SELECT id, type_name FROM repair_types";

// Выполнение запроса
$result = $conn->query($sql);

// Проверка наличия данных
if ($result->num_rows > 0) {
    // Формирование HTML опций
    while($row = $result->fetch_assoc()) {
        echo "<option value='{$row['id']}'>{$row['type_name']}</option>";
    }
} else {
    echo "<option value=''>Нет данных</option>";
}

// Закрытие соединения с базой данных
$conn->close();
?>
