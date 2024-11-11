<?php
include 'config.php';
session_start();

// Обработка удаления товара из корзины
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove'])) {
    $remove_id = intval($_POST['remove']);
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['product_id'] == $remove_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    // Перенаправление для предотвращения повторной отправки формы
    header("Location: cart.php");
    exit();
}

// Обработка изменения количества товаров
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity'])) {
    $update_id = intval($_POST['update_quantity']);
    $new_quantity = intval($_POST['quantity']);
    if ($new_quantity > 0) {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $update_id) {
                $item['quantity'] = $new_quantity;
                break;
            }
        }
        unset($item);
    }
    header("Location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Корзина - On-Time</title>

    <!-- Фавикон (ярлык) -->
    <link rel="icon" href="assets/images/favicon.ico">

    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- Подключение Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Ваша корзина</h1>

        <?php if (!empty($_SESSION['cart'])): ?>
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Товар</th>
                        <th>Цена за шт.</th>
                        <th>Количество</th>
                        <th>Итого</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($_SESSION['cart'] as $item):
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['model']) ?></td>
                            <td><?= number_format($item['price'], 2, ',', ' ') ?> ₽</td>
                            <td>
                                <form method="POST" class="form-inline">
                                    <input type="hidden" name="update_quantity" value="<?= $item['product_id'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control mr-2" style="width: 80px;">
                                    <button type="submit" class="btn btn-primary">Обновить</button>
                                </form>
                            </td>
                            <td><?= number_format($subtotal, 2, ',', ' ') ?> ₽</td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="remove" value="<?= $item['product_id'] ?>">
                                    <button type="submit" class="btn btn-danger">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Всего:</strong></td>
                        <td colspan="2"><strong><?= number_format($total, 2, ',', ' ') ?> ₽</strong></td>
                    </tr>
                </tbody>
            </table>
            <div class="text-center">
                <a href="checkout.php" class="btn btn-success">Оформить заказ</a>
            </div>
        <?php else: ?>
            <p class="text-center">Ваша корзина пуста.</p>
            <div class="text-center">
                <a href="catalog.php" class="btn btn-primary">Перейти к покупкам</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Подключение скриптов Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
