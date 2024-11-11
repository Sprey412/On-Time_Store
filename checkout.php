<?php
include 'config.php';
session_start();

// Проверка наличия товаров в корзине
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Обработка оформления заказа
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    // Здесь можно добавить логику сохранения заказа в базу данных и отправки уведомлений
    // Например:
    // 1. Сохранить заказ в таблицу orders
    // 2. Сохранить товары заказа в таблицу order_items
    // 3. Очистить корзину
    // 4. Отправить подтверждение на email пользователя

    // Для демонстрации просто очистим корзину и покажем сообщение
    unset($_SESSION['cart']);
    $_SESSION['message'] = "Ваш заказ оформлен успешно!";
    header("Location: checkout_success.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оформление заказа - On-Time</title>

    <!-- Фавикон (ярлык) -->
    <link rel="icon" href="assets/images/favicon.ico">

    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- Подключение Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Оформление заказа</h1>
        <form method="POST">
            <!-- Поля для ввода информации о заказчике -->
            <div class="form-group">
                <label for="fullname">ФИО</label>
                <input type="text" class="form-control" id="fullname" name="fullname" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="address">Адрес доставки</label>
                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
            </div>
            <!-- Другие поля по необходимости -->
            <button type="submit" name="checkout" class="btn btn-success">Оформить заказ</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>
    <!-- Подключение скриптов Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
