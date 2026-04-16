<?php
require_once 'config.php';
require_once 'db_functions.php';
$pageTitle = 'Контакты';
$status = $_GET['status'] ?? '';
$error = '';

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $topic_id = intval($_POST['topic_id'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    
    if (!$name || !$email || !$topic_id || !$comment) {
        $error = 'Пожалуйста, заполните все обязательные поля.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Введите корректный email адрес.';
    } else {
        $stmtCheck = $pdo->prepare("SELECT id FROM support_topic WHERE id = ?");
        $stmtCheck->execute([$topic_id]);
        
        if (!$stmtCheck->fetch()) {
            $error = 'Выбранная тема обращения не найдена.';
        } else {
            $stmtInsert = $pdo->prepare("INSERT INTO support (name, email, topic_id, comment) VALUES (?, ?, ?, ?)");
            if ($stmtInsert->execute([$name, $email, $topic_id, $comment])) {
                header('Location: contacts.php?status=success');
                exit;
            } else {
                $error = 'Ошибка сервера. Попробуйте отправить сообщение позже.';
            }
        }
    }
}
require_once 'header.php';
$topics = getAlltopics();
?>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Свяжитесь с нами</h2>
            <p>Мы всегда рады помочь с выбором велосипеда</p>
        </div>

        <!-- ✅ Двухколоночная сетка -->
        <div class="contacts-grid">
            
            <!-- 🔹 Левая колонка: инфо + карта -->
            <div class="contacts-left">
                <div class="contact-info-cards">
                    <div class="contact-card">
                        <div class="contact-card-icon">📞</div>
                        <div>
                            <h4>Телефон</h4>
                            <p>+7 (800) 555-35-35 (бесплатно)</p>
                            <p>+7 (495) 123-45-67 (Москва)</p>
                        </div>
                    </div>
                    <div class="contact-card">
                        <div class="contact-card-icon">📧</div>
                        <div>
                            <h4>Email</h4>
                            <p>info@velomir.ru</p>
                            <p>support@velomir.ru</p>
                        </div>
                    </div>
                    <div class="contact-card">
                        <div class="contact-card-icon">📍</div>
                        <div>
                            <h4>Адрес</h4>
                            <p>г. Москва, ул. Велосипедная, 42</p>
                            <p>Пн-Вс: 9:00 — 21:00</p>
                        </div>
                    </div>
                    <div class="contact-card">
                        <div class="contact-card-icon">💬</div>
                        <div>
                            <h4>Мессенджеры</h4>
                            <p>WhatsApp, Telegram: +7 (999) 123-45-67</p>
                        </div>
                    </div>
                </div>

                <!-- Карта -->
                <div class="contact-map">
                    <iframe 
                        src="https://yandex.ru/map-widget/v1/?ll=37.502481%2C55.789879&z=16&text=Веломир&mode=search" 
                        width="100%" 
                        height="300" 
                        style="border:0; border-radius: 8px;" 
                        allowfullscreen="true"
                        referrerpolicy="no-referrer-when-downgrade"
                        loading="lazy">
                    </iframe>
                </div>
            </div>

            <!-- 🔹 Правая колонка: форма -->
            <div class="contacts-right">
                <div class="contact-form">
                    <h2>✉️ Напишите нам</h2>
                    
                    <?php if ($status === 'success'): ?>
                        <div class="alert alert-success">✅ Сообщение успешно отправлено!</div>
                    <?php elseif ($error): ?>
                        <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <form id="contactForm" method="POST" action="contacts.php">
                        <div class="form-group">
                            <label for="contactName">Ваше имя *</label>
                            <input type="text" id="contactName" name="name" required 
                                   placeholder="Как к вам обращаться?" 
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="contactEmail">Email *</label>
                            <input type="email" id="contactEmail" name="email" required 
                                   placeholder="your@email.com" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="contactTopic">Тема обращения *</label>
                            <select id="contactTopic" name="topic_id" required>
                                <option value="">Выберите тему...</option>
                                <?php if (!empty($topics)): ?>
                                    <?php foreach ($topics as $t): ?>
                                        <option value="<?= $t['id'] ?>" 
                                                <?= (isset($_POST['topic_id']) && $_POST['topic_id'] == $t['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($t['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="1">Вопрос о товаре</option>
                                    <option value="2">Доставка</option>
                                    <option value="3">Гарантия</option>
                                    <option value="4">Сервис</option>
                                    <option value="5">Другое</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="contactMessage">Сообщение *</label>
                            <textarea id="contactMessage" name="comment" required 
                                      placeholder="Ваш вопрос или предложение..." rows="5"><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Отправить сообщение</button>
                    </form>
                </div>
            </div>
            
        </div> <!-- .contacts-grid -->
    </div>
</section>

<?php require_once 'footer.php'; ?>

