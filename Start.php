<?php
// Подключение файла конфигурации
include 'config.php';

// SQL-запрос для получения данных товаров
$sql = "SELECT model, short_description, price, image_url FROM products";
$result = $conn->query($sql);

function renderProductCard($conn, $productId) {
  // Запрос к базе данных для получения информации о товаре по ID
  $sql = "SELECT * FROM products WHERE product_id = ?";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
      return '<div class="product-card">Ошибка подготовки запроса</div>';
  }
  $stmt->bind_param("i", $productId);
  $stmt->execute();
  $result = $stmt->get_result();

  // Если товар найден
  if ($row = $result->fetch_assoc()) {
      // Формирование HTML-код карточки товара с обёрткой <a>
      return '<div class="product-card">' .
                '<a href="product.php?id=' . htmlspecialchars($row['product_id']) . '" class="product-link">' .
                  '<img src="' . htmlspecialchars($row["image_url"]) . '" alt="' . htmlspecialchars($row["model"]) . '" class="product-img">' .
                  '<h3 class="product-title">' . htmlspecialchars($row["model"]) . '</h3>' .
                  '<p class="product-desc">' . htmlspecialchars($row["short_description"]) . '</p>' .
                  '<span class="product-price">' . number_format($row["price"], 2, ',', ' ') . ' ₽</span>' .
                '</a>' .
             '</div>';
  } else {
      return '<div class="product-card">Товар не найден</div>';
  }
}
?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <!-- Чат бот -->
    <script>
      window.juswidgetVariables = new Object();
      window.juswidgetVariables.start = new Object();
      window.juswidgetVariables.start.address = "Чехов";
    </script>
    
    <script src="https://bot.jaicp.com/chatwidget/XwqYAstf:facfa1db6eaa69f6a1550bef3bef326532f8ca8a/justwidget.js?force=true" async></script>
    <!-- Указание кодировки документа -->
    <meta charset="utf-8">

    <!-- Подключение Bootstrap для стилизации -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" 
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    
    <!-- Подключение файла со своими стилями -->
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <!-- Предварительная загрузка Google Fonts для ускорения -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Подключение шрифта Alegreya -->
    <link href="https://fonts.googleapis.com/css2?family=Alegreya:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    <!-- Фавикон (ярлык) -->
    <link rel="icon" href="assets/images/favicon.ico">
    
    <!-- Заголовок страницы -->
    <title>Магазин часов On-Time</title>
    
    <!-- Метатег для адаптации под мобильные устройства -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  
  <body>

    <?php include 'header.php'; ?>
    <!-- Блок с логотипом и текстом -->
    <div class="startlogo textnav_light">
      <img class="img_logo" src="assets/images/top_bggg.jpg" alt="logo">
      <div class="startlogo_text">
        <h1 class="start_text">Магазин часов<br><b>On-Time</b></h1>
        <p class="start_text2">Известные часовые бренды Японии, Европы и России</p>
        <div class="center-content">
          <!-- Кнопка для перехода к покупкам -->
          <a href="catalog.php" class="whitelink">К покупкам</a>
        </div>
      </div>
    </div>

    <!-- Блок с новинками -->
    <div class="gray-container">
      <div class="sales-container">
        <h2 class="sales-text">Новинки</h2>
        <!-- Витрина товаров -->
        <div class="product-grid">
          <!-- Карточка товара 1 -->
          <?php
            // Массив с ID товаров, ручной ввод
            $productIds = [1, 7, 8, 9]; // Укажите нужные вам ID товаров

            // Перебирает все ID и выводит карточки товаров
            foreach ($productIds as $id) {
                echo renderProductCard($conn, $id); // Вызывает функцию для каждой карточки товара
            }
          ?>
        </div>
      </div>
    </div>

    <!-- Блок с бестселлеры -->
    <div class="white-container">
      <div class="sales-container">
        <h4 class="sales-text">Бестселлеры</h4>
        <!-- Витрина товаров -->
          <div class="product-grid">
          <!-- Карточка товара 1 -->
          <?php
            // Массив с ID товаров, ручной
            $productIds = [10, 11, 12, 13, 14, 15, 16, 17]; // Укажите нужные вам ID товаров

            // Перебирает все ID и выводит карточки товаров
            foreach ($productIds as $id) {
                echo renderProductCard($conn, $id); // Вызывает функцию для каждой карточки товара
            }
          ?>
          </div>
        </div>
      </div>
    </div>


    <?php include 'footer.php'; ?>

    <!-- Подключение скриптов Bootstrap -->
     
    <!-- jQuery (необходим для работы Bootstrap JavaScript компонентов) -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" 
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" 
            crossorigin="anonymous"></script>

    <!-- Popper.js (необходим для работы выпадающих меню Bootstrap) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" 
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9Xyl8+2yhDtvUi68sEyxh2jMIIy4eGtv5TIWm" 
            crossorigin="anonymous"></script>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" 
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" 
            crossorigin="anonymous"></script>

  </body>
</html>