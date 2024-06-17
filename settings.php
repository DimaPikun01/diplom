<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Страница настройки</title>
    <!-- Подключение Bootstrap CSS из CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-3">Управление типами устройств и моделями</h2>
        <a href="index.php" class="btn btn-secondary mb-2">Вернуться назад</a>

        <div class="row">
            <div class="col-md-6">
                <!-- Форма для добавления нового типа устройства -->
                <div class="card my-4">
                    <div class="card-header">
                        Добавить новый тип устройства
                    </div>
                    <div class="card-body">
                        <form action="scripts/add_device_type.php" method="POST">
                            <div class="form-group">
                                <label for="device_type">Тип устройства:</label>
                                <input type="text" class="form-control" id="device_type" name="device_type" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Добавить тип устройства</button>
                        </form>
                    </div>
                </div>

                <!-- Форма для добавления моделей устройства -->
                <div class="card my-4">
                    <div class="card-header">
                        Добавить модель устройства
                    </div>
                    <div class="card-body">
                        <form action="scripts/add_device_model.php" method="POST">
                            <div class="form-group">
                                <label for="select_device_type">Выберите тип устройства:</label>
                                <select class="form-control" id="select_device_type" name="device_type_id" required>
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
                                <label for="model_name">Модель устройства:</label>
                                <input type="text" class="form-control" id="model_name" name="model_name" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Добавить модель устройства</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Форма для добавления типов ремонтов -->
                <div class="card my-4">
                    <div class="card-header">
                        Добавить новый тип ремонта
                    </div>
                    <div class="card-body">
                        <form action="scripts/add_repair_type.php" method="POST">
                            <div class="form-group">
                                <label for="repairType">Наименование типа ремонта</label>
                                <input type="text" class="form-control" id="repairType" name="repair_type" required>
                            </div>
                            <div class="form-group">
                                <label for="repairDescription">Описание</label>
                                <textarea class="form-control" id="repairDescription" name="repair_description" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Добавить</button>
                        </form>
                    </div>
                </div>

                <!-- Форма для добавления сроков гарантий -->
                <div class="card my-4">
                    <div class="card-header">
                        Добавить новый срок гарантии
                    </div>
                    <div class="card-body">
                        <form action="scripts/add_warranty_term.php" method="POST">
                            <div class="form-group">
                                <label for="warrantyTerm">Наименование срока гарантии</label>
                                <input type="text" class="form-control" id="warrantyTerm" name="warranty_term" required>
                            </div>
                            <div class="form-group">
                                <label for="warrantyMonths">Количество месяцев</label>
                                <input type="number" class="form-control" id="warrantyMonths" name="warranty_months" min="1" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Добавить</button>
                        </form>
                    </div>
                </div>

                <!-- Форма для добавления типов ремонта -->
                <div class="card my-4">
                    <div class="card-header">
                        Добавить новый тип работ
                    </div>
                    <div class="card-body">
                        <form action="scripts/add_service_type.php" method="POST">
                            <div class="form-group">
                                <label for="serviceType">Наименование типа работы</label>
                                <input type="text" class="form-control" id="serviceType" name="service_type" required>
                            </div>
                            <div class="form-group">
                                <label for="serviceDescription">Описание</label>
                                <textarea class="form-control" id="serviceDescription" name="service_description" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Добавить</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Подключение Bootstrap JS из CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
