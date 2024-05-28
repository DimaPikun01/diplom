<?php
session_start(); // Начало сессии

// Удаление всех данных сессии
session_unset();
session_destroy();

// Перенаправление на страницу входа
header("Location: login_form.php");
exit;
?>
