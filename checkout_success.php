<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заказ оформлен - On-Time</title>

    <!-- Фавикон (ярлык) -->
    <link rel="icon" href="assets/images/favicon.ico">

    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- Подключение Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success text-center"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <div class="text-center">
            <h1>Спасибо за ваш заказ!</h1>
            <p>Наши менеджеры свяжутся с вами для подтверждения и уточнения деталей.</p>
            <a href="index.php" class="btn btn-primary">Вернуться на главную</a>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <!-- Подключение скриптов Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
