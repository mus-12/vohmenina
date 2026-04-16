<?php
$pageTitle = 'Оформление заказа';
require_once 'config.php';

$orderSuccess = false;

// if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['cart'])) {
//     $name = trim($_POST['name'] ?? '');
//     $phone = trim($_POST['phone'] ?? '');
//     $email = trim($_POST['email'] ?? '');
//     $city = trim($_POST['city'] ?? '');
//     $address = trim($_POST['address'] ?? '');
//     $comment = trim($_POST['comment'] ?? '');
//     $payment = $_POST['payment'] ?? 'card';

//     if ($name && $phone && $email && $city && $address) {
//         // Здесь: сохранение заказа в БД, отправка email
//         $orderNumber = 'VM-' . date('Y') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

//         // Очистка корзины
//         $_SESSION['cart'] = [];
//         $orderSuccess = true;
//     }
// }

$orderSuccess = false;
$orderNumber = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['cart'])) {
    $data = [
        'name'    => trim($_POST['name']),
        'phone'   => trim($_POST['phone']),
        'email'   => trim($_POST['email']),
        'city'    => trim($_POST['city']),
        'address' => trim($_POST['address']),
        'comment' => trim($_POST['comment']),
        'payment' => $_POST['payment'] ?? 'card',
        'total'   => getCartTotal() + (getCartTotal() >= 30000 ? 0 : 2990)
    ];

    $orderNumber = 'VM-' . date('Y') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
    $data['order_number'] = $orderNumber;

    // Формируем массив товаров для БД
    $items = [];
    foreach ($_SESSION['cart'] as $id => $item) {
        $items[] = [
            'id' => $id, 'name' => $item['name'], 'price' => $item['price'],
            'quantity' => $item['quantity'], 'total' => $item['price'] * $item['quantity']
        ];
    }

    try {
        require_once 'db_functions.php';
        createOrder($data, $items);
        $_SESSION['cart'] = [];
        $orderSuccess = true;
    } catch (Exception $e) {
        $error = "Ошибка оформления заказа: " . $e->getMessage();
    }
}

require_once 'header.php';

if ($orderSuccess): ?>
    <section class="section">
        <div class="container">
            <div class="success-page">
                <div class="success-icon">✅</div>
                <h1>Заказ оформлен!</h1>
                <p>Номер заказа: <strong><?= htmlspecialchars($orderNumber ?? '') ?></strong><br>
                Мы свяжемся с вами в ближайшее время для подтверждения.</p>
                <a href="index.php" class="btn btn-primary btn-lg">На главную</a>
            </div>
        </div>
    </section>
<?php else: ?>
    <section class="section">
        <div class="container">
            <h1 style="font-size: 1.8rem; margin-bottom: 28px;">Оформление заказа</h1>

            <?php if (empty($_SESSION['cart'])): ?>
                <div class="cart-page">
                    <div class="cart-empty">
                        <div class="empty-icon">🛒</div>
                        <p>Корзина пуста. Добавьте товары для оформления заказа.</p>
                        <a href="catalog.php" class="btn btn-primary btn-lg">Перейти в каталог</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="checkout-grid">
                    <form class="checkout-form" id="checkoutForm" method="POST" action="checkout.php">
                        <h2>📋 Контактные данные</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Имя и фамилия *</label>
                                <input type="text" id="name" name="name" required placeholder="Иван Иванов">
                            </div>
                            <div class="form-group">
                                <label for="phone">Телефон *</label>
                                <input type="tel" id="phone" name="phone" required placeholder="+7 (999) 123-45-67">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required placeholder="ivan@example.com">
                        </div>

                        <h2 style="margin-top: 32px;">📍 Адрес доставки</h2>

                        <div class="form-group">
                            <label for="city">Город *</label>
                            <input type="text" id="city" name="city" required placeholder="Москва">
                        </div>

                        <div class="form-group">
                            <label for="address">Адрес *</label>
                            <input type="text" id="address" name="address" required placeholder="ул. Примерная, д. 1, кв. 10">
                        </div>

                        <div class="form-group">
                            <label for="comment">Комментарий к заказу</label>
                            <textarea id="comment" name="comment" placeholder="Дополнительные пожелания..."></textarea>
                        </div>

                        <h2 style="margin-top: 32px;">💳 Способ оплаты</h2>

                        <div style="display: grid; gap: 12px;">
                            <label style="display: flex; align-items: center; gap: 12px; padding: 16px; border: 2px solid var(--gray-300); border-radius: var(--radius-sm); cursor: pointer; transition: var(--transition);">
                                <input type="radio" name="payment" value="card" checked style="width: 18px; height: 18px;">
                                <div>
                                    <strong>Банковская карта</strong>
                                    <p style="font-size: 0.85rem; color: var(--gray-500);">Visa, Mastercard, МИР</p>
                                </div>
                            </label>
                            <label style="display: flex; align-items: center; gap: 12px; padding: 16px; border: 2px solid var(--gray-300); border-radius: var(--radius-sm); cursor: pointer; transition: var(--transition);">
                                <input type="radio" name="payment" value="cash" style="width: 18px; height: 18px;">
                                <div>
                                    <strong>Наличные при получении</strong>
                                    <p style="font-size: 0.85rem; color: var(--gray-500);">Оплата курьеру</p>
                                </div>
                            </label>
                            <label style="display: flex; align-items: center; gap: 12px; padding: 16px; border: 2px solid var(--gray-300); border-radius: var(--radius-sm); cursor: pointer; transition: var(--transition);">
                                <input type="radio" name="payment" value="installment" style="width: 18px; height: 18px;">
                                <div>
                                    <strong>Рассрочка 0%</strong>
                                    <p style="font-size: 0.85rem; color: var(--gray-500);">На 6 или 12 месяцев</p>
                                </div>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block" style="margin-top: 24px;">
                            Подтвердить заказ — <?= number_format(getCartTotal(), 0, ',', ' ') ?> ₽
                        </button>
                    </form>

                    <!-- Order Summary -->
                    <div class="order-summary">
                        <h3>Ваш заказ</h3>
                        <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                            <div class="order-item">
                                <span class="order-item-name"><?= htmlspecialchars($item['name']) ?></span>
                                <span class="order-item-qty">×<?= $item['quantity'] ?></span>
                                <span class="order-item-price"><?= number_format($item['price'] * $item['quantity'], 0, ',', ' ') ?> ₽</span>
                            </div>
                        <?php endforeach; ?>

                        <div class="cart-summary-row" style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--gray-300);">
                            <span>Товары</span>
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
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<?php require_once 'footer.php'; ?>