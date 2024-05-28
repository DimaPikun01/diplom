<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация сервиса</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            margin: 0;
            padding: 0;
            height: 100vh;
        }
        .left {
            width: 50%;
            background: url('../images/background.jpg') no-repeat center center;
            background-size: cover;
        }
        .right {
            width: 50%;
            background-color: #191970;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .reg {
            background-color: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        .error-message {
            color: red;
        }
        .is-invalid {
            border-color: red;
        }
        .form-text {
            color: red;
        }
    </style>
</head>
<body>
    <div class="left"></div>
    <div class="right">
        <div class="reg">
            <h2 class="text-center">Регистрация сервиса</h2>
            <?php
            if (isset($_GET['error'])) {
                $error = $_GET['error'];
                if ($error === 'service_exists') {
                    echo "<p class='error-message'>Ошибка при регистрации: Сервис с таким именем уже существует.</p>";
                } elseif ($error === 'db_exists') {
                    echo "<p class='error-message'>Ошибка при регистрации: База данных с таким именем уже существует.</p>";
                } elseif ($error === 'registration_failed') {
                    echo "<p class='error-message'>Ошибка при регистрации: Не удалось зарегистрировать сервис.</p>";
                }
            }
            ?>
            <form id="registrationForm" action="../includes/register_service.php" method="post">
                <div class="form-group">
                    <label for="service_name">Название сервиса:</label>
                    <input type="text" class="form-control" name="service_name" id="service_name" required>
                </div>
                <div class="form-group">
                    <label for="address">Адрес:</label>
                    <input type="text" class="form-control" name="address" id="address" required>
                </div>
                <div class="form-group">
                    <label for="reg_number">УНП регистрации:</label>
                    <input type="text" class="form-control" name="reg_number" id="reg_number" required>
                </div>
                <div class="form-group">
                    <label for="admin_login">Логин администратора:</label>
                    <input type="text" class="form-control" name="admin_login" id="admin_login" required>
                    <small class="form-text">Логин должен содержать не менее 3 символов и состоять только из букв и цифр.</small>
                </div>
                <div class="form-group">
                    <label for="admin_password">Пароль администратора:</label>
                    <input type="password" class="form-control" name="admin_password" id="admin_password" required>
                    <small class="form-text">Пароль должен содержать не менее 6 символов, включать хотя бы одну букву и одну цифру.</small>
                </div>
                <div class="form-group">
                    <label for="db_name">Название базы данных:</label>
                    <input type="text" class="form-control" name="db_name" id="db_name" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Зарегистрироваться</button>
            </form>
            <div class="text-center mt-3">
                <a href="../templates/login_form.php" class="text-primary">Войти</a>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            var valid = true;

            // Проверка логина
            var adminLogin = document.getElementById('admin_login');
            var loginRegex = /^[a-zA-Z0-9]{3,}$/;
            if (!loginRegex.test(adminLogin.value)) {
                adminLogin.classList.add('is-invalid');
                valid = false;
            } else {
                adminLogin.classList.remove('is-invalid');
            }

            // Проверка пароля
            var adminPassword = document.getElementById('admin_password');
            var passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d).{6,}$/;
            if (!passwordRegex.test(adminPassword.value)) {
                adminPassword.classList.add('is-invalid');
                valid = false;
            } else {
                adminPassword.classList.remove('is-invalid');
            }

            // Если форма не валидна, предотвращаем отправку
            if (!valid) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>
