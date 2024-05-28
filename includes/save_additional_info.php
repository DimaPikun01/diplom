<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверка наличия всех необходимых данных
    if (isset($_POST['record_id']) && isset($_POST['description']) && isset($_POST['final_cost']) && isset($_POST['warranty']) && isset($_POST['technician'])) {
        // Получение данных из POST запроса
        $recordId = $_POST['record_id'];
        $description = $_POST['description'];
        $finalCost = $_POST['final_cost'];
        $warranty = $_POST['warranty'];
        $technician = $_POST['technician'];

        // Подключение к базе данных
        include '../database/database.php';
        $service_id = $_SESSION['service_id'] ?? null;
        $stmt = $conn->prepare("SELECT db_name FROM services WHERE id = ?");
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        $db_name_result = $stmt->get_result();

        // Проверка наличия данных о базе данных текущего сервиса
        if ($db_name_result->num_rows === 0) {
            http_response_code(500);
            exit("Service data not found.");
        }

        $db_name_row = $db_name_result->fetch_assoc();
        $db_name = $db_name_row['db_name'];
        $service_conn = new mysqli($servername, $username, $password, $db_name);

        // Проверка подключения к базе данных текущего сервиса
        if ($service_conn->connect_error) {
            http_response_code(500);
            exit("Connection error: " . $service_conn->connect_error);
        }

        // Создание таблицы additional, если она не существует
        $create_table_query = "CREATE TABLE IF NOT EXISTS additional (
            id INT AUTO_INCREMENT PRIMARY KEY,
            record_id INT,
            description TEXT,
            final_cost DECIMAL(10, 2),
            warranty VARCHAR(100),
            technician VARCHAR(100),
            FOREIGN KEY (record_id) REFERENCES Reception(id)
        )";

        if ($service_conn->query($create_table_query) === FALSE) {
            http_response_code(500);
            exit("Error creating table: " . $service_conn->error);
        }

        // Подготовка и выполнение SQL запроса для сохранения данных
        $stmt = $service_conn->prepare("INSERT INTO additional (record_id, description, final_cost, warranty, technician) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $recordId, $description, $finalCost, $warranty, $technician);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Данные успешно сохранены.";
        } else {
            echo "Ошибка при сохранении данных.";
        }

        // Закрытие соединения с базой данных
        $stmt->close();
        $service_conn->close();
    } else {
        http_response_code(400);
        echo "Недостаточно данных для сохранения.";
    }
} else {
    http_response_code(405);
    echo "Метод не разрешен.";
}
?>
