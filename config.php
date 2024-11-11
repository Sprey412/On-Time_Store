<?php
// Параметры подключения к базе данных
$servername = "localhost";  // Хост MySQL
$username = "root";         // Имя пользователя MySQL
$password = "";             // Пароль пользователя MySQL
$dbname = "watch_store";    // Имя базы данных

// Создание подключения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Установка кодировки
$conn->set_charset("utf8mb4");
?>
