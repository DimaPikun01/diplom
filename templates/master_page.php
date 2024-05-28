<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Страница мастера</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <style>
        .status-ready { color: green; }
        .status-waiting { color: orange; }
        .status-no-repair { color: red; }
        .additional-info { display: none; }
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; }
        .close:hover, .close:focus { color: black; text-decoration: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Страница мастера</h2>
        <a href="../templates/dashboard.php" class="btn btn-primary mb-3">Назад</a>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Имя клиента</th>
                    <th>Номер телефона</th>
                    <th>Тип устройства</th>
                    <th>Модель</th>
                    <th>Неисправность</th>
                    <th>Статус</th>
                    <th>Смена статуса</th>
                    <th>Дополнительно</th>
                    <th>Печать акта</th>
                    <th>Запись</th>
                </tr>
            </thead>
            <tbody>
                <?php
                session_start(); // Начало сессии

                // Проверка, что пользователь авторизован
                if (!isset($_SESSION['service_id'])) {
                    header("Location: login_form.php");
                    exit;
                }

                // Подключение к основной базе данных
                include '../database/database.php';

                // Получение ID текущего сервиса из сессии
                $service_id = $_SESSION['service_id'] ?? null;

                // Подготовка запроса для получения данных о базе данных текущего сервиса
                $stmt = $conn->prepare("SELECT db_name FROM services WHERE id = ?");
                $stmt->bind_param("i", $service_id);
                $stmt->execute();
                $db_name_result = $stmt->get_result();

                // Проверка наличия данных о базе данных текущего сервиса
                if ($db_name_result->num_rows === 0) {
                    exit("Service data not found.");
                }

                // Получение имени базы данных текущего сервиса
                $db_name_row = $db_name_result->fetch_assoc();
                $db_name = $db_name_row['db_name'];

                // Подключение к базе данных текущего сервиса
                $service_conn = new mysqli($servername, $username, $password, $db_name);

                // Проверка подключения к базе данных текущего сервиса
                if ($service_conn->connect_error) {
                    http_response_code(500);
                    exit("Connection error: " . $service_conn->connect_error);
                }

                // Получение всех записей из таблицы "Reception" для текущего сервиса
                $result = $service_conn->query("SELECT * FROM Reception");

                // Выводим сообщение об ошибке, если есть
                if (!$result) {
                    echo "<tr><td colspan='8'>Ошибка: " . $service_conn->error . "</td></tr>";
                } else {
                    // Получаем данные из базы данных
                    while ($row = $result->fetch_assoc()) {
                        $client_name = htmlspecialchars($row['client_name']); // Имя клиента
                        echo "<tr class='record-row' data-record-id='{$row['id']}'>"; // Добавим атрибут для идентификации записи
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$client_name}</td>"; // Убедиться, что имя клиента присутствует
                        echo "<td>{$row['phone_number']}</td>";
                        echo "<td>{$row['device_type']}</td>";
                        echo "<td>{$row['model']}</td>";
                        echo "<td>{$row['issue']}</td>";
                        echo "<td>{$row['status']}</td>";
                        echo "<td>";
                        echo "<select class='status-select form-control' data-record-id='{$row['id']}'>";
                        echo "<option value='ready' class='status-ready' " . ($row['status'] == 'ready' ? 'selected' : '') . ">Готов</option>";
                        echo "<option value='waiting' class='status-waiting' " . ($row['status'] == 'waiting' ? 'selected' : '') . ">Ждет запчасти</option>";
                        echo "<option value='no_repair' class='status-no-repair' " . ($row['status'] == 'no_repair' ? 'selected' : '') . ">Без ремонта</option>";
                        echo "</select>";
                        echo "</td>";
                        echo "<td><button class='btn btn-info additional-info-button'>Дополнительно</button></td>"; // Кнопка для открытия дополнительной информации
                        echo "<td><button class='btn btn-primary print-record-button' data-record-id='{$row['id']}'>Распечатать акт</button></td>"; // Кнопка для печати акта
                        echo "<td><button class='btn btn-secondary show-info-button' data-record-id='{$row['id']}'>Записи</button></td>"; // Кнопка для отображения информации в модальном окне
                        echo "</tr>";
                        // Блок с дополнительной информацией
                        echo "<tr class='additional-info' style='display:none;'>";
                        echo "<td colspan='8'>";
                        echo "<input type='text' class='form-control mb-2 description' placeholder='Описание'>";
                        echo "<input type='number' class='form-control mb-2 final-cost' placeholder='Окончательная стоимость'>";
                        echo "<input type='text' class='form-control mb-2 warranty' placeholder='Гарантия'>";
                        echo "<input type='text' class='form-control mb-2 technician' placeholder='Мастер'>";
                        echo "<button class='btn btn-success save-button'>Сохранить</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>

        <!-- Модальное окно -->
        <div id="additionalModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <div id="additionalInfo"></div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
    $('.status-select').on('change', function() {
        var recordId = $(this).data('record-id');
        var newStatus = $(this).val();

        $.ajax({
            url: '../includes/update_status.php',
            method: 'POST',
            data: {
                'record_id': recordId,
                'new_status': newStatus
            },
            success: function() {
                console.log('Статус обновлен');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Ошибка при обновлении статуса:', textStatus, errorThrown);
            }
        });
    });

    // Показать/скрыть блок с дополнительной информацией при клике на кнопку
    $('.additional-info-button').click(function() {
        $(this).closest('tr').next('.additional-info').toggle();
    });

    // Обработчик нажатия кнопки "Сохранить"
    $('.save-button').click(function() {
        var recordId = $(this).closest('tr').prev('.record-row').data('record-id'); // ID записи
        var description = $(this).siblings('.description').val(); // Описание
        var finalCost = $(this).siblings('.final-cost').val(); // Окончательная стоимость
        var warranty = $(this).siblings('.warranty').val(); // Гарантия
        var technician = $(this).siblings('.technician').val(); // Мастер

        // Отправка данных через AJAX
        $.ajax({
            url: '../includes/save_additional_info.php',
            method: 'POST',
            data: {
                'record_id': recordId,
                'description': description,
                'final_cost': finalCost,
                'warranty': warranty,
                'technician': technician
            },
            success: function(response) {
                console.log('Данные сохранены:', response);
                // Очистка полей ввода после сохранения
                $(this).siblings('.description, .final-cost, .warranty, .technician').val('');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Ошибка при сохранении данных:', textStatus, errorThrown);
            }
        });
    });

    // Обработчик нажатия кнопки "Записи"
    $('.show-info-button').on('click', function() {
        var recordId = $(this).data('record-id'); // Получаем ID записи из атрибута данных
        // Отправляем AJAX запрос для получения дополнительной информации
        $.ajax({
            url: '../includes/get_additional_info.php',
            method: 'POST',
            data: { 'record_id': recordId },
            success: function(response) {
                // Отображаем полученную информацию в модальном окне
                $('#additionalInfo').html(response);
                $('#additionalModal').show(); // Показываем модальное окно
                //console.log(recordId);
            },
            error: function(xhr, status, error) {
                console.error('Ошибка при получении дополнительной информации:', error);
            }
        });
    });

    // Закрытие модального окна при клике на крестик
    $('.modal .close').click(function() {
        $('#additionalModal').hide();
    });

    // Закрытие модального окна при клике за его пределами
    $(window).click(function(event) {
        if (event.target == $('#additionalModal')[0]) {
            $('#additionalModal').hide();
        }
    });

    // Обработчик нажатия кнопки "Распечатать акт"
    $('.print-record-button').click(function() {
            var recordId = $(this).data('record-id');
            window.open('../includes/print_record.php?record_id=' + recordId, '_blank');
        });
});
    </script>





</body>
</html>
