
<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

require_login();

$order_id = (int) ($_SESSION['last_order_id'] ?? 0);

if ($order_id < 1) {
    redirect(BASE_URL . '/index.php');
}

$controller = new CartController();
$order = $controller->getOrderById($order_id);

if (
    !$order ||
    (int) $order['customer_id'] !== (int) $_SESSION['customer_id']
) {
    redirect(BASE_URL . '/index.php');
}

unset($_SESSION['last_order_id']);

include __DIR__ . '/layout/header.php';
include __DIR__ . '/layout/sidebar.php';

?>

<h1>Payment Successful!</h1>

<p>Thank you for shopping with Shoppn.</p>

<h2>Order Invoice</h2>

<p>
    <strong>Order Number:</strong>
    <?= (int) $order['order_id'] ?>
</p>

<p>
    <strong>Invoice Number:</strong>
    <?= (int) $order['invoice_no'] ?>
</p>

<p>
    <strong>Customer:</strong>
    <?= htmlspecialchars($order['customer_name']) ?>
</p>

<p>
    <strong>Email:</strong>
    <?= htmlspecialchars($order['customer_email']) ?>
</p>

<p>
    <strong>Order Date:</strong>
    <?= htmlspecialchars($order['order_date']) ?>
</p>

<p>
    <strong>Payment Status:</strong>
    <?= htmlspecialchars($order['order_status']) ?>
</p>

<table class="admin-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Subtotal</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($order['items'] as $item): ?>
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
            <th>GHS <?= number_format((float) $order['total'], 2) ?></th>
        </tr>
    </tfoot>
</table>

<p>
    <a href="<?= BASE_URL ?>/index.php">Continue Shopping</a>
</p>

<?php include __DIR__ . '/layout/footer.php'; ?>
