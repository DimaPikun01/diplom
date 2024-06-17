<?php
include '../includes/db.php';

// Получаем данные из POST
$order_id = intval($_POST['order_id']);
$repair_type_name = $_POST['repair_type_name'];
$service_type_name = $_POST['service_type_name'];
$warranty_term_name = $_POST['warranty_term_name'];

// Вставляем данные в таблицу finish_orders
$sqlInsert = "INSERT INTO finish_orders (order_id, repair_type, service_type, warranty_term)
              VALUES (?, ?, ?, ?)";
$stmtInsert = $conn->prepare($sqlInsert);
$stmtInsert->bind_param('isss', $order_id, $repair_type_name, $service_type_name, $warranty_term_name);

// Выполняем запрос
if ($stmtInsert->execute()) {
    echo "success";
} else {
    echo "error";
}

$stmtInsert->close();
$conn->close();
?>
