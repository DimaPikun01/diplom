<?php
session_start(); // Начало сессии

include '../database/database.php';

// Функция для проверки корректности логина
function isValidLogin($login) {
    // Логин должен быть не менее 3 символов и содержать только буквы и цифры
    return preg_match('/^[a-zA-Z0-9]{3,}$/', $login);
}

// Функция для проверки корректности пароля
function isValidPassword($password) {
    // Пароль должен быть не менее 6 символов и содержать хотя бы одну букву и одну цифру
    return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/', $password);
}

// Проверка, был ли запрос через POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adminLogin = $_POST['admin_login'];
    $adminPassword = $_POST['admin_password'];

    // Проверка логина и пароля на корректность
    if (!isValidLogin($adminLogin)) {
        header("Location: ../templates/login_form.php?error=invalid_login");
        exit;
    } elseif (!isValidPassword($adminPassword)) {
        header("Location: ../templates/login_form.php?error=invalid_password");
        exit;
    } else {
        // Используем подготовленные операторы, чтобы избежать SQL-инъекций
        $stmt = $conn->prepare("SELECT * FROM services WHERE admin_login = ?");
        $stmt->bind_param("s", $adminLogin);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $service = $result->fetch_assoc();

            // Проверка пароля
            if (password_verify($adminPassword, $service['admin_password'])) {
                // Успешный вход, устанавливаем сессию
                $_SESSION['service_id'] = $service['id'];
                $_SESSION['service_name'] = $service['name'];

                // Перенаправление на панель управления
                header("Location: ../templates/dashboard.php");
                exit;
            } else {
                header("Location: ../templates/login_form.php?error=wrong_password");
                exit;
            }
        } else {
            header("Location: ../templates/login_form.php?error=login_not_found");
            exit;
        }
    }
}
?>
