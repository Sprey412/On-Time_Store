<?php
include 'config.php';
include 'auth_check.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Добавление товара вручную
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $model = trim($_POST['model']);
    $short_description = trim($_POST['short_description']);
    $full_description = trim($_POST['full_description']);
    $price = floatval($_POST['price']);
    $brand_id = intval($_POST['brand_id']);
    $image_url = trim($_POST['image_url']);
    $quantity = intval($_POST['quantity']);
    $created_at = date('Y-m-d H:i:s');

    // Проверяем, существует ли товар с такой же моделью
    $stmt = $conn->prepare("SELECT product_id FROM products WHERE model = ?");
    $stmt->bind_param("s", $model);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['message'] = "Товар с такой моделью уже существует.";
    } else {
        $insert_sql = "INSERT INTO products (model, short_description, full_description, price, brand_id, image_url, quantity, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("sssdisis", $model, $short_description, $full_description, $price, $brand_id, $image_url, $quantity, $created_at);

        if ($stmt_insert->execute()) {
            $_SESSION['message'] = "Товар успешно добавлен!";
        } else {
            $_SESSION['message'] = "Ошибка при добавлении товара: " . $stmt_insert->error;
        }
    }
    header('Location: add_product.php');
    exit();
}

// Удаление товара
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_product'])) {
    $delete_id = intval($_POST['delete_product']);
    $stmt_delete = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt_delete->bind_param("i", $delete_id);
    if ($stmt_delete->execute()) {
        $_SESSION['message'] = "Товар успешно удален!";
    } else {
        $_SESSION['message'] = "Ошибка при удалении товара: " . $stmt_delete->error;
    }
    header('Location: add_product.php');
    exit();
}

// Получение списка брендов
$brands_result = $conn->query("SELECT * FROM brands");

