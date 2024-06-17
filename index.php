<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Страница мастерской по ремонту техники</title>
    <!-- Подключение Bootstrap и jQuery из CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Подключение Select2 CSS и JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- Подключение Font Awesome для иконок -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
        .floating-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 100;
        }
        .technician-icon, .manager-icon {
            margin-right: 5px;
            font-size: 1.2em;
        }
        .status-new {
            background-color: #007bff;
            color: white;
        }
        .status-repair {
            background-color: #fd7e14;
            color: white;
        }
        .status-ready {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h2 class="mt-3">Заказы</h2>
        <a href="master.php" class="btn btn-primary mb-2">Страница мастера</a>
        <a href="settings.php" class="btn btn-secondary mb-2">Страница настроек</a>
        <div class="form-group">
            <input type="text" class="form-control mb-2" id="searchInput" placeholder="Поиск...">
        </div>
        <div class="btn-group mb-2" role="group" aria-label="Фильтр заказов">
            <button type="button" class="btn btn-info filter-button" data-status="active">Активные</button>
            <button type="button" class="btn btn-warning filter-button" data-status="в ремонте">В ремонте</button>
            <button type="button" class="btn btn-success filter-button" data-status="готов">Готовые</button>
        </div>
        <div class="table-container">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Заказ (ID и дата)</th>
                        <th>Статус</th>
                        <th>Клиент</th>
                        <th>Менеджер</th>
                        <th>Мастер</th>
                        <th>Стоимость</th>
                        <th>Причина обращения</th>
                        <th>IMEI/SN</th>
                        <th>Внешний вид</th>
                        <th>Тип устройства</th>
                        <th>Модель устройства</th>
                        <th>Действия</th> <!-- Новая колонка для кнопок -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT o.*, dt.type_name, dm.model_name 
                            FROM orders o 
                            LEFT JOIN device_types dt ON o.device_type = dt.id
                            LEFT JOIN device_models dm ON o.device_model = dm.id";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $statusClass = '';
                            switch ($row['status']) {
                                case 'новый':
                                    $statusClass = 'status-new';
                                    break;
                                case 'в ремонте':
                                    $statusClass = 'status-repair';
                                    break;
                                case 'готов':
                                    $statusClass = 'status-ready';
                                    break;
                                default:
                                    $statusClass = '';
                            }
                            echo "<tr class='order-row' data-status='{$row['status']}'>";
                            echo "<td>{$row['id']} ({$row['creation_date']})</td>";
                            echo "<td class='$statusClass'>{$row['status']}</td>";
                            echo "<td>{$row['client_first_name']} {$row['client_last_name']} ({$row['client_phone']})</td>";
                            echo "<td><i class='fas fa-user-tie manager-icon'></i>{$row['manager']}</td>";
                            echo "<td><i class='fas fa-user technician-icon'></i>{$row['technician']}</td>";
                            echo "<td>{$row['cost']}</td>";
                            echo "<td>{$row['reason']}</td>";
                            echo "<td>{$row['imei_sn']}</td>";
                            echo "<td>{$row['appearance']}</td>";
                            echo "<td>{$row['type_name']}</td>";
                            echo "<td>{$row['model_name']}</td>";
                            echo "<td>
                                    <a href='receipt.php?order_id={$row['id']}' class='btn btn-info btn-sm mr-2'>Квитанция</a>
                                    <button class='btn btn-primary btn-sm edit-button' data-id='{$row['id']}' data-toggle='modal' data-target='#editOrderModal'>Редактировать</button>
                                  </td>"; // Кнопки квитанции и редактирования
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='12'>Нет заказов</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Плавающая кнопка для добавления нового заказа -->
    <button class="btn btn-primary floating-button" data-toggle="modal" data-target="#addOrderModal">+</button>

    <!-- Модальное окно для добавления нового заказа -->
    <div class="modal fade" id="addOrderModal" tabindex="-1" role="dialog" aria-labelledby="addOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOrderModalLabel">Добавить новый заказ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addOrderForm" action="scripts/add_order.php" method="POST">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="client_first_name">Имя клиента</label>
                                <input type="text" class="form-control" id="client_first_name" name="client_first_name" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="client_last_name">Фамилия клиента</label>
                                <input type="text" class="form-control" id="client_last_name" name="client_last_name" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="client_phone">Телефон клиента</label>
                            <input type="text" class="form-control" id="client_phone" name="client_phone" required>
                        </div>
                        <div class="form-group">
                            <label for="manager">Менеджер</label>
                            <input type="text" class="form-control" id="manager" name="manager" required>
                        </div>
                        <div class="form-group">
                            <label for="technician">Мастер</label>
                            <input type="text" class="form-control" id="technician" name="technician" required>
                        </div>
                        <div class="form-group">
                            <label for="cost">Стоимость</label>
                            <input type="number" class="form-control" id="cost" name="cost" required>
                        </div>
                        <div class="form-group">
                            <label for="reason">Причина обращения</label>
                            <input type="text" class="form-control" id="reason" name="reason" required>
                        </div>
                        <div class="form-group">
                            <label for="imei_sn">IMEI/SN</label>
                            <input type="text" class="form-control" id="imei_sn" name="imei_sn" required>
                        </div>
                        <div class="form-group">
                            <label for="appearance">Внешний вид</label>
                            <input type="text" class="form-control" id="appearance" name="appearance" required>
                        </div>
                        <div class="form-group">
                            <label for="device_type">Тип устройства</label>
                            <select class="form-control" id="device_type" name="device_type" required>
                                <option value="">Выберите тип устройства</option>
                                <?php
                                $sql = "SELECT id, type_name FROM device_types";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['id']}'>{$row['type_name']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="device_model">Модель устройства</label>
                            <select class="form-control" id="device_model" name="device_model" required>
                                <option value="">Выберите модель устройства</option>
                                <!-- Опции моделей будут добавлены динамически через JavaScript -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Добавить заказ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования заказа -->
    <div class="modal fade" id="editOrderModal" tabindex="-1" role="dialog" aria-labelledby="editOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editOrderModalLabel">Редактировать заказ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editOrderForm" action="scripts/edit_order.php" method="POST">
                        <!-- Здесь будут поля для редактирования заказа -->
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Инициализация Select2 для выбора типа и модели устройства -->
    <script>
    $(document).ready(function() {
        $('#device_type').select2();
        $('#device_model').select2();

        $('#device_type').change(function() {
            var deviceTypeId = $(this).val();
            if (deviceTypeId) {
                $.ajax({
                    url: 'scripts/get_device_models.php',
                    method: 'POST',
                    data: { device_type_id: deviceTypeId },
                    success: function(response) {
                        $('#device_model').html(response);
                    },
                    error: function() {
                        alert('Ошибка при загрузке моделей устройств.');
                    }
                });
            } else {
                $('#device_model').html('<option value="">Выберите модель устройства</option>');
            }
        });

        // Функция для фильтрации таблицы по введенному тексту
        $('#searchInput').on('keyup', function() {
            var searchText = $(this).val().toLowerCase();
            $('table tbody tr').each(function() {
                var lineStr = $(this).text().toLowerCase();
                if (lineStr.indexOf(searchText) === -1) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        });

        // Фильтрация таблицы по статусу
        $('.filter-button').click(function() {
            var status = $(this).data('status');
            if (status === 'active') {
                $('table tbody tr').show(); // Показать все заказы
            } else {
                $('table tbody tr').each(function() {
                    var orderStatus = $(this).data('status');
                    if (orderStatus === status) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });

        // Открытие модального окна для редактирования заказа
        $(document).on('click', '.edit-button', function() {
            var orderId = $(this).data('id');
            $.ajax({
                url: 'scripts/get_order.php',
                method: 'POST',
                data: { order_id: orderId },
                success: function(response) {
                    $('#editOrderForm').html(response);
                    $('#editOrderModal').modal('show');
                },
                error: function() {
                    alert('Ошибка при загрузке данных заказа для редактирования.');
                }
            });
        });

    });

    $(document).ready(function() {
    $('#client_first_name, #client_last_name').on('keyup', function() {
        var firstName = $('#client_first_name').val().trim();
        var lastName = $('#client_last_name').val().trim();

        if (firstName !== '' && lastName !== '') {
            $.ajax({
                url: 'scripts/find_client.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    first_name: firstName,
                    last_name: lastName
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#client_phone').val(response.data.client_phone);
                        $('#manager').val(response.data.manager);
                        $('#technician').val(response.data.technician);
                        $('#cost').val(response.data.cost);
                        $('#reason').val(response.data.reason);
                    } else {
                        // Клиент не найден или произошла ошибка
                        $('#client_phone').val('');
                        $('#manager').val('');
                        $('#technician').val('');
                        $('#cost').val('');
                        $('#reason').val('');
                    }
                },
                error: function() {
                    alert('Ошибка при поиске клиента.');
                    $('#client_phone').val('');
                    $('#manager').val('');
                    $('#technician').val('');
                    $('#cost').val('');
                    $('#reason').val('');
                }
            });
        } else {
            // Если поля пустые, сбросить значения
            $('#client_phone').val('');
            $('#manager').val('');
            $('#technician').val('');
            $('#cost').val('');
            $('#reason').val('');
        }
    });
});



    </script>
</body>
</html>
