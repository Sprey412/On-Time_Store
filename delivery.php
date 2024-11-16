<?php
// Подключение файла конфигурации
include 'config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Доставка и оплата - On-Time</title>

    <!-- Фавикон (ярлык) -->
    <link rel="icon" href="assets/images/favicon.ico">

    <!-- Подключение Bootstrap и других ресурсов -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <!-- Включение хедера -->
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Доставка и оплата</h1>
        <h2 style="font-size: 24px;">Способы доставки</h2>
        <ul style="font-size: 20px;">
            <li>Мы доставляем часы по городу Чехов. Стоимость доставки зависит от товара.
            Вы можете выбрать доставку курьером или самовывоз.
            Когда оформите заказ, наш менеджер свяжется с вами и сообщит стоимость, а также уточнит детали доставки.
            Срок доставки - 1 до 5 дней после оформления заказа. Подробности обсуждаются по телефону с курьером.</li>
            <p></p>
            <li>Самовывоз доступен сразу после оформления заказа из нашего магазина по адресу: г. Чехов, Симферопольское шоссе, 1</li>
        </ul>
        <h2 style="font-size: 24px;">Способы оплаты</h2>
        <ul style="font-size: 20px;">
            <li>Наличными или банковской картой при получении</li>
            <li>Банковской картой онлайн</li>
            <li>Вы можете купить товар в кредит или рассрочку от Тинькофф.
            Это можно сделать при оформлении заказа. Вероятность одобрения — от 95%.
            Если вы клиент Тинькофф, понадобятся только ФИО и телефон.
            Банк сам заполнит заявку — вам останется только проверить
            и отправить.
            Время рассмотрения заявки — две минуты. Решение от банка появится прямо в форме заявки на странице оформления заказа.</li>
        </ul>
        <p style="font-size: 20px;">При возникновении вопросов свяжитесь с нашей службой поддержки.</p>
    </div>

    <!-- Включение футера -->
    <?php include 'footer.php'; ?>

    <!-- Подключение скриптов -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
</body>
</html>
