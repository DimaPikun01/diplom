<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в сервис</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            margin: 0;
            padding: 0;
            display: flex;
        }
        .left{
            width: 100%;
            height: 100vh;
            background: url(../images/background.jpg);
            background-size: cover;
        }
        .right{
            width: 100%;
            height: 100vh;
            background-color: #191970;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .reg{
            width: 500px;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            border-radius: 20px;
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 left"></div>
        <div class="col-md-6 right">
            <div class="reg">
                <h2>Вход в сервис</h2>
                <?php
                if (isset($_GET['error'])) {
                    $error = $_GET['error'];
                    $errorMessage = '';
                    switch ($error) {
                        case 'invalid_login':
                            $errorMessage = 'Некорректный логин. Логин должен содержать не менее 3 символов и состоять только из букв и цифр.';
                            break;
                        case 'invalid_password':
                            $errorMessage = 'Некорректный пароль. Пароль должен содержать не менее 6 символов, включать хотя бы одну букву и одну цифру.';
                            break;
                        case 'wrong_password':
                            $errorMessage = 'Неверный пароль.';
                            break;
                        case 'login_not_found':
                            $errorMessage = 'Логин не найден.';
                            break;
                        default:
                            $errorMessage = 'Неизвестная ошибка.';
                            break;
                    }
                    echo '<p style="color:red;">' . htmlspecialchars($errorMessage) . '</p>';
                }
                ?>
                <form action="../includes/login.php" method="post">
                    <div class="form-group">
                        <label for="admin_login">Логин:</label>
                        <input type="text" class="form-control" name="admin_login" id="admin_login" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_password">Пароль:</label>
                        <input type="password" class="form-control" name="admin_password" id="admin_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Войти</button>
                </form>
                <a href="../index.php">Нет аккаунта? Регистрация!</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
