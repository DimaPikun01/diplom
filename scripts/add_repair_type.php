<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $repairType = $_POST['repair_type'];
    $repairDescription = $_POST['repair_description'];

    $sql = "INSERT INTO repair_types (type_name, description) VALUES ('$repairType', '$repairDescription')";

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
