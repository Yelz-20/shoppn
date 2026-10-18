<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

$controller = new CartController();

$ip = get_ip();

$items = $controller->getCartItems($ip);
$total = $controller->getCartTotal($ip);

$success = pull_flash('success');
$error = pull_flash('error');

include __DIR__ . '/layout/header.php';
include __DIR__ . '/layout/sidebar.php';

?>

<h1>Shopping Cart</h1>

<?php if ($success): ?>
    <div class="alert success">
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert error">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php if (empty($items)): ?>

    <p>Your shopping cart is empty.</p>

    <p>
        <a href="<?= BASE_URL ?>/index.php">
            Continue Shopping
        </a>
    </p>

<?php else: ?>

    <form
        id="cartForm"
        action="<?= BASE_URL ?>/actions/update_qty_action.php"
        method="POST"
    >

        <div style="overflow-x:auto;">

            <table class="admin-table">

                <thead>
                    <tr>
                        <th>Remove</th>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($items as $item): ?>

                    <tr>

                        <td>
                            <input
                                type="checkbox"
                                name="remove[]"
                                value="<?= (int) $item['p_id'] ?>"
                                form="removeCartForm"
                            >
                        </td>

                        <td>
                            <?= htmlspecialchars($item['product_title']) ?>
                        </td>

                        <td>

                            <?php if (!empty($item['product_image'])): ?>

                                <img
                                    src="<?= UPLOAD_URL ?>/<?= rawurlencode(basename($item['product_image'])) ?>"
                                    alt="<?= htmlspecialchars($item['product_title']) ?>"
                                    style="width:70px;height:70px;object-fit:contain;"
                                >

                            <?php endif; ?>

                        </td>

                        <td>
                            GHS <?= number_format(
                                (float) $item['product_price'],
                                2
                            ) ?>
                        </td>

                        <td>

                            <input
                                type="hidden"
                                name="update[]"
                                value="<?= (int) $item['p_id'] ?>"
                            >

                            <input
                                type="number"
                                name="qty[]"
                                min="1"
                                step="1"
                                required
                                value="<?= (int) $item['qty'] ?>"
                                style="width:75px;"
                            >

                        </td>

                        <td>
                            GHS <?= number_format(
                                (int) $item['qty'] *
                                (float) $item['product_price'],
                                2
                            ) ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

                <tfoot>

                    <tr>
                        <th colspan="5">Cart Total</th>

                        <th>
                            GHS <?= number_format($total, 2) ?>
                        </th>
                    </tr>

                </tfoot>

            </table>

        </div>

        <br>

        <button type="submit">
            Update Quantities
        </button>

    </form>

    <form
        id="removeCartForm"
        action="<?= BASE_URL ?>/actions/remove_from_cart_action.php"
        method="POST"
        style="margin-top:10px;"
    >

        <button type="submit">
            Remove Selected Items
        </button>

    </form>

    <p>
        <a href="<?= BASE_URL ?>/index.php">
            Continue Shopping
        </a>
    </p>

<?php endif; ?>

<?php include __DIR__ . '/layout/footer.php'; ?>