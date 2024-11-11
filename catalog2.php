<?php
// Подключение файла конфигурации
include 'config.php';

// Обработка фильтров
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$brand_filter = isset($_GET['brand']) ? $_GET['brand'] : '';
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : '';

// Получение значений фильтров
$filters = [
    'glass_type' => isset($_GET['glass_type']) ? $_GET['glass_type'] : '',
    'strap_type' => isset($_GET['strap_type']) ? $_GET['strap_type'] : '',
    'movement_type' => isset($_GET['movement_type']) ? $_GET['movement_type'] : '',
    'power_source' => isset($_GET['power_source']) ? $_GET['power_source'] : '',
    'gender' => isset($_GET['gender']) ? $_GET['gender'] : '',
    'water_resistance' => isset($_GET['water_resistance']) ? $_GET['water_resistance'] : '',
    'watch_type' => isset($_GET['watch_type']) ? $_GET['watch_type'] : '',
    'additional_features' => isset($_GET['additional_features']) ? $_GET['additional_features'] : [],
];

// Инициализация параметров для запроса
$params = [];
$types = '';
$sql = "SELECT * FROM products WHERE quantity > 0";

// Формирование условий поиска и фильтрации
if ($search_query) {
    $sql .= " AND (model LIKE ? OR short_description LIKE ? OR full_description LIKE ?)";
    $search_param = '%' . $search_query . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

if ($brand_filter) {
    $sql .= " AND brand_id = (SELECT brand_id FROM brands WHERE brand_name = ?)";
    $params[] = $brand_filter;
    $types .= 's';
}

// Добавление фильтров по характеристикам
foreach ($filters as $key => $value) {
    if ($value) {
        if ($key === 'additional_features') {
            if (is_array($value) && count($value) > 0) {
                $feature_conditions = [];
                foreach ($value as $feature) {
                    $sql .= " AND full_description LIKE ?";
                    $params[] = '%' . $feature . '%';
                    $types .= 's';
                }
            }
        } else {
            $sql .= " AND full_description LIKE ?";
            $params[] = '%' . $value . '%';
            $types .= 's';
        }
    }
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

    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- Подключение Bootstrap и других ресурсов -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>

<body>
    <!-- Включение хедера -->
    <?php include 'header.php'; ?>

    <div class="white-container">
        <div class="container">
            <h1 class="sales-text text-center">Каталог</h1>

            <!-- Форма поиска -->
            <form method="GET" class="form-inline mb-4 justify-content-center">
                <input type="text" name="search" class="form-control mr-2" placeholder="Поиск..." value="<?= htmlspecialchars($search_query) ?>">
                <button type="submit" class="btn btn-primary">Поиск</button>
            </form>

            <!-- Фильтры -->
            <form method="GET" class="mb-4">
                <div class="row">
                    <!-- Бренд -->
                    <div class="col-md-3">
                        <label for="brand">Бренд:</label>
                        <select name="brand" id="brand" class="form-control">
                            <option value="">Все бренды</option>
                            <?php
                            $brands_sql = "SELECT brand_name FROM brands";
                            $brands_result = $conn->query($brands_sql);
                            while ($brand = $brands_result->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($brand['brand_name']) ?>" <?= $brand_filter === $brand['brand_name'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars(ucfirst($brand['brand_name'])) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Тип стекла -->
                    <div class="col-md-3">
                        <label for="glass_type">Тип стекла:</label>
                        <select name="glass_type" id="glass_type" class="form-control">
                            <option value="">Все</option>
                            <option value="Сапфировое" <?= $filters['glass_type'] === 'Сапфировое' ? 'selected' : '' ?>>Сапфировое</option>
                            <option value="Минеральное" <?= $filters['glass_type'] === 'Минеральное' ? 'selected' : '' ?>>Минеральное</option>
                            <option value="Акриловое" <?= $filters['glass_type'] === 'Акриловое' ? 'selected' : '' ?>>Акриловое</option>
                        </select>
                    </div>

                    <!-- Тип ремешка -->
                    <div class="col-md-3">
                        <label for="strap_type">Тип ремешка:</label>
                        <select name="strap_type" id="strap_type" class="form-control">
                            <option value="">Все</option>
                            <option value="Металлический" <?= $filters['strap_type'] === 'Металлический' ? 'selected' : '' ?>>Металлический браслет</option>
                            <option value="Кожаный" <?= $filters['strap_type'] === 'Кожаный' ? 'selected' : '' ?>>Кожаный ремешок</option>
                            <option value="Резиновый" <?= $filters['strap_type'] === 'Резиновый' ? 'selected' : '' ?>>Резиновый ремешок</option>
                            <option value="Тканевый" <?= $filters['strap_type'] === 'Тканевый' ? 'selected' : '' ?>>Тканевый ремешок</option>
                        </select>
                    </div>

                    <!-- Тип механизма -->
                    <div class="col-md-3">
                        <label for="movement_type">Тип механизма:</label>
                        <select name="movement_type" id="movement_type" class="form-control">
                            <option value="">Все</option>
                            <option value="Кварцевый" <?= $filters['movement_type'] === 'Кварцевый' ? 'selected' : '' ?>>Кварцевый</option>
                            <option value="Механический" <?= $filters['movement_type'] === 'Механический' ? 'selected' : '' ?>>Механический</option>
                            <option value="Механический с автоподзаводом" <?= $filters['movement_type'] === 'Механический с автоподзаводом' ? 'selected' : '' ?>>Механический с автоподзаводом</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <!-- Элемент питания -->
                    <div class="col-md-3">
                        <label for="power_source">Элемент питания:</label>
                        <select name="power_source" id="power_source" class="form-control">
                            <option value="">Все</option>
                            <option value="Батарейка" <?= $filters['power_source'] === 'Батарейка' ? 'selected' : '' ?>>Батарейка</option>
                            <option value="Солнечный аккумулятор" <?= $filters['power_source'] === 'Солнечный аккумулятор' ? 'selected' : '' ?>>Солнечный аккумулятор</option>
                        </select>
                    </div>

                    <!-- Пол -->
                    <div class="col-md-3">
                        <label for="gender">Пол:</label>
                        <select name="gender" id="gender" class="form-control">
                            <option value="">Все</option>
                            <option value="Мужские" <?= $filters['gender'] === 'Мужские' ? 'selected' : '' ?>>Мужские</option>
                            <option value="Женские" <?= $filters['gender'] === 'Женские' ? 'selected' : '' ?>>Женские</option>
                            <option value="Унисекс" <?= $filters['gender'] === 'Унисекс' ? 'selected' : '' ?>>Унисекс</option>
                        </select>
                    </div>

                    <!-- Водозащита -->
                    <div class="col-md-3">
                        <label for="water_resistance">Водозащита:</label>
                        <select name="water_resistance" id="water_resistance" class="form-control">
                            <option value="">Все</option>
                            <option value="Отсутствует"
