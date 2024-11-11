<?php
session_start();
include 'config.php';

// Генерация CSRF токена
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = ""; // Инициализация переменной для ошибок

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверка CSRF токена
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Неверный CSRF токен.";
    } else {
        // Получение данных из формы
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Валидация email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Некорректный формат email.";
        }
        // Проверка на пустые поля
        elseif (empty($email) || empty($password)) {
            $error = "Пожалуйста, заполните все поля.";
        } else {
            // Подготовка SQL-запроса для получения хешированного пароля пользователя
            $sql = "SELECT employee_id, password_hash FROM employees WHERE email = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                $error = "Ошибка подготовки запроса: " . htmlspecialchars($conn->error);
            } else {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                // Проверка, найден ли пользователь
                if ($row = $result->fetch_assoc()) {
                    // Проверка пароля
                    if (password_verify($password, $row['password_hash'])) {
                        // Успешная авторизация
                        session_regenerate_id(true); // Предотвращение фиксации сессии
                        $_SESSION['employee_id'] = $row['employee_id'];
                        $_SESSION['username'] = $email; // Или другое поле для отображения имени
                        header("Location: dashboard.php"); // Перенаправление на страницу дашборда
                        exit();
                    } else {
                        $error = "Неверный пароль.";
                    }
                } else {
                    $error = "Пользователь не найден.";
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Вход</title>

    <!-- Фавикон (ярлык) -->
    <link rel="icon" href="assets/images/favicon.ico">

    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- Подключение Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    
    <style>
        body {
            background-color: #343a40; /* Темный фон страницы */
            color: #ffffff; /* Белый текст */
        }
        .login-form {
            background-color: #495057; /* Темный фон формы */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }
        .form-control {
            background-color: #6c757d; /* Темный фон полей ввода */
            color: #ffffff; /* Белый текст */
            border: 1px solid #ced4da;
        }
        .form-control::placeholder {
            color: #adb5bd; /* Цвет плейсхолдера */
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0069d9;
        }
        .btn-link {
            color: #ffffff;
        }
        .btn-link:hover {
            color: #d4d4d4;
            text-decoration: underline;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="container">
        <h2 class="text-center mb-4">Вход</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php" class="login-form">
            <!-- CSRF токен -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Пароль" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Войти</button>
        </form>
        <div class="text-center mt-3">
            <a href="start.php" class="btn btn-link">На главную</a>
        </div>
    </div>

    <!-- Подключение скриптов -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <!-- Подключение Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
