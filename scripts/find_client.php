<?php
// scripts/find_client.php

include '../includes/db.php'; // Подключение к базе данных

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['first_name']) && isset($_POST['last_name'])) {
    $firstName = $conn->real_escape_string($_POST['first_name']);
    $lastName = $conn->real_escape_string($_POST['last_name']);

    $sql = "SELECT client_phone, manager, technician, cost, reason FROM orders WHERE client_first_name = '$firstName' AND client_last_name = '$lastName'";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $response = array(
                'status' => 'success',
                'data' => array(
                    'client_phone' => $row['client_phone'],
                    'manager' => $row['manager'],
                    'technician' => $row['technician'],
                    'cost' => $row['cost'],
                    'reason' => $row['reason']
                )
            );
        } else {
            $response = array('status' => 'error', 'message' => 'Клиент не найден в базе данных.');
        }
    } else {
        $response = array('status' => 'error', 'message' => 'Ошибка выполнения запроса к базе данных: ' . $conn->error);
    }

    echo json_encode($response);
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Недостаточно данных для поиска клиента.'));
}
?>
