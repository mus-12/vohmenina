<?php
session_start();

define('SITE_NAME', 'ВелоМир');
define('SITE_URL', 'http://localhost/bikeshop');

// Настройки БД (по умолчанию для OpenServer)
define('DB_HOST', 'localhost');
define('DB_NAME', 'bikeshop');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// PDO подключение
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

// Корзина (хранится в сессии)
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function addToCart($id, $name, $price, $image, $quantity = 1) {
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$id] = [
            'name' => $name, 'price' => $price, 'image' => $image, 'quantity' => $quantity
        ];
    }
}

function removeFromCart($id) { unset($_SESSION['cart'][$id]); }

function updateCartQuantity($id, $quantity) {
    if ($quantity <= 0) { removeFromCart($id); }
    elseif (isset($_SESSION['cart'][$id])) { $_SESSION['cart'][$id]['quantity'] = $quantity; }
}

function getCartTotal() {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) $total += $item['price'] * $item['quantity'];
    return $total;
}

function getCartCount() {
    $count = 0;
    foreach ($_SESSION['cart'] as $item) $count += $item['quantity'];
    return $count;
}
?>