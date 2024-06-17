<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Страница мастера</title>
    <!-- Подключение Bootstrap и jQuery из CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        html, body {
            height: 100%;
        }
        .container-fluid {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .table-container {
            flex: 1;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h2 class="mt-3">Заказы (страница мастера)</h2>
        <a href="index.php" class="btn btn-primary mb-2">Вернуться назад</a>
        <!-- Добавлено текстовое поле для поиска -->
        <div class="form-group">
            <input type="text" class="form-control" id="searchInput" placeholder="Введите текст для поиска...">
        </div>
        <div class="table-container">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Заказ (ID и дата)</th>
                        <th>Клиент</th>
                        <th>Причина обращения</th>
                        <th>Тип устройства</th>
                        <th>Модель устройства</th>
                        <th>Статус</th>
                        <th>Детали по ремонту</th>
                        <th>Акт работ</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    <?php
                    $sql = "SELECT o.id, o.creation_date, CONCAT(o.client_first_name, ' ', o.client_last_name) AS client_name, o.reason, dt.type_name, dm.model_name, o.status
                            FROM orders o
                            LEFT JOIN device_types dt ON o.device_type = dt.id
                            LEFT JOIN device_models dm ON o.device_model = dm.id";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['id']} ({$row['creation_date']})</td>";
                            echo "<td>{$row['client_name']}</td>";
                            echo "<td>{$row['reason']}</td>";
                            echo "<td>{$row['type_name']}</td>";
                            echo "<td>{$row['model_name']}</td>";
                            // Форма для изменения статуса
                            echo "<td>
                                    <select class='form-control' onchange='changeStatus({$row['id']}, this.value)'>
                                        <option value='новый' " . ($row['status'] == 'новый' ? 'selected' : '') . ">Новый</option>
                                        <option value='в ремонте' " . ($row['status'] == 'в ремонте' ? 'selected' : '') . ">В ремонте</option>
                                        <option value='готов' " . ($row['status'] == 'готов' ? 'selected' : '') . ">Готов</option>
                                    </select>
                                </td>";
                            // Кнопка "Детали по ремонту"
                            echo "<td><button class='btn btn-info btn-sm' onclick='showRepairDetails({$row['id']})'>Детали по ремонту</button></td>";
                            echo "<td><button class='btn btn-success btn-sm' onclick='printAct({$row['id']})'>Акт работы</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>Нет заказов</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Модальное окно для деталей по ремонту -->
    <div class="modal fade" id="repairDetailsModal" tabindex="-1" role="dialog" aria-labelledby="repairDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="repairDetailsModalLabel">Детали по ремонту</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Здесь будет отображаться информация о заказе и форма -->
                    <form id="repairDetailsForm">
                        <!-- Поля для отображения информации о заказе -->
                        <div class="form-group">
                            <label for="orderId">ID заказа:</label>
                            <input type="text" class="form-control" id="orderId" name="orderId" readonly>
                        </div>
                        <div class="form-group">
                            <label for="clientName">Клиент:</label>
                            <input type="text" class="form-control" id="clientName" name="clientName" readonly>
                        </div>
                        <div class="form-group">
                            <label for="reason">Причина обращения:</label>
                            <input type="text" class="form-control" id="reason" name="reason" readonly>
                        </div>
                        <div class="form-group">
                            <label for="deviceType">Тип устройства:</label>
                            <input type="text" class="form-control" id="deviceType" name="deviceType" readonly>
                        </div>
                        <div class="form-group">
                            <label for="deviceModel">Модель устройства:</label>
                            <input type="text" class="form-control" id="deviceModel" name="deviceModel" readonly>
                        </div>
                        <!-- Поля для выбора данных из таблиц -->
                        <div class="form-group">
                            <label for="repairType">Тип ремонта:</label>
                            <select class="form-control" id="repairType" name="repairType" required>
                                <option value="">Выберите тип ремонта</option>
                                <?php
                                $sql = "SELECT id, repair_type FROM repair_types";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['id']}'>{$row['repair_type']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="serviceType">Тип услуги:</label>
                            <select class="form-control" id="serviceType" name="serviceType" required>
                                <option value="">Выберите тип услуги</option>
                                <?php
                                $sql = "SELECT id, service_type FROM service_types";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['id']}'>{$row['service_type']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="warrantyTerm">Срок гарантии:</label>
                            <select class="form-control" id="warrantyTerm" name="warrantyTerm" required>
                                <option value="">Выберите срок гарантии</option>
                                <?php
                                $sql = "SELECT id, warranty_term FROM warranty_terms";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['id']}'>{$row['warranty_term']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary" onclick="saveFinishOrder()">Сохранить</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript для отправки AJAX запроса на изменение статуса и открытия модального окна -->
    <script>
    $(document).ready(function() {
        // Функция для фильтрации таблицы по введенному тексту
        $('#searchInput').on('keyup', function() {
            var searchText = $(this).val().toLowerCase();
            $('#ordersTableBody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
            });
        });
    });

    function printAct(orderId) {
        // Переход на страницу print_act.php с передачей orderId через GET параметр
        window.location.href = 'print_act.php?order_id=' + orderId;
    }

    function changeStatus(orderId, newStatus) {
        $.ajax({
            url: 'scripts/update_status.php',
            method: 'POST',
            data: { order_id: orderId, new_status: newStatus },
            success: function(response) {
                // Обновляем статус в таблице без перезагрузки страницы
                if (response.trim() === 'success') {
                    alert('Статус успешно изменен.');
                } else {
                    alert('Ошибка при изменении статуса.');
                }
            },
            error: function() {
                alert('Ошибка при отправке запроса.');
            }
        });
    }

    function showRepairDetails(orderId) {
        // Получаем данные о заказе по orderId
        $.ajax({
            url: 'scripts/get_order_details.php',
            method: 'POST',
            data: { order_id: orderId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Заполняем модальное окно данными о заказе
                    $('#orderId').val(response.data.id);
                    $('#clientName').val(response.data.client_name);
                    $('#reason').val(response.data.reason);
                    $('#deviceType').val(response.data.type_name);
                    $('#deviceModel').val(response.data.model_name);

                    // Загружаем данные для выпадающих списков
                    loadRepairTypes();
                    loadServiceTypes();
                    loadWarrantyTerms();

                    // Открываем модальное окно
                    $('#repairDetailsModal').modal('show');
                } else {
                    alert('Ошибка при получении данных о заказе.');
                }
            },
            error: function() {
                alert('Ошибка при отправке запроса.');
            }
        });
    }

    function loadRepairTypes() {
        // Загружаем типы ремонта
        $.ajax({
            url: 'scripts/get_repair_types.php',
            method: 'GET',
            dataType: 'html',
            success: function(response) {
                $('#repairType').html(response);
            },
            error: function() {
                alert('Ошибка при загрузке типов ремонта.');
            }
        });
    }

    function loadServiceTypes() {
        // Загружаем типы услуг
        $.ajax({
            url: 'scripts/get_service_types.php',
            method: 'GET',
            dataType: 'html',
            success: function(response) {
                $('#serviceType').html(response);
            },
            error: function() {
                alert('Ошибка при загрузке типов услуг.');
            }
        });
    }

    function loadWarrantyTerms() {
        // Загружаем сроки гарантии
        $.ajax({
            url: 'scripts/get_warranty_terms.php',
            method: 'GET',
            dataType: 'html',
            success: function(response) {
                $('#warrantyTerm').html(response);
            },
            error: function() {
                alert('Ошибка при загрузке сроков гарантии.');
            }
        });
    }

    function saveFinishOrder() {
        // Собираем данные из формы
        var orderId = $('#orderId').val();
        var repairTypeName = $('#repairType option:selected').text();
        var serviceTypeName = $('#serviceType option:selected').text();
        var warrantyTermName = $('#warrantyTerm option:selected').text();

        // Отправляем данные на сервер для сохранения в таблицу finish_orders
        $.ajax({
            url: 'scripts/save_finish_order.php',
            method: 'POST',
            data: {
                order_id: orderId,
                repair_type_name: repairTypeName,
                service_type_name: serviceTypeName,
                warranty_term_name: warrantyTermName
            },
            success: function(response) {
                if (response.trim() === 'success') {
                    alert('Данные успешно сохранены.');
                    $('#repairDetailsModal').modal('hide');
                } else {
                    alert('Ошибка при сохранении данных.');
                }
            },
            error: function() {
                alert('Ошибка при отправке запроса.');
            }
        });
    }
    </script>
</body>
</html>
