<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceType = $_POST['service_type'];
    $serviceDescription = $_POST['service_description'];

    $sql = "INSERT INTO service_types (service_name, description) VALUES ('$serviceType', '$serviceDescription')";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../settings.php");
        exit();
    } else {
        echo "Ошибка: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Неверный метод запроса";
}

$conn->close();
?>
