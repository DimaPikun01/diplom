<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $warrantyTerm = $_POST['warranty_term'];
    $warrantyMonths = $_POST['warranty_months'];

    $sql = "INSERT INTO warranty_terms (term_name, months) VALUES ('$warrantyTerm', '$warrantyMonths')";

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
