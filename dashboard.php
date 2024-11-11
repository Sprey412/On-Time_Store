<?php
include 'config.php';
include 'auth_check.php';

// Получение информации о пользователе для отображения в приветствии
$stmt = $conn->prepare("SELECT email FROM employees WHERE employee_id = ?");
$stmt->bind_param("i", $_SESSION['employee_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Получение списка товаров из базы данных
$products_sql = "SELECT p.*, b.brand_name FROM products p LEFT JOIN brands b ON p.brand_id = b.brand_id";
$products_result = $conn->query($products_sql);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет - On-Time</title>

    <!-- Фавикон (ярлык) -->
    <link rel="icon" href="assets/images/favicon.ico">

    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- Подключение Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Темная тема для страницы */
        body {
            background-color: #2c2f33; /* Темный фон страницы */
            color: #ffffff; /* Белый текст */
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .btn-primary {
            background-color: #7289da;
            border: none;
        }
        .btn-primary:hover {
            background-color: #5b6eae;
        }
        .btn-danger {
            background-color: #99aab5;
            border: none;
        }
        .btn-danger:hover {
            background-color: #7f8c8d;
        }
        .card {
            background-color: #23272a; /* Темный фон карточек */
            color: #ffffff; /* Белый текст */
            border: 1px solid #99aab5;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .card-img-top {
            max-height: 200px;
            object-fit: cover;
        }
        .product-title {
            font-size: 1.25rem;
            margin-top: 10px;
        }
        .product-price {
            font-weight: bold;
            color: #7289da;
        }
        .alert-success {
            background-color: #7289da;
            color: #ffffff;
            border: none;
        }
        .btn-group .btn {
            margin-right: 10px;
        }
        .product-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
        }
        .product-item {
            width: 23%;
            margin: 1%;
            box-sizing: border-box;
        }
        @media (max-width: 992px) {
            .product-item {
                width: 48%;
            }
        }
        @media (max-width: 576px) {
            .product-item {
                width: 98%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mb-4">Добро пожаловать, <?= htmlspecialchars($user['email']) ?>!</h1>
        <div class="btn-group mb-4" role="group">
            <a href="add_product.php" class="btn btn-primary">Управление товарами</a>
            <a href="manage_brands.php" class="btn btn-primary">Управление брендами</a>
            <a href="logout.php" class="btn btn-danger">Выход</a>
        </div>

        <!-- Вывод сообщений -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['message']) ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Отображение списка товаров -->
        <h2 class="mb-3">Список товаров</h2>
        <?php if ($products_result->num_rows > 0): ?>
            <div class="product-list">
                <?php while ($product = $products_result->fetch_assoc()): ?>
                    <div class="product-item">
                        <div class="card h-100">
                            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['model']) ?>" class="card-img-top">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title product-title"><?= htmlspecialchars($product['model']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($product['short_description']) ?></p>
                                <p class="product-price mt-auto"><?= number_format($product['price'], 2, ',', ' ') ?> ₽</p>
                                <a href="product.php?id=<?= $product['product_id'] ?>" class="btn btn-primary mt-2">Подробнее</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>Товары не найдены.</p>
        <?php endif; ?>
    </div>

    <!-- Подключение скриптов -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <!-- Подключение Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
