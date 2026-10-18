<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/cart.php');
}

$controller = new CartController();
$ip = get_ip();

$product_ids = $_POST['update'] ?? [];
$quantities = $_POST['qty'] ?? [];

if (
    !is_array($product_ids) ||
    !is_array($quantities) ||
    count($product_ids) !== count($quantities)
) {
    $_SESSION['error'] = 'Invalid cart information.';
    redirect(BASE_URL . '/views/cart.php');
}

$updated = 0;
$invalid = false;

foreach ($product_ids as $index => $product_id) {

    $product_id = filter_var(
        $product_id,
        FILTER_VALIDATE_INT
    );

    $quantity = filter_var(
        $quantities[$index] ?? null,
        FILTER_VALIDATE_INT
    );

    if (
        !$product_id ||
        $product_id < 1 ||
        !$quantity ||
        $quantity < 1
    ) {
        $invalid = true;
        continue;
    }

    if ($controller->updateQty($product_id, $quantity, $ip)) {
        $updated++;
    }
}

if ($invalid) {
    $_SESSION['error'] =
        'Some quantities were invalid. Please enter whole numbers of at least 1.';
} elseif ($updated > 0) {
    $_SESSION['success'] = 'Cart quantities updated successfully.';
} else {
    $_SESSION['error'] = 'No quantities were updated.';
}

redirect(BASE_URL . '/views/cart.php');