// Получение списка товаров
$products_result = $conn->query("SELECT p.*, b.brand_name FROM products p LEFT JOIN brands b ON p.brand_id = b.brand_id");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление товарами</title>

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
            margin-bottom: 50px;
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
        .btn-secondary {
            background-color: #99aab5;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        /* Удаление теней у изображений */
        .card-img-top {
            max-height: 200px;
            object-fit: cover;
            box-shadow: none;
        }
        .alert-success {
            background-color: #7289da;
            color: #ffffff;
            border: none;
        }
        .btn-group .btn {
            margin-right: 10px;
        }
        /* Таблица продуктов */
        .product-table {
            width: 100%;
            color: #ffffff;
            border-collapse: collapse;
        }
        .product-table th, .product-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #99aab5;
        }
        .product-table th {
            background-color: #3a3f44;
        }
        .product-table tr:hover {
            background-color: #3a3f44;
        }
        .action-buttons button {
            margin-right: 5px;
        }
        /* Стиль модальных окон */
        .modal-content {
            background-color: #23272a; /* Темный фон модального окна */
            color: #ffffff; /* Белый текст */
        }
        .modal-content .form-control {
            background-color: #3a3f44; /* Темный фон полей ввода */
            color: #ffffff; /* Белый текст */
            border: 1px solid #99aab5; /* Светлая граница */
        }
        .modal-content .form-control::placeholder {
            color: #99aab5; /* Цвет плейсхолдера */
        }
        .modal-content label {
            color: #ffffff; /* Белый цвет меток */
        }
        .modal-content .close {
            color: #ffffff; /* Белый крестик закрытия */
        }
        /* Адаптивность таблицы */
        @media (max-width: 768px) {
            .product-table thead {
                display: none;
            }
            .product-table, .product-table tbody, .product-table tr, .product-table td {
                display: block;
                width: 100%;
            }
            .product-table tr {
                margin-bottom: 15px;
            }
            .product-table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            .product-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 15px;
                font-weight: bold;
                text-align: left;
            }
            .action-buttons button {
                width: 48%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mb-4">Управление товарами</h1>
        <div class="btn-group mb-4" role="group">
            <a href="dashboard.php" class="btn btn-primary">Личный кабинет</a>
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

        <!-- Кнопка для добавления товара вручную -->
        <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#addProductModal">
            Добавить товар вручную
        </button>

        <!-- Кнопка для загрузки файла Excel -->
        <button type="button" class="btn btn-info mb-3" data-toggle="modal" data-target="#uploadExcelModal">
            Загрузить из Excel
        </button>

        <!-- Модальное окно для добавления товара вручную -->
        <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Добавить товар вручную</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="add_product.php" method="POST">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="model">Модель</label>
                                <input type="text" class="form-control" name="model" required>
                            </div>
                            <div class="form-group">
                                <label for="short_description">Краткое описание</label>
                                <input type="text" class="form-control" name="short_description" required>
                            </div>
                            <div class="form-group">
                                <label for="full_description">Полное описание</label>
                                <textarea class="form-control" name="full_description" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="price">Цена</label>
                                <input type="number" step="0.01" class="form-control" name="price" required>
                            </div>
                            <div class="form-group">
                                <label for="brand_id">Бренд</label>
                                <select name="brand_id" class="form-control" required>
                                    <?php
                                    while ($brand = $brands_result->fetch_assoc()):
                                    ?>
                                        <option value="<?= $brand['brand_id'] ?>"><?= htmlspecialchars($brand['brand_name']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="image_url">URL изображения</label>
                                <input type="text" class="form-control" name="image_url" required>
                            </div>
                            <div class="form-group">
                                <label for="quantity">Количество</label>
                                <input type="number" class="form-control" name="quantity" value="1" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                            <button type="submit" name="add_product" class="btn btn-primary">Добавить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Модальное окно для загрузки файла Excel -->
        <div class="modal fade" id="uploadExcelModal" tabindex="-1" role="dialog" aria-labelledby="uploadExcelModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadExcelModalLabel">Загрузить товары из Excel</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="upload_excel.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="excel_file">Выберите Excel-файл (.xlsx, .xls):</label>
                                <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xlsx, .xls" required>
                            </div>
                            <!-- Кнопка подсказки -->
                            <button type="button" class="btn btn-info mt-2" data-toggle="modal" data-target="#excelGuideModal">
                                Инструкция по оформлению Excel-файла
                            </button>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                            <button type="submit" class="btn btn-primary">Загрузить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Модальное окно с инструкцией по оформлению Excel-файла -->
        <div class="modal fade" id="excelGuideModal" tabindex="-1" role="dialog" aria-labelledby="excelGuideModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="excelGuideModalLabel">Инструкция по оформлению Excel-файла</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Пожалуйста, оформите ваш Excel-файл следующим образом:</p>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Столбец 1</th>
                                    <th>Столбец 2</th>
                                    <th>Столбец 3</th>
                                    <th>Столбец 4</th>
                                    <th>Столбец 5</th>
                                    <th>Столбец 6</th>
                                    <th>Столбец 7</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Модель</td>
                                    <td>Краткое описание</td>
                                    <td>Полное описание</td>
                                    <td>Цена</td>
                                    <td>Название бренда</td>
                                    <td>URL изображения</td>
                                    <td>Количество</td>
                                </tr>
                            </tbody>
                        </table>
                        <p>Первая строка должна содержать заголовки столбцов, как указано выше. Начните ввод данных со второй строки.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Отображение списка товаров в виде таблицы -->
        <h2 class="mb-3">Список товаров</h2>
        <?php if ($products_result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="product-table table">
                    <thead>
                        <tr>
                            <th>Ключ</th>
                            <th>Наименование</th>
                            <th>Кол-во</th>
                            <th>Цена</th>
                            <th>Бренд</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($product = $products_result->fetch_assoc()): ?>
                            <tr>
                                <td data-label="Ключ"><?= $product['product_id'] ?></td>
                                <td data-label="Наименование"><?= htmlspecialchars($product['model']) ?></td>
                                <td data-label="Кол-во"><?= $product['quantity'] ?></td>
                                <td data-label="Цена"><?= number_format($product["price"], 2, ',', ' ') ?> ₽</td>
                                <td data-label="Бренд"><?= htmlspecialchars($product['brand_name']) ?></td>
                                <td data-label="Действия" class="action-buttons">
                                    <!-- Кнопка редактирования -->
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editProductModal<?= $product['product_id'] ?>">
                                        Изменить
                                    </button>

                                    <!-- Кнопка удаления -->
                                    <form action="add_product.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_product" value="<?= $product['product_id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить этот товар?');">
                                            Удалить
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Модальное окно для редактирования товара -->
                            <div class="modal fade" id="editProductModal<?= $product['product_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel<?= $product['product_id'] ?>" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editProductModalLabel<?= $product['product_id'] ?>">Изменить товар</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="edit_product.php" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                                <div class="form-group">
                                                    <label for="model<?= $product['product_id'] ?>">Модель</label>
                                                    <input type="text" class="form-control" name="model" id="model<?= $product['product_id'] ?>" value="<?= htmlspecialchars($product['model']) ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="short_description<?= $product['product_id'] ?>">Краткое описание</label>
                                                    <input type="text" class="form-control" name="short_description" id="short_description<?= $product['product_id'] ?>" value="<?= htmlspecialchars($product['short_description']) ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="full_description<?= $product['product_id'] ?>">Полное описание</label>
                                                    <textarea class="form-control" name="full_description" id="full_description<?= $product['product_id'] ?>" required><?= htmlspecialchars($product['full_description']) ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="price<?= $product['product_id'] ?>">Цена</label>
                                                    <input type="number" step="0.01" class="form-control" name="price" id="price<?= $product['product_id'] ?>" value="<?= $product['price'] ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="brand_id<?= $product['product_id'] ?>">Бренд</label>
                                                    <select name="brand_id" class="form-control" id="brand_id<?= $product['product_id'] ?>" required>
                                                        <?php
                                                        $brands_result_modal = $conn->query("SELECT * FROM brands");
                                                        while ($brand_modal = $brands_result_modal->fetch_assoc()):
                                                        ?>
                                                            <option value="<?= $brand_modal['brand_id'] ?>" <?= $product['brand_id'] == $brand_modal['brand_id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($brand_modal['brand_name']) ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="image_url<?= $product['product_id'] ?>">URL изображения</label>
                                                    <input type="text" class="form-control" name="image_url" id="image_url<?= $product['product_id'] ?>" value="<?= htmlspecialchars($product['image_url']) ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="quantity<?= $product['product_id'] ?>">Количество</label>
                                                    <input type="number" class="form-control" name="quantity" id="quantity<?= $product['product_id'] ?>" value="<?= $product['quantity'] ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                                                <button type="submit" name="edit_product" class="btn btn-primary">Сохранить изменения</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        <?php endwhile; ?>
                    </tbody>
                </table>
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
