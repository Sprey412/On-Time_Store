<?php
// Подключение файла конфигурации
include 'config.php';

// Обработка фильтров
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$brand_filter = isset($_GET['brand']) ? $_GET['brand'] : '';
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : '';

// Получение параметров фильтров
$filters = [
    'glass_type' => isset($_GET['glass_type']) ? $_GET['glass_type'] : [],
    'strap_type' => isset($_GET['strap_type']) ? $_GET['strap_type'] : [],
    'movement_type' => isset($_GET['movement_type']) ? $_GET['movement_type'] : [],
    'power_source' => isset($_GET['power_source']) ? $_GET['power_source'] : [],
    'gender' => isset($_GET['gender']) ? $_GET['gender'] : [],
    'water_resistance' => isset($_GET['water_resistance']) ? $_GET['water_resistance'] : [],
    'watch_type' => isset($_GET['watch_type']) ? $_GET['watch_type'] : [],
    'other_functions' => isset($_GET['other_functions']) ? $_GET['other_functions'] : [],
];

// Инициализация параметров для запроса
$params = [];
$types = '';
$sql = "SELECT * FROM products WHERE quantity > 0"; // Показываем только товары с количеством больше 0

// Формирование условий поиска и фильтрации
$conditions = [];

if ($search_query) {
    $conditions[] = "(model LIKE ? OR short_description LIKE ? OR full_description LIKE ?)";
    $search_param = '%' . $search_query . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

if ($brand_filter) {
    $conditions[] = "brand_id = (SELECT brand_id FROM brands WHERE brand_name = ?)";
    $params[] = $brand_filter;
    $types .= 's';
}

// Обработка дополнительных фильтров
foreach ($filters as $key => $values) {
    if (!empty($values)) {
        // Обработка пола (гендера) отдельно для учета "Унисекс"
        if ($key === 'gender') {
            $gender_conditions = [];
            foreach ($values as $gender) {
                $gender_conditions[] = "full_description LIKE ?";
                $params[] = '%' . $gender . '%';
                $types .= 's';

                // Добавляем условие для "Унисекс"
                $gender_conditions[] = "full_description LIKE ?";
                $params[] = '%Унисекс%';
                $types .= 's';
            }
            $conditions[] = '(' . implode(' OR ', $gender_conditions) . ')';
        } else {
            $filter_conditions = [];
            foreach ($values as $value) {
                $filter_conditions[] = "full_description LIKE ?";
                $params[] = '%' . $value . '%';
                $types .= 's';
            }
            $conditions[] = '(' . implode(' OR ', $filter_conditions) . ')';
        }
    }
}

// Если есть условия, добавляем их в запрос
if (!empty($conditions)) {
    $sql .= ' AND ' . implode(' AND ', $conditions);
}

// Сортировка
switch ($sort_order) {
    case 'price_asc':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY price DESC";
        break;
    case 'alphabetical':
        $sql .= " ORDER BY model ASC";
        break;
    default:
        break;
}

// Подготовка и выполнение запроса
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Ошибка подготовки запроса: ' . htmlspecialchars($conn->error));
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Каталог</title>

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
        <h1 class="text-center mb-4">Каталог</h1>

        <!-- Форма поиска -->
        <form method="GET" class="form-inline mb-4 justify-content-center">
            <input type="text" name="search" class="form-control mr-2" placeholder="Поиск..." value="<?= htmlspecialchars($search_query) ?>">
            <button type="submit" class="btn btn-primary search-btn">Поиск</button>
        </form>

        <!-- Форма фильтров -->
        <form method="GET" class="mb-4">
            <!-- Фильтр по брендам -->
            <div class="form-group">
                <label for="brand"><strong>Бренд</strong></label>
                <select name="brand" class="form-control brand-select" id="brand">
                    <option value="">Все бренды</option>
                    <?php
                    // Получение брендов из базы данных
                    $brands_sql = "SELECT brand_name FROM brands";
                    $brands_result = $conn->query($brands_sql);
                    while ($brand = $brands_result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($brand['brand_name']) ?>" <?= $brand_filter === $brand['brand_name'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($brand['brand_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Разделение фильтров на группы -->
            <div class="card mb-3">
                <div class="card-header">
                    <strong>Характеристики</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Тип стекла -->
                        <div class="col-md-3">
                            <h5>Тип стекла</h5>
                            <?php
                            $glass_types = ['Сапфировое', 'Минеральное', 'Акриловое'];
                            foreach ($glass_types as $type): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="glass_type[]" value="<?= $type ?>" <?= in_array($type, $filters['glass_type']) ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?= $type ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Тип ремешка -->
                        <div class="col-md-3">
                            <h5>Тип ремешка</h5>
                            <?php
                            $strap_types = ['Металлический', 'Кожаный', 'Резиновый', 'Тканый'];
                            foreach ($strap_types as $type): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="strap_type[]" value="<?= $type ?>" <?= in_array($type, $filters['strap_type']) ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?= $type ?> ремешок</label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Тип механизма -->
                        <div class="col-md-3">
                            <h5>Тип механизма</h5>
                            <?php
                            $movement_types = ['Кварцевый', 'Механический', 'Механический с автоподзаводом'];
                            foreach ($movement_types as $type): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="movement_type[]" value="<?= $type ?>" <?= in_array($type, $filters['movement_type']) ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?= $type ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Элемент питания -->
                        <div class="col-md-3">
                            <h5>Элемент питания</h5>
                            <?php
                            $power_sources = ['Батарейка', 'Солнечный аккумулятор'];
                            foreach ($power_sources as $source): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="power_source[]" value="<?= $source ?>" <?= in_array($source, $filters['power_source']) ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?= $source ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Пол -->
                        <div class="col-md-3">
                            <h5>Пол</h5>
                            <?php
                            $genders = ['Мужские', 'Женские'];
                            foreach ($genders as $gender): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="gender[]" value="<?= $gender ?>" <?= in_array($gender, $filters['gender']) ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?= $gender ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Водозащита -->
                        <div class="col-md-3">
                            <h5>Водозащита</h5>
                            <?php
                            $water_resistances = ['Отсутствует', '3-BAR', '5-BAR', '10-BAR', '20-BAR'];
                            foreach ($water_resistances as $resistance): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="water_resistance[]" value="<?= $resistance ?>" <?= in_array($resistance, $filters['water_resistance']) ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?= $resistance ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Тип часов -->
                        <div class="col-md-3">
                            <h5>Тип часов</h5>
                            <?php
                            $watch_types = ['Спортивные', 'Полуспортивные', 'Классические', 'Детские', 'Настенные', 'Настольные', 'Карманные'];
                            foreach ($watch_types as $type): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="watch_type[]" value="<?= $type ?>" <?= in_array($type, $filters['watch_type']) ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?= $type ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Другие функции -->
                        <div class="col-md-3">
                            <h5>Другие функции</h5>
                            <?php
                            $other_functions = ['Хронограф', 'Bluetooth', 'Шагомер', 'Записная книжка'];
                            foreach ($other_functions as $function): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="other_functions[]" value="<?= $function ?>" <?= in_array($function, $filters['other_functions']) ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?= $function ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Сортировка и кнопки -->
            <div class="form-group">
                <label for="sort"><strong>Сортировка</strong></label>
                <select name="sort" class="form-control" id="sort">
                    <option value="">По умолчанию</option>
                    <option value="price_asc" <?= $sort_order === 'price_asc' ? 'selected' : '' ?>>Цена: по возрастанию</option>
                    <option value="price_desc" <?= $sort_order === 'price_desc' ? 'selected' : '' ?>>Цена: по убыванию</option>
                    <option value="alphabetical" <?= $sort_order === 'alphabetical' ? 'selected' : '' ?>>По алфавиту</option>
                </select>
            </div>

            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary mr-2 search-btn">Применить фильтры</button>
                <a href="catalog.php" class="btn btn-primary search-btn">Сбросить фильтры</a>
            </div>
        </form>

        <!-- Список товаров -->
        <div class="product-grid">
            <?php
            // Проверяем, есть ли товары в базе данных
            if ($result->num_rows > 0) {
                // Перебираем все товары и выводим их
                while($row = $result->fetch_assoc()) {
                    echo '<a href="product.php?id=' . $row['product_id'] . '" class="product-card">';
                    echo '<img src="' . htmlspecialchars($row["image_url"]) . '" alt="' . htmlspecialchars($row["model"]) . '" class="product-img">';
                    echo '<h3 class="product-title">' . htmlspecialchars($row["model"]) . '</h3>';
                    echo '<p class="product-desc">' . htmlspecialchars($row["short_description"]) . '</p>';
                    echo '<span class="product-price">' . number_format($row["price"], 2, ',', ' ') . ' ₽</span>';
                    echo '</a>';
                }
            } else {
                echo '<p>Товары не найдены</p>';
            }
            ?>
        </div>
    </div>

    <!-- Включение футера -->
    <?php include 'footer.php'; ?>

    <!-- Подключение скриптов -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <!-- Подключение Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
