<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$cartController = new CartController();
$productController = new ProductController();

$product_id = filter_input(
    INPUT_GET,
    'add_cart',
    FILTER_VALIDATE_INT
);

if (!$product_id || $product_id < 1) {
    $_SESSION['error'] = 'Invalid product selected.';
    redirect(BASE_URL . '/index.php');
}

$product = $productController->getProductById($product_id);

if (!$product) {
    $_SESSION['error'] = 'Product not found.';
    redirect(BASE_URL . '/index.php');
}

$ip = get_ip();

if ($cartController->isInCart($product_id, $ip)) {
    $_SESSION['error'] =
        'This item is already in your cart. Go to your cart to update its quantity.';

    redirect(BASE_URL . '/views/cart.php');
}

$customer_id = isset($_SESSION['customer_id'])
    ? (int) $_SESSION['customer_id']
    : null;

if ($cartController->addToCart(
    $product_id,
    1,
    $ip,
    $customer_id
)) {
    $_SESSION['success'] = 'Product added to your cart.';
} else {
    $_SESSION['error'] = 'Unable to add this product to your cart.';
}

redirect(BASE_URL . '/views/cart.php');