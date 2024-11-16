<?php
include 'config.php';
include 'auth_check.php'; // Проверка авторизации

// Обработка добавления нового бренда
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_brand'])) {
    $brand_name = trim($_POST['brand_name']);

    if (empty($brand_name)) {
        $error_message = "Название бренда не может быть пустым.";
    } else {
        // Проверяет, существует ли бренд с таким же названием
        $stmt = $conn->prepare("SELECT brand_id FROM brands WHERE brand_name = ?");
        $stmt->bind_param("s", $brand_name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "Бренд с таким названием уже существует.";
        } else {
            // Вставка нового бренда
            $insert_sql = "INSERT INTO brands (brand_name) VALUES (?)";
            $stmt_insert = $conn->prepare($insert_sql);
            $stmt_insert->bind_param("s", $brand_name);

            if ($stmt_insert->execute()) {
                $_SESSION['message'] = "Бренд успешно добавлен!";
            } else {
                $error_message = "Ошибка: " . $stmt_insert->error;
            }

            $stmt_insert->close();
        }

        $stmt->close();
    }

    header("Location: manage_brands.php");
    exit();
}

// Обработка удаления бренда
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_brand_id'])) {
    $brand_id = intval($_POST['delete_brand_id']);

    // Проверяет, связаны ли товары с этим брендом
    $stmt_check = $conn->prepare("SELECT product_id FROM products WHERE brand_id = ?");
    $stmt_check->bind_param("i", $brand_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $_SESSION['message'] = "Невозможно удалить бренд, так как он связан с товарами.";
    } else {
        // Удаление бренда
        $delete_sql = "DELETE FROM brands WHERE brand_id = ?";
        $stmt_delete = $conn->prepare($delete_sql);
        $stmt_delete->bind_param("i", $brand_id);

        if ($stmt_delete->execute()) {
            $_SESSION['message'] = "Бренд успешно удалён!";
        } else {
            $_SESSION['message'] = "Ошибка: " . $stmt_delete->error;
        }

        $stmt_delete->close();
    }

    $stmt_check->close();
    header("Location: manage_brands.php");
    exit();
}

// Получает список брендов для отображения
$brands_sql = "SELECT brand_id, brand_name FROM brands ORDER BY brand_name ASC";
$brands_result = $conn->query($brands_sql);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление Брендами - On-Time</title>

    <!-- Фавикон (ярлык) -->
    <link rel="icon" href="assets/images/favicon.ico">

    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- Подключение Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
        .btn-secondary {
            background-color: #99aab5;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        .table {
            background-color: #2c2f33; /* Темный фон таблицы */
            color: #ffffff; /* Белый текст */
        }
        .table thead {
            background-color: #3a3f44; /* Темный фон заголовков таблицы */
            color: #ffffff; /* Белый текст заголовков */
        }
        .table tbody tr {
            background-color: #2c2f33; /* Темный фон строк таблицы */
            color: #ffffff; /* Белый текст */
        }
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
        .alert-success {
            background-color: #7289da;
            color: #ffffff;
            border: none;
        }
        .alert-danger {
            background-color: #f04747;
            color: #ffffff;
            border: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mb-4">Управление брендами</h1>
        <div class="btn-group mb-4" role="group">
            <a href="dashboard.php" class="btn btn-primary">Личный кабинет</a>
            <a href="add_product.php" class="btn btn-primary">Управление товарами</a>
            <a href="logout.php" class="btn btn-danger">Выход</a>
            <a href="start.php" class="btn btn-secondary">На главную</a> <!-- Кнопка "На главную" -->
        </div>

        <!-- Вывод сообщений -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['message']) ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <!-- Кнопка для добавления бренда -->
        <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#addBrandModal">
            Добавить Бренд
        </button>

        <!-- Таблица брендов -->
        <?php if ($brands_result->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название Бренда</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $brands_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['brand_id']) ?></td>
                            <td><?= htmlspecialchars($row['brand_name']) ?></td>
                            <td>
                                <!-- Кнопка удаления бренда -->
                                <form action="manage_brands.php" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить этот бренд?');">
                                    <input type="hidden" name="delete_brand_id" value="<?= $row['brand_id'] ?>">
                                    <button type="submit" class="btn btn-danger">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Бренды не найдены.</p>
        <?php endif; ?>

        <!-- Модальное окно для добавления бренда -->
        <div class="modal fade" id="addBrandModal" tabindex="-1" role="dialog" aria-labelledby="addBrandModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addBrandModalLabel">Добавить Бренд</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="manage_brands.php" method="POST">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="brand_name">Название Бренда</label>
                                <input type="text" class="form-control" name="brand_name" placeholder="Введите название бренда" required>
                            </div>
                            <small class="form-text text-muted">Пожалуйста, введите уникальное название бренда.</small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                            <button type="submit" name="add_brand" class="btn btn-primary">Добавить Бренд</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Конец модального окна -->

    </div>

    <!-- Подключение скриптов -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <!-- Подключение Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
