<?php
require_once '../config.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        $id = $_POST['id'] ?? '';
        $name = $_POST['name'] ?? '';
        $price = floatval($_POST['price'] ?? 0);
        $image = $_POST['image'] ?? '';
        $quantity = intval($_POST['quantity'] ?? 1);

        addToCart($id, $name, $price, $image, $quantity);

        echo json_encode([
            'success' => true,
            'cartCount' => getCartCount()
        ]);
        break;

    case 'update':
        $id = $_POST['id'] ?? '';
        $quantity = intval($_POST['quantity'] ?? 1);

        updateCartQuantity($id, $quantity);

        echo json_encode([
            'success' => true,
            'cartCount' => getCartCount()
        ]);
        break;

    case 'remove':
        $id = $_POST['id'] ?? '';

        removeFromCart($id);

        echo json_encode([
            'success' => true,
            'cartCount' => getCartCount()
        ]);
        break;

    case 'contact':
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $message = $_POST['message'] ?? '';

        if ($name && $email && $message) {
            // Здесь можно добавить отправку на email
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Заполните все поля']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Неизвестное действие']);
        break;
}
?>