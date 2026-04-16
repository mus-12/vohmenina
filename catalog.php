<?php
$pageTitle = 'Каталог велосипедов';
require_once 'config.php';
require_once 'db_functions.php';
require_once 'header.php';


$cat = $_GET['cat'] ?? 'all';
$sort = $_GET['sort'] ?? 'default';
$search = $_GET['search'] ?? '';
$minPrice = isset($_GET['min']) ? floatval($_GET['min']) : 0;
$maxPrice = isset($_GET['max']) && $_GET['max'] !== '' ? floatval($_GET['max']) : null;

$products = getProducts($cat, $sort, $minPrice, $maxPrice, $search);
$categories = getAllCategories();
?>

<section class="section">
    <div class="container">
        <h1 style="font-size: 2rem; margin-bottom: 28px;">Каталог велосипедов</h1>
        <div class="catalog-layout">
            <aside class="sidebar">
                <h3>Категории</h3>
                <ul class="category-list">
                    <?php foreach ($categories as $c): ?>
                    <li><a href="catalog.php?cat=<?= $c['slug'] ?><?= $sort !== 'default' ? '&sort=' . $sort : '' ?>" class="<?= $cat === $c['slug'] ? 'active' : '' ?>"><?= htmlspecialchars($c['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <div class="price-filter">
                    <h3>Цена, ₽</h3>
                    <form method="GET" class="price-range">
                        <input type="hidden" name="cat" value="<?= htmlspecialchars($cat) ?>">
                        <input type="number" name="min" placeholder="От" value="<?= $minPrice ?: '' ?>">
                        <input type="number" name="max" placeholder="До" value="<?= $maxPrice ?: '' ?>">
                        <button type="submit" class="btn btn-primary btn-sm btn-block" id="priceFilterBtn">Применить</button>
                    </form>
                </div>
            </aside>

            <div class="catalog-content">
                <div class="catalog-toolbar">
                    <span class="results-count">Найдено: <?= count($products) > 0 ? count($products) : 0 ?> велосипедов</span>
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <form method="GET" style="display: flex; gap: 8px;">
                            <input type="hidden" name="cat" value="<?= htmlspecialchars($cat) ?>">
                            <input type="text" name="search" placeholder="🔍 Поиск..." value="<?= htmlspecialchars($search) ?>" style="padding: 8px 14px; border: 2px solid var(--gray-300); border-radius: 20px; font-family: inherit; width: 200px;">
                            <select name="sort" onchange="this.form.submit()" style="padding: 8px 14px; border: 2px solid var(--gray-300); border-radius: 20px; font-family: inherit;">
                                <option value="default" <?= $sort === 'default' ? 'selected' : '' ?>>По умолчанию</option>
                                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Цена ↑</option>
                                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Цена ↓</option>
                                <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>По рейтингу</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">Найти</button>
                        </form>
                    </div>
                </div>

                <?php if (empty($products)): ?>
                    <div style="text-align: center; padding: 60px 20px; background: white; border-radius: var(--radius);">
                        <div style="font-size: 3rem; margin-bottom: 16px;">😔</div>
                        <h3>Товары не найдены</h3>
                    </div>
                <?php else: ?>
                    <div class="products-grid">
                        <?php foreach ($products as $p): 
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
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>