
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

<div class="container">

    <h1>Shopping Cart</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($items)): ?>

        <p>Your shopping cart is currently empty.</p>

        <a href="<?= BASE_URL ?>/index.php">
            Continue Shopping
        </a>

    <?php else: ?>

        <!-- Form used for removing selected products -->
        <form
            id="remove-selected"
            method="POST"
            action="<?= BASE_URL ?>/actions/remove_from_cart_action.php"
        ></form>

        <table class="admin-table">

            <thead>
                <tr>
                    <th>Remove</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Update</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($items as $item): ?>

                    <?php
                        $product_id = (int) $item['p_id'];
                        $quantity = (int) $item['qty'];
                        $price = (float) $item['product_price'];
                        $subtotal = $quantity * $price;

                        $update_form_id = 'update-' . $product_id;
                    ?>

                    <tr>

                        <!-- Product removal checkbox -->
                        <td>
                            <input
                                type="checkbox"
                                name="remove[]"
                                value="<?= $product_id ?>"
                                form="remove-selected"
                            >
                        </td>

                        <!-- Product details -->
                        <td>

                            <?php if (!empty($item['product_image'])): ?>

                                <img
                                    src="<?= htmlspecialchars($item['product_image']) ?>"
                                    alt="<?= htmlspecialchars($item['product_title']) ?>"
                                    width="60"
                                    height="60"
                                    style="object-fit: contain;"
                                >

                            <?php endif; ?>

                            <br>

                            <?= htmlspecialchars($item['product_title']) ?>

                        </td>

                        <!-- Product price -->
                        <td>
                            GHS <?= number_format($price, 2) ?>
                        </td>

                        <!-- Quantity update form -->
                        <td>

                            <form
                                id="<?= $update_form_id ?>"
                                method="POST"
                                action="<?= BASE_URL ?>/actions/update_qty_action.php"
                            >

                                <input
                                    type="hidden"
                                    name="update[]"
                                    value="<?= $product_id ?>"
                                >

                                <input
                                    type="number"
                                    name="qty[]"
                                    value="<?= $quantity ?>"
                                    min="1"
                                    required
                                >

                            </form>

                        </td>

                        <!-- Product subtotal -->
                        <td>
                            GHS <?= number_format($subtotal, 2) ?>
                        </td>

                        <!-- Update button -->
                        <td>

                            <button
                                type="submit"
                                form="<?= $update_form_id ?>"
                            >
                                Update
                            </button>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

            <tfoot>

                <tr>
                    <th colspan="4">Cart Total</th>

                    <th colspan="2">
                        GHS <?= number_format($total, 2) ?>
                    </th>
                </tr>

            </tfoot>

        </table>

        <br>

        <!-- Remove selected products -->
        <button
            type="submit"
            form="remove-selected"
        >
            Remove Selected Items
        </button>

        <br><br>

        <p>
            <a href="<?= BASE_URL ?>/index.php">
                Continue Shopping
            </a>
        </p>

        <p>
            <a href="<?= BASE_URL ?>/views/payment.php">
                Proceed to Checkout
            </a>
        </p>

    <?php endif; ?>

</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
