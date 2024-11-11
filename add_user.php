<?php
// Подключение к базе данных
include 'config.php';

// Данные пользователя
$name = 'Admin';
$email = 'spreyking@gmail.com';
$password = '1029384756gtgT'; // Исходный пароль
$role = 'admin';

// Хэширование пароля
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// SQL-запрос для вставки пользователя
$sql = "INSERT INTO employees (name, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $password_hash, $role);

// Выполнение запроса и проверка успешности
if ($stmt->execute()) {
    echo "Пользователь успешно добавлен!";
} else {
    echo "Ошибка: " . $stmt->error;
}

// Закрытие соединения
$stmt->close();
$conn->close();
?>
