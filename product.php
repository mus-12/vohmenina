<?php
require_once 'config.php';
require_once 'db_functions.php';

$id = intval($_GET['id'] ?? 0);
$product = getProduct($id);
if (!$product) { header('Location: catalog.php'); exit; }

$pageTitle = $product['name'];
require_once 'header.php';

$discount = $product['old_price'] > 0 ? round((1 - $product['price'] / $product['old_price']) * 100) : 0;
$stars = str_repeat('★', floor($product['rating']));
$specs = json_decode($product['specs'], true) ?: [];
$related = getRelatedProducts($product['category_id'], $product['id'], 3);
?>

<section class="section">
    <div class="container">
        <div style="margin-bottom: 24px; font-size: 0.9rem; color: var(--gray-500);">
            <a href="index.php">Главная</a> / <a href="catalog.php">Каталог</a> / 
            <a href="catalog.php?cat=<?= $product['category_slug'] ?>"><?= htmlspecialchars($product['category_name']) ?></a> / 
            <span style="color: var(--gray-900);"><?= htmlspecialchars($product['name']) ?></span>
        </div>

        <div class="product-page">
            <div class="product-page-grid">
                <div class="product-page-image">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                </div>
                <div class="product-page-info">
                    <?php if ($discount > 0): ?><span class="product-badge">Скидка <?= $discount ?>%</span><?php endif; ?>
                    <div class="product-card-category"><?= htmlspecialchars($product['category_name']) ?></div>
                    <h1><?= htmlspecialchars($product['name']) ?></h1>
                    <div class="product-rating"><span class="stars"><?= $stars ?></span> <span><?= $product['rating'] ?></span> <span class="rating-count">(<?= $product['reviews_count'] ?> отзывов)</span></div>
                    <div class="product-card-price">
                        <span class="price-current"><?= number_format($product['price'], 0, ',', ' ') ?> ₽</span>
                        <?php if ($discount > 0): ?><span class="price-old"><?= number_format($product['old_price'], 0, ',', ' ') ?> ₽</span><?php endif; ?>
                    </div>
                    <p style="color: <?= $product['stock'] > 0 ? 'var(--success)' : 'var(--danger)' ?>; font-weight: 600; margin-bottom: 16px;">
                        <?= $product['stock'] > 0 ? "✅ В наличии ({$product['stock']} шт.)" : "❌ Нет в наличии" ?>
                    </p>
                    <p class="product-description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    
                    <div class="quantity-selector">
                        <button class="qty-minus">−</button>
                        <input type="number" value="1" min="1" max="<?= $product['stock'] ?>">
                        <button class="qty-plus">+</button>
                    </div>

                    <div class="product-page-actions">
                        <button class="btn btn-primary btn-lg add-to-cart-btn" data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>" data-price="<?= $product['price'] ?>" data-image="<?= htmlspecialchars($product['image']) ?>">🛒 Добавить в корзину</button>
                        <button class="btn btn-accent btn-lg" onclick="document.querySelector('.add-to-cart-btn').click(); setTimeout(() => window.location.href='checkout.php', 600);">⚡ Купить сейчас</button>
                    </div>
                </div>
            </div>

            <div style="padding: 40px;">
                <h2 style="font-size: 1.4rem; margin-bottom: 20px;">Характеристики</h2>
                <?php if (!empty($specs)): ?>
                <table class="specs-table">
                    <?php foreach ($specs as $k => $v): ?>
                    <tr><td><?= htmlspecialchars($k) ?></td><td><?= htmlspecialchars($v) ?></td></tr>
                    <?php endforeach; ?>
                </table>
                <?php else: ?>
                <p style="color: var(--gray-500);">Характеристики не указаны.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($related)): ?>
        <div style="margin-top: 60px;">
            <div class="section-title"><h2>Похожие товары</h2></div>
            <div class="products-grid">
                <?php foreach ($related as $r): 
                    $rd = $r['old_price'] > 0 ? round((1 - $r['price'] / $r['old_price']) * 100) : 0;
                ?>
                <div class="product-card">
                    <div class="product-card-image">
                        <img src="<?= htmlspecialchars($r['image']) ?>" alt="<?= htmlspecialchars($r['name']) ?>">
                        <?php if ($rd > 0): ?><span class="product-badge">-<?= $rd ?>%</span><?php endif; ?>
                    </div>
                    <div class="product-card-body">
                        <h3 class="product-card-title"><a href="product.php?id=<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></a></h3>
                        <div class="product-card-price">
                            <span class="price-current"><?= number_format($r['price'], 0, ',', ' ') ?> ₽</span>
                            <?php if ($rd > 0): ?><span class="price-old"><?= number_format($r['old_price'], 0, ',', ' ') ?> ₽</span><?php endif; ?>
                        </div>
                        <div class="product-card-actions">
                            <button class="btn btn-primary btn-sm add-to-cart-btn" data-id="<?= $r['id'] ?>" data-name="<?= htmlspecialchars($r['name']) ?>" data-price="<?= $r['price'] ?>" data-image="<?= htmlspecialchars($r['image']) ?>">🛒 В корзину</button>
                            <a href="product.php?id=<?= $r['id'] ?>" class="btn btn-outline btn-sm">→</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'footer.php'; ?>