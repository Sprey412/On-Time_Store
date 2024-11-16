<?php
// Проверка авторизации
session_start();
if (!isset($_SESSION['employee_id'])) {
    header('Location: login.php');
    exit();
}
