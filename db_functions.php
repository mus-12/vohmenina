<?php
// Файл db_functions.php
function getAlltopics() {
    global $pdo;
    return $pdo->query("SELECT * FROM support_topic ORDER BY id")->fetchAll();
}
function getAllCategories() {
    global $pdo;
    return $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll();
}

function getProducts($category = null, $sort = 'default', $minPrice = 0, $maxPrice = null, $search = '', $limit = null) {
    global $pdo;
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE 1=1";
    $params = [];

    if ($category && $category !== 'all') {
        $sql .= " AND c.slug = ?";
        $params[] = $category;
    }
    if ($minPrice > 0) {
        $sql .= " AND p.price >= ?";
        $params[] = $minPrice;
    }
    if ($maxPrice !== null && $maxPrice > 0) {
        $sql .= " AND p.price <= ?";
        $params[] = $maxPrice;
    }
    if ($search) {
        $sql .= " AND (p.name LIKE ? OR c.name LIKE ? OR p.description LIKE ?)";
        $s = "%$search%";
        $params[] = $s; $params[] = $s; $params[] = $s;
    }

    switch ($sort) {
        case 'price_asc': $sql .= " ORDER BY p.price ASC"; break;
        case 'price_desc': $sql .= " ORDER BY p.price DESC"; break;
        case 'rating': $sql .= " ORDER BY p.rating DESC"; break;
        case 'name': $sql .= " ORDER BY p.name ASC"; break;
        default: $sql .= " ORDER BY p.id DESC";
    }

    if ($limit !== null) $sql .= " LIMIT " . (int)$limit;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getProduct($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getRelatedProducts($category_id, $exclude_id, $limit = 3) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? ORDER BY RAND() LIMIT ?");
    $stmt->execute([$category_id, $exclude_id, $limit]);
    return $stmt->fetchAll();
}

function createOrder($data, $items) {
    global $pdo;
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO orders (order_number, customer_name, phone, email, city, address, payment_method, comment, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['order_number'], $data['name'], $data['phone'], $data['email'],
            $data['city'], $data['address'], $data['payment'], $data['comment'], $data['total']
        ]);
        $orderId = $pdo->lastInsertId();

        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, total) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($items as $item) {
            $stmtItem->execute([
                $orderId, $item['id'], $item['name'], $item['price'], $item['quantity'], $item['total']
            ]);
        }
        $pdo->commit();
        return $orderId;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
?>