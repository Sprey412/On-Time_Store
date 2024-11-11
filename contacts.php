<?php
// Подключение файла конфигурации (если необходимо)
include 'config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Контакты - On-Time</title>

    <!-- Фавикон (ярлык) -->
    <link rel="icon" href="assets/images/favicon.ico">

    <!-- Подключение Bootstrap и других ресурсов -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Ваш собственный CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <!-- Включение хедера -->
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Контактная информация</h1>
        <p style="font-size: 20px;">Свяжитесь с нами любым удобным для вас способом:</p>
        <ul style="font-size: 20px;">
            <li><strong>Адрес:</strong> г. Чехов, Симферопольское шоссе, 1</li>
            <li><strong>Телефон:</strong> +7 (985) 722-01-98</li>
            <li><strong>Email:</strong> support@ontime.ru</li>
            <li><strong>Режим работы:</strong> Пн-Вс: 10:00 - 22:00, без выходных</li>
        </ul>

        <div style="position:relative;overflow:hidden;"><a href="https://yandex.ru/maps/org/on_time/135200016849/?utm_medium=mapframe&utm_source=maps" style="color:#eee;font-size:12px;position:absolute;top:0px;">On Time</a><a href="https://yandex.ru/maps/10761/chehov/category/watch_shop/184107945/?utm_medium=mapframe&utm_source=maps" style="color:#eee;font-size:12px;position:absolute;top:14px;">Магазин часов в Чехове</a><iframe src="https://yandex.ru/map-widget/v1/?ll=37.468201%2C55.161517&mode=search&oid=135200016849&ol=biz&utm_campaign=desktop&utm_medium=search&utm_source=maps&z=17.35" width="1100" height="500" frameborder="1" allowfullscreen="true" style="position:relative;"></iframe></div>
            <div style="width:560px;height:800px;overflow:hidden;position:relative;"><iframe style="width:100%;height:100%;border:1px solid #e6e6e6;border-radius:8px;box-sizing:border-box" src="https://yandex.ru/maps-reviews-widget/135200016849?comments"></iframe><a href="https://yandex.ru/maps/org/on_time/135200016849/" target="_blank" style="box-sizing:border-box;text-decoration:none;color:#b3b3b3;font-size:10px;font-family:YS Text,sans-serif;padding:0 20px;position:absolute;bottom:8px;width:100%;text-align:center;left:0;overflow:hidden;text-overflow:ellipsis;display:block;max-height:14px;white-space:nowrap;padding:0 16px;box-sizing:border-box">On Time на карте Чехова — Яндекс Карты</a></div>
        <p style="font-size: 20px;">Мы всегда рады ответить на ваши вопросы и помочь с выбором.</p>
    </div>

    <!-- Включение футера -->
    <?php include 'footer.php'; ?>

    <!-- Подключение скриптов -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
</body>
</html>
