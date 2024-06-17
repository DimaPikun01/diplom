<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['new_status'];

    $sql = "UPDATE orders SET status = '$newStatus' WHERE id = $orderId";

    if ($conn->query($sql) === TRUE) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}

$conn->close();
?>
