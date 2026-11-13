
<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

require_login();

$controller = new CartController();
$ip = get_ip();

$items = $controller->getCartItems($ip);
$total = $controller->getCartTotal($ip);

if (empty($items) || $total <= 0) {
    $_SESSION['error'] = 'Your cart is empty.';
    redirect(BASE_URL . '/views/cart.php');
}

if (!isset($_SESSION['payment_csrf'])) {
    $_SESSION['payment_csrf'] = bin2hex(random_bytes(32));
}

include __DIR__ . '/layout/header.php';
include __DIR__ . '/layout/sidebar.php';

?>

<h1>Checkout</h1>

<p>Review your order before proceeding to payment.</p>

<table class="admin-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['product_title']) ?></td>
                <td><?= (int) $item['qty'] ?></td>
                <td>
                    GHS <?= number_format((float) $item['product_price'], 2) ?>
                </td>
                <td>
                    GHS <?= number_format(
                        (int) $item['qty'] * (float) $item['product_price'],
                        2
                    ) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>

    <tfoot>
        <tr>
            <th colspan="3">Total</th>
            <th>GHS <?= number_format($total, 2) ?></th>
        </tr>
    </tfoot>
</table>

<form method="POST"
      action="<?= BASE_URL ?>/actions/initialize_payment_action.php">

    <input type="hidden"
           name="csrf_token"
           value="<?= htmlspecialchars($_SESSION['payment_csrf']) ?>">

    <br>

    <button type="submit">
        Proceed to Paystack
    </button>

</form>

<p>
    <a href="<?= BASE_URL ?>/views/cart.php">Return to Cart</a>
</p>

<?php include __DIR__ . '/layout/footer.php'; ?>
