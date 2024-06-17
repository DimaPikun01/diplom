<?php
// Подключение к базе данных
include '../includes/db.php';

// SQL запрос для выборки сроков гарантии
$sql = "SELECT id, term_name FROM warranty_terms";

// Выполнение запроса
$result = $conn->query($sql);

// Проверка наличия данных
if ($result->num_rows > 0) {
    // Формирование HTML опций
    while($row = $result->fetch_assoc()) {
        echo "<option value='{$row['id']}'>{$row['term_name']}</option>";
    }
} else {
    echo "<option value=''>Нет данных</option>";
}

// Закрытие соединения с базой данных
$conn->close();
?>
