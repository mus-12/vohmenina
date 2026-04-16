<?php
$pageTitle = 'Корзина';
require_once 'config.php';
require_once 'header.php';
?>

<section class="section">
    <div class="container">
        <div class="cart-page">
            <h1>🛒 Корзина</h1>

            <?php if (empty($_SESSION['cart'])): ?>
                <div class="cart-empty">
                    <div class="empty-icon">🛒</div>
                    <p>Ваша корзина пуста</p>
                    <a href="catalog.php" class="btn btn-primary btn-lg">Перейти в каталог</a>
                </div>
            <?php else: ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Товар</th>
                            <th>Цена</th>
                            <th>Количество</th>
                            <th>Сумма</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                            <tr>
                                <td>
                                    <div class="cart-item-info">
                                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                        <div>
                                            <div class="cart-item-name">
                                                <a href="product.php?id=<?= $id ?>"><?= htmlspecialchars($item['name']) ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong><?= number_format($item['price'], 0, ',', ' ') ?> ₽</strong>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0;">
                                        <button class="cart-qty-btn" data-id="<?= $id ?>" data-action="decrease"
                                                style="width: 32px; height: 32px; border: 1px solid var(--gray-300); background: white; cursor: pointer; font-size: 1rem; border-radius: 4px 0 0 4px;">−</button>
                                        <span id="qty-<?= $id ?>" style="width: 40px; height: 32px; display: flex; align-items: center; justify-content: center; border-top: 1px solid var(--gray-300); border-bottom: 1px solid var(--gray-300); font-weight: 600;"><?= $item['quantity'] ?></span>
                                        <button class="cart-qty-btn" data-id="<?= $id ?>" data-action="increase"
                                                style="width: 32px; height: 32px; border: 1px solid var(--gray-300); background: white; cursor: pointer; font-size: 1rem; border-radius: 0 4px 4px 0;">+</button>
                                    </div>
                                </td>
                                <td>
                                    <strong><?= number_format($item['price'] * $item['quantity'], 0, ',', ' ') ?> ₽</strong>
                                </td>
                                <td>
                                    <button class="remove-cart-btn" data-id="<?= $id ?>"
                                            style="background: none; border: none; cursor: pointer; font-size: 1.3rem; color: var(--gray-500);"
                                            title="Удалить">🗑️</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    <a href="catalog.php" class="btn btn-outline">← Продолжить покупки</a>

                    <div class="cart-summary">
                        <div class="cart-summary-row">
                            <span>Товары (<?= getCartCount() ?> шт.)</span>
                            <span><?= number_format(getCartTotal(), 0, ',', ' ') ?> ₽</span>
                        </div>
                        <div class="cart-summary-row">
                            <span>Доставка</span>
                            <span style="color: var(--success);"><?= getCartTotal() >= 30000 ? 'Бесплатно' : '2 990 ₽' ?></span>
                        </div>
                        <div class="cart-summary-total">
                            <span>Итого:</span>
                            <span><?= number_format(getCartTotal() + (getCartTotal() >= 30000 ? 0 : 2990), 0, ',', ' ') ?> ₽</span>
                        </div>
                        <a href="checkout.php" class="btn btn-primary btn-lg btn-block">Оформить заказ</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>