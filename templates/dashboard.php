<?php
session_start(); // Начало сессии

// Проверка, установлен ли ID сервиса в сессии
if (!isset($_SESSION['service_id'])) {
    // Если нет, перенаправляем на страницу входа
    header("Location: login_form.php");
    exit;
}

// Получение информации о сервисе из сессии
$service_id = $_SESSION['service_id'];
$service_name = $_SESSION['service_name'];

// Подключение к базе данных основного сервиса
include '../database/database.php';

// Получаем название базы данных для текущего сервиса
$stmt = $conn->prepare("SELECT db_name FROM services WHERE id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();
$service = $result->fetch_assoc();
$db_name = $service['db_name'];

// Подключение к базе данных сервиса
$service_conn = new mysqli($servername, $username, $password, $db_name);

if ($service_conn->connect_error) {
    die("Ошибка подключения к базе данных сервиса: " . $service_conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_name = $_POST['client_name'];
    $phone_number = $_POST['phone_number'];
    $device_type = $_POST['device_type'];
    $model = $_POST['device_model'];
    $status = $_POST['status'];
    $issue = $_POST['issue'];
    $cost = $_POST['cost'];

    $stmt = $service_conn->prepare("INSERT INTO Reception (client_name, phone_number, device_type, model, status, issue, cost) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssd", $client_name, $phone_number, $device_type, $model, $status, $issue, $cost);

    if ($stmt->execute()) {
        // Используем PRG: перенаправляем на ту же страницу, чтобы избежать повторного отправления
        header("Location: dashboard.php?success=1");
        exit;
    } else {
        $error_message = "Ошибка при добавлении записи: " . $stmt->error;
    }
}

$statusTranslations = [
    "ready" => "Готов",
    "waiting" => "Ждет запчасти",
    "no_repair" => "Без ремонта"
];

// Извлечение данных из таблицы "Приемка"
$result = $service_conn->query("SELECT * FROM Reception");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель управления</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet">
    <script src="../scripts/ajax.js"></script>
    <script src="../scripts/search.js"></script>
    <style>
        #receptionTableContainer {
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
    <script>
    $(document).ready(function() {
        // AJAX-запрос для получения данных из get_device_data.php
        $.ajax({
            url: '../includes/get_device_data.php', // Путь к вашему файлу
            method: 'GET',
            success: function(data) {
                var deviceTypes = data.device_types;
                var deviceModels = data.device_models;

                // Заполнение селектора типа устройства
                $('#device_type').empty();
                $('#device_type').append('<option value="">Выберите тип устройства</option>');
                deviceTypes.forEach(function(type) {
                    var option = $('<option>').attr('value', type).text(type);
                    $('#device_type').append(option);
                });

                // Обработчик изменения типа устройства для обновления списка моделей
                $('#device_type').on('change', function() {
                    var deviceType = $(this).val();
                    $('#device_model').empty(); // Очищаем селектор моделей

                    if (deviceType) {
                        $('#device_model').append('<option value="">Выберите модель</option>'); // Значение по умолчанию
                        deviceModels.forEach(function(model) {
                            if (model.device_type === deviceType) { // Фильтрация по типу
                                var option = $('<option>').attr('value', model.model_name).text(model.model_name);
                                $('#device_model').append(option);
                            }
                        });
                    }
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Ошибка при получении данных:', textStatus, errorThrown);
            }
        });

        // Функция для фильтрации таблицы
        $('#search').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#receptionTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
    </script>
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">Панель управления сервиса: <?php echo htmlspecialchars($service_name); ?></h2>
        <a href="../templates/manage_devices.php" class="btn btn-info">Таблица устройств</a>
        <a href="../templates/master_page.php" class="btn btn-info">Мастер</a>
        <a href="logout.php" class="btn btn-danger">Выйти</a>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success">Запись успешно добавлена.</div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <h3>Таблица приемки:</h3>
        <div class="form-group">
            <input type="text" class="form-control" id="search" placeholder="Поиск по таблице...">
        </div>
        <div id="receptionTableContainer">
            <table class="table table-bordered" id="receptionTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя клиента</th>
                        <th>Номер телефона</th>
                        <th>Тип устройства</th>
                        <th>Модель</th>
                        <th>Статус</th>
                        <th>Неисправность</th>
                        <th>Стоимость</th>
                        <th>Документ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['client_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['device_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['model']); ?></td>
                            <td>
                                <?php
                                // Перевод статуса с английского на русский
                                $statusInEnglish = $row['status'];
                                $status = isset($statusTranslations[$statusInEnglish]) ? $statusTranslations[$statusInEnglish] : $statusInEnglish;
                                echo htmlspecialchars($status);
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['issue']); ?></td>
                            <td><?php echo htmlspecialchars($row['cost']); ?></td>
                            <td>
                                <button class="btn btn-primary" onclick="window.open('../templates/receipt_page.php?id=<?php echo $row['id']; ?>', '_blank')">Квитанция</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <h3>Добавить новую запись в приемку:</h3>
        <form action="dashboard.php" method="post" class="mb-4">
            <div class="form-group">
                <label for="client_name">Имя клиента:</label>
                <input type="text" class="form-control" name="client_name" id="client_name" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Номер телефона:</label>
                <input type="text" class="form-control" name="phone_number" id="phone_number">
            </div>
            <div class="form-group">
                <label for="device_type">Тип устройства:</label>
                <select class="form-control" id="device_type" name="device_type" required>
                    <!-- AJAX будет заполнять этот селектор -->
                </select>
            </div>
            <div class="form-group">
                <label for="device_model">Модель:</label>
                <select class="form-control" id="device_model" name="device_model" required>
                    <!-- Заполнение зависит от выбранного типа устройства -->
                </select>
            </div>
            <div class="form-group">
                <label for="status">Статус:</label>
                <input type="text" class="form-control" name="status" id="status">
            </div>
            <div class="form-group">
                <label for="issue">Неисправность:</label>
                <input type="text" class="form-control" name="issue" id="issue">
            </div>
            <div class="form-group">
                <label for="cost">Стоимость:</label>
                <input type="number" step="0.01" class="form-control" name="cost" id="cost">
            </div>
            <button type="submit" class="btn btn-success">Добавить</button>
        </form>
    </div>
</body>
</html>
