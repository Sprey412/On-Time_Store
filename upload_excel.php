<?php
// upload_excel.php

include 'config.php';
include 'auth_check.php';
session_start();
require 'vendor/autoload.php'; // Подключение автозагрузчика Composer

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $tmpName = $file['tmp_name'];
        $fileName = basename($file['name']);
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $destination = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $destination)) {
            $message = process_excel_upload($destination, $conn);
            unlink($destination); // Удаляем файл после обработки
            $_SESSION['message'] = $message;
        } else {
            $_SESSION['message'] = "Ошибка при перемещении загруженного файла.";
        }
    } else {
        $_SESSION['message'] = "Ошибка загрузки файла: " . $file['error'];
    }

    header('Location: add_product.php');
    exit();
} else {
    $_SESSION['message'] = "Некорректный запрос.";
    header('Location: add_product.php');
    exit();
}

function process_excel_upload($file, $conn) {
    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Перебираем строки файла Excel, начиная со второй (предполагая, что первая строка - заголовки)
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            // Проверяем, что все необходимые ячейки заполнены
            if (!empty($row[0]) && !empty($row[1]) && !empty($row[2]) && !empty($row[3]) && !empty($row[4]) && !empty($row[5])) {
                $model = trim($row[0]); // Модель в первом столбце
                $short_description = trim($row[1]); // Краткое описание во втором столбце
                $full_description = trim($row[2]); // Полное описание в третьем столбце
                $price = floatval($row[3]); // Цена в четвертом столбце
                $brand_name = trim($row[4]); // Название бренда в пятом столбце
                $image_url = trim($row[5]); // URL изображения в шестом столбце
                $quantity = isset($row[6]) ? intval($row[6]) : 1; // Количество в седьмом столбце (опционально)

                // Получаем brand_id по brand_name
                $brand_stmt = $conn->prepare("SELECT brand_id FROM brands WHERE brand_name = ?");
                $brand_stmt->bind_param("s", $brand_name);
                $brand_stmt->execute();
                $brand_result = $brand_stmt->get_result();

                if ($brand_row = $brand_result->fetch_assoc()) {
                    $brand_id = $brand_row['brand_id'];
                } else {
                    // Если бренд не существует, добавляем его
                    $insert_brand = $conn->prepare("INSERT INTO brands (brand_name) VALUES (?)");
                    $insert_brand->bind_param("s", $brand_name);
                    $insert_brand->execute();
                    $brand_id = $insert_brand->insert_id;
                    $insert_brand->close();
                }
                $brand_stmt->close();

                // Проверяем, существует ли товар с такой же моделью
                $product_stmt = $conn->prepare("SELECT product_id, quantity FROM products WHERE model = ?");
                $product_stmt->bind_param("s", $model);
                $product_stmt->execute();
                $product_result = $product_stmt->get_result();

                if ($product_row = $product_result->fetch_assoc()) {
                    // Товар существует, обновляем количество
                    $new_quantity = $product_row['quantity'] + $quantity;
                    $update_product = $conn->prepare("UPDATE products SET quantity = ?, price = ?, image_url = ?, full_description = ?, short_description = ?, brand_id = ? WHERE product_id = ?");
                    $update_product->bind_param("idsssii", $new_quantity, $price, $image_url, $full_description, $short_description, $brand_id, $product_row['product_id']);
                    $update_product->execute();
                    $update_product->close();
                } else {
                    // Товар не существует, добавляем новый
                    $insert_product = $conn->prepare("INSERT INTO products (model, short_description, full_description, price, brand_id, image_url, quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $insert_product->bind_param("sssdisi", $model, $short_description, $full_description, $price, $brand_id, $image_url, $quantity);
                    $insert_product->execute();
                    $insert_product->close();
                }
                $product_stmt->close();
            } else {
                // Не все поля заполнены
                // Можно добавить запись об ошибке, но сейчас просто пропускаем
                continue;
            }
        }

        return "Данные из Excel успешно обработаны!";
    } catch (Exception $e) {
        return "Ошибка при загрузке файла: " . $e->getMessage();
    }
}
?>
