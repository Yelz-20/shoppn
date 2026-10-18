<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/cart.php');
}

$controller = new CartController();
$ip = get_ip();

$products = $_POST['remove'] ?? [];

if (!is_array($products) || empty($products)) {
    $_SESSION['error'] = 'Please select at least one item to remove.';
    redirect(BASE_URL . '/views/cart.php');
}

$removed = 0;

foreach ($products as $product_id) {

    $product_id = filter_var(
        $product_id,
        FILTER_VALIDATE_INT
    );

    if (!$product_id || $product_id < 1) {
        continue;
    }

    if ($controller->removeFromCart($product_id, $ip)) {
        $removed++;
    }
}

if ($removed > 0) {
    $_SESSION['success'] = 'Selected items were removed from your cart.';
} else {
    $_SESSION['error'] = 'No items were removed.';
}

redirect(BASE_URL . '/views/cart.php');