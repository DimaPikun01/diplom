<?php
session_start(); // Начало сессии

// Проверка, установлен ли ID сервиса в сессии
if (!isset($_SESSION['service_id'])) {
    header("Location: login_form.php");
    exit;
}

// Подключение к основной базе данных
include '../database/database.php';

// Получение информации о сервисе
$service_id = $_SESSION['service_id'];
$stmt = $conn->prepare("SELECT db_name FROM services WHERE id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();
$db_name = $result->fetch_assoc()['db_name'];

// Подключение к базе данных сервиса
$service_conn = new mysqli($servername, $username, $password, $db_name);

// Проверка, существуют ли таблицы "DeviceType" и "DeviceModel"
if (!$service_conn->query("SHOW TABLES LIKE 'DeviceType'")->num_rows) {
    // Создаем таблицу "DeviceType", если она не существует
    $service_conn->query("
        CREATE TABLE DeviceType (
            id INT AUTO_INCREMENT PRIMARY KEY,
            device_type VARCHAR(50) UNIQUE
        )
    ");
}

if (!$service_conn->query("SHOW TABLES LIKE 'DeviceModel'")->num_rows) {
    // Создаем таблицу "DeviceModel", если она не существует
    $service_conn->query("
        CREATE TABLE DeviceModel (
            id INT AUTO_INCREMENT PRIMARY KEY,
            device_type_id INT,
            model_name VARCHAR(50),
            FOREIGN KEY (device_type_id) REFERENCES DeviceType(id)
        )
    ");
}

// Обработка POST-запросов для добавления новых типов устройств и моделей
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['form_type'])) {
        if ($_POST['form_type'] === 'device_type' && isset($_POST['new_device_type'])) {
            $newDeviceType = $_POST['new_device_type'];

            // Проверка на существование типа устройства
            $stmt = $service_conn->prepare("SELECT * FROM DeviceType WHERE device_type = ?");
            $stmt->bind_param("s", $newDeviceType);
            $stmt->execute();

            if ($stmt->get_result()->num_rows === 0) { // Если типа устройства нет, добавляем его
                $stmt = $service_conn->prepare("INSERT INTO DeviceType (device_type) VALUES (?)");
                $stmt->bind_param("s", $newDeviceType);
                $stmt->execute();
            } else {
                $error_message = "Такой тип устройства уже существует.";
            }
        } elseif ($_POST['form_type'] === 'device_model' && isset($_POST['device_type']) && isset($_POST['new_model_name'])) {
            $deviceType = $_POST['device_type'];
            $newModelName = $_POST['new_model_name'];

            // Получаем ID типа устройства
            $stmt = $service_conn->prepare("SELECT id FROM DeviceType WHERE device_type = ?");
            $stmt->bind_param("s", $deviceType);
            $stmt->execute();
            $deviceTypeId = $stmt->get_result()->fetch_assoc()['id'];

            // Добавляем новый модельный ряд
            $stmt = $service_conn->prepare("INSERT INTO DeviceModel (device_type_id, model_name) VALUES (?, ?)");
            $stmt->bind_param("is", $deviceTypeId, $newModelName);
            $stmt->execute();
        }
    }
}

// Получение всех типов устройств для заполнения выпадающего списка
$deviceTypesResult = $service_conn->query("SELECT * FROM DeviceType");

$deviceData = $service_conn->query("
    SELECT DeviceType.device_type, DeviceModel.model_name
    FROM DeviceModel
    INNER JOIN DeviceType
    ON DeviceModel.device_type_id = DeviceType.id
    ORDER BY DeviceType.device_type, DeviceModel.model_name
");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление устройствами и моделями</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        // Обработка изменения select для выбора типа устройства
        $('#device_type').on('change', function() {
            var deviceType = $(this).val();
            if (deviceType) {
                $('#model_form').show(); // Показываем форму добавления модели
                $('#model_form input[name="device_type"]').val(deviceType); // Устанавливаем выбранный тип устройства
            } else {
                $('#model_form').hide(); // Скрываем форму, если ничего не выбрано
            }
        });
    });
    </script>
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">Управление устройствами и моделями</h2>
        <a href="dashboard.php" class="btn btn-primary">Вернуться к панели управления</a>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <h3>Добавить новый тип устройства:</h3>
                <form action="manage_devices.php" method="post">
                    <input type="hidden" name="form_type" value="device_type">
                    <div class="form-group">
                        <input type="text" class="form-control" name="new_device_type" placeholder="Название нового типа устройства" required>
                    </div>
                    <button type="submit" class="btn btn-success">Добавить</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h3>Выбрать тип устройства:</h3>
                <div class="form-group">
                    <select class="form-control" id="device_type" name="device_type">
                        <option value="">Выберите тип устройства</option>
                        <?php while ($row = $deviceTypesResult->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['device_type']); ?>"><?php echo htmlspecialchars($row['device_type']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div id="model_form" style="display:none;">
                    <h3>Добавить модель к выбранному типу устройства:</h3>
                    <form action="manage_devices.php" method="post">
                        <input type="hidden" name="form_type" value="device_model">
                        <input type="hidden" name="device_type" value=""><!-- Устанавливаем тип устройства -->
                        <div class="form-group">
                            <input type="text" class="form-control" name="new_model_name" placeholder="Название новой модели" required>
                        </div>
                        <button type="submit" class="btn btn-success">Добавить модель</button>
                    </form>
                </div>
            </div>
        </div>

        <h3>Типы устройств и их модели:</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Тип устройства</th>
                    <th>Модель</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $deviceData->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['device_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['model_name']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
