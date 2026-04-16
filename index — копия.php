<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sekuya&display=swap" rel="stylesheet">
    <link href="stylesheet" href="...style.css">
</head>
<body>
<?php
$pageTitle = 'Главная';
require_once 'config.php';
require_once 'db_functions.php';
require_once 'header.php';

$featured = getProducts('all', 'rating', 0, null, '', 3);
$categories = getAllCategories();
?>
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Найди свой <span>идеальный</span> велосипед</h1>
                <p>Более 500 моделей от лучших производителей. Бесплатная доставка. Гарантия 3 года.</p>
                <div class="hero-buttons">
                    <a href="catalog.php" class="btn btn-accent btn-lg">Смотреть каталог</a>
                    <a href="about.php" class="btn btn-outline btn-lg" style="color: white; border-color: white;">О нас</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1532298229144-0ec0c57515c7?w=600" alt="Велосипед">
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="features-grid">
            <div class="feature-card"><div class="feature-icon">🚚</div><h3>Бесплатная доставка</h3><p>От 30 000 ₽ по всей России</p></div>
            <div class="feature-card"><div class="feature-icon">🛡️</div><h3>Гарантия 3 года</h3><p>На раму и комплектующие</p></div>
            <div class="feature-card"><div class="feature-icon">🔧</div><h3>Сервис и сборка</h3><p>Бесплатная настройка</p></div>
            <div class="feature-card"><div class="feature-icon">💳</div><h3>Рассрочка 0%</h3><p>До 24 месяцев без переплат</p></div>
        </div>
    </div>
</section>

<section class="section section-dark">
    <div class="container">
        <div class="section-title"><h2>Популярные модели</h2><p>Хиты продаж этого сезона</p></div>
        <div class="products-grid">
            <?php foreach ($featured as $p): 
                $discount = $p['old_price'] > 0 ? round((1 - $p['price'] / $p['old_price']) * 100) : 0;
                $stars = str_repeat('★', floor($p['rating']));
            ?>
            <div class="product-card">
                <div class="product-card-image">
                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                    <?php if ($discount > 0): ?><span class="product-badge">-<?= $discount ?>%</span><?php endif; ?>
                </div>
                <div class="product-card-body">
                    <div class="product-card-category"><?= htmlspecialchars($p['category_name']) ?></div>
                    <h3 class="product-card-title"><a href="product.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></a></h3>
                    <div class="product-rating"><span class="stars"><?= $stars ?></span> <span><?= $p['rating'] ?></span> <span class="rating-count">(<?= $p['reviews_count'] ?>)</span></div>
                    <div class="product-card-price">
                        <span class="price-current"><?= number_format($p['price'], 0, ',', ' ') ?> ₽</span>
                        <?php if ($discount > 0): ?><span class="price-old"><?= number_format($p['old_price'], 0, ',', ' ') ?> ₽</span><?php endif; ?>
                    </div>
                    <div class="product-card-actions">
                        <button class="btn btn-primary btn-sm add-to-cart-btn" data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>" data-price="<?= $p['price'] ?>" data-image="<?= htmlspecialchars($p['image']) ?>">🛒 В корзину</button>
                        <a href="product.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">→</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center; margin-top: 40px;"><a href="catalog.php" class="btn btn-primary btn-lg">Смотреть весь каталог →</a></div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title"><h2>Категории</h2><p>Выберите велосипед для любых целей</p></div>
        <div class="features-grid" style="grid-template-columns: repeat(3, 1fr);">
            <?php foreach ($categories as $cat): 
                $count = count(getProducts($cat['slug']));
            ?>
            <a href="catalog.php?cat=<?= $cat['slug'] ?>" class="feature-card" style="text-decoration: none;">
                <div class="feature-icon"><?= $cat['icon'] ?></div>
                <h3><?= htmlspecialchars($cat['name']) ?></h3>
                <p><?= $count ?> моделей</p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>    
</body>
</html>
