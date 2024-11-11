<?php
include 'config.php';
session_start();

// Получаем ID товара из параметра URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Проверяем, передан ли корректный ID
if ($product_id <= 0) {
    echo "Неверный ID товара.";
    exit();
}

// Запрос на получение данных о товаре
$stmt = $conn->prepare("SELECT p.*, b.brand_name FROM products p LEFT JOIN brands b ON p.brand_id = b.brand_id WHERE p.product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    echo "Товар не найден.";
    exit();
}

// Обработка добавления в корзину
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $cart_item = [
        'product_id' => $product['product_id'],
        'model' => $product['model'],
        'price' => $product['price'],
        'quantity' => 1
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Проверяем, есть ли уже этот товар в корзине
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_id'] == $product['product_id']) {
            $item['quantity'] += 1;
            $found = true;
            break;
        }
    }
    unset($item);

    if (!$found) {
        $_SESSION['cart'][] = $cart_item;
    }

    $_SESSION['message'] = "Товар добавлен в корзину!";
    header("Location: product.php?id=" . $product_id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['model']) ?> - On-Time</title>

    <!-- Фавикон (ярлык) -->
    <link rel="icon" href="assets/images/favicon.ico">

    <!-- Подключение Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Подключение вашего собственного CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        /* Контейнер для изображения товара */
        .product-image-container {
            position: sticky;
            top: 20px;
            align-self: flex-start;
        }

        /* Дополнительные стили (если необходимо) */
        /* Убедитесь, что эти стили не конфликтуют с основными стилями сайта */
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <!-- Вывод сообщения об успешном добавлении в корзину -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- Отображение информации о товаре -->
        <div class="row">
            <div class="col-md-6 product-image-container">
                <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['model']) ?>" class="img-fluid product-img">
            </div>
            <div class="col-md-6">
                <h1><?= htmlspecialchars($product['model']) ?></h1>
                <p><strong>Бренд:</strong> <?= htmlspecialchars($product['brand_name']) ?></p>
                <div class="d-flex align-items-center mb-3 price-cart">
                    <!-- Исправлено: удалён неверный атрибут color из тега <strong> -->
                    <p class="mb-0 product-price"><strong>Цена:</strong> <?= number_format($product['price'], 2, ',', ' ') ?> ₽</p>
                    <form method="POST" class="ml-4">
                        <button type="submit" name="add_to_cart" class="btn btn-success">Добавить в корзину</button>
                    </form>
                </div>
                <?php if ($product['quantity'] > 0): ?>
                    <p>В наличии: <?= $product['quantity'] ?> шт.</p>
                <?php else: ?>
                    <p class="text-danger">Товар распродан</p>
                <?php endif; ?>
                <p><?= nl2br(htmlspecialchars($product['full_description'])) ?></p>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Подключение скриптов Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
