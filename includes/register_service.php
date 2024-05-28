<?php
include '../database/database.php';

// Проверяем, что данные были переданы через POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $serviceName = $_POST['service_name'];
    $address = $_POST['address'];
    $regNumber = $_POST['reg_number'];
    $adminLogin = $_POST['admin_login'];
    $adminPassword = password_hash($_POST['admin_password'], PASSWORD_BCRYPT); // Хешируем пароль
    $dbName = $_POST['db_name'];

    // Проверяем, существует ли сервис с таким же именем
    $stmt = $conn->prepare("SELECT id FROM services WHERE name = ?");
    $stmt->bind_param("s", $serviceName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        header("Location: ../index.php?error=service_exists");
        exit;
    }

    // Проверяем, существует ли база данных с таким же именем
    $checkDbExists = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'");
    if ($checkDbExists->num_rows > 0) {
        header("Location: ../index.php?error=db_exists");
        exit;
    }

    // Создание записи в основной базе данных
    $stmt = $conn->prepare("INSERT INTO services (name, address, reg_number, admin_login, admin_password, db_name) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $serviceName, $address, $regNumber, $adminLogin, $adminPassword, $dbName);

    if ($stmt->execute()) {
        // Создаем новую базу данных для этого сервиса
        $conn->query("CREATE DATABASE `$dbName`");

        // Подключаемся к новой базе данных
        $serviceConn = new mysqli($servername, $username, $password, $dbName);

        // Создаем таблицу "Приемка"
        $serviceConn->query("
            CREATE TABLE Reception (
                id INT AUTO_INCREMENT PRIMARY KEY,
                client_name VARCHAR(100),
                phone_number VARCHAR(20),
                device_type VARCHAR(50),
                model VARCHAR(50),
                status VARCHAR(20),
                issue VARCHAR(255),
                cost DECIMAL(10, 2)
            )
        ");

        // Перенаправляем на страницу входа
        header("Location: ../templates/login_form.php");
        exit;
    } else {
        header("Location: ../index.php?error=registration_failed");
        exit;
    }
}
?>
