<?php
// Начало сессии для работы с корзиной
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Функция для подсчёта общего количества товаров в корзине
function getCartCount() {
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        return array_sum(array_column($_SESSION['cart'], 'quantity'));
    }
    return 0;
}
?>

<!-- Контейнер для верхней части страницы (sticky для фиксации) -->
<div class="container-fluid sticky-top header_style">
    <!-- Навигационная панель Bootstrap -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <!-- Логотип сайта -->
        <a href="Start.php" class="navbar-brand">
            <img src="assets/images/ontime_logo_calibri.png" alt="лого сайта" style="max-width: 263px; height: 40px;">
        </a>
        <!-- Кнопка-тоглер для мобильных устройств -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" 
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Ссылки навигации внутри выпадающего меню -->
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav mr-auto">
                <!-- Навигационные ссылки -->
                <li class="nav-item">
                    <a href="catalog.php" class="nav-link toplink">Каталог</a>
                </li>
                <li class="nav-item">
                    <a href="delivery.php" class="nav-link toplink">Доставка и оплата</a>
                </li>
                <li class="nav-item">
                    <a href="contacts.php" class="nav-link toplink">Контакты</a>
                </li>
            </ul>
        </div>
        <!-- Поиск и корзина всегда видимы -->
        <form class="form-inline my-2 my-lg-0" action="catalog.php" method="GET">
            <input class="form-control mr-2 search-input" type="search" placeholder="Поиск" aria-label="Поиск" name="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button class="btn btn-success search-btn" type="submit">Найти</button>
        </form>
        <a href="cart.php" class="btn btn-outline-primary ml-3">
            <i class="fas fa-shopping-cart"></i> Корзина (<?= getCartCount(); ?>)
        </a>
    </nav>
</div>
