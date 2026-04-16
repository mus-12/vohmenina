<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' — ' . SITE_NAME : SITE_NAME . ' — Магазин велосипедов' ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-top">
                <div class="header-info">
                    <span>📞 +7 (800) 555-35-35</span>
                    <span>📍 Москва, ул. Велосипедная, 42</span>
                </div>
                <div class="header-actions">
                    <a href="cart.php" class="cart-btn">
                        🛒 Корзина
                        <span class="cart-count" id="cartCount"><?= getCartCount() ?></span>
                    </a>
                </div>
            </div>
            <nav class="header-nav">
                <a href="index.php" class="logo"><video class="video-logo" width="45" autoplay muted playsinline loop> 
                    <source src= "bike.mp4">
                </video> ВелоМир</a>
                <button class="burger" id="burgerBtn" aria-label="Меню">☰</button>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Главная</a></li>
                    <li><a href="catalog.php" class="<?= basename($_SERVER['PHP_SELF']) == 'catalog.php' ? 'active' : '' ?>">Каталог</a></li>
                    <li><a href="about.php" class="<?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>">О нас</a></li>
                    <li><a href="contacts.php" class="<?= basename($_SERVER['PHP_SELF']) == 'contacts.php' ? 'active' : '' ?>">Контакты</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>