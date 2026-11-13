
<?php

require_once __DIR__ . '/../core/core.php';

require_login();

$error = pull_flash('error');

include __DIR__ . '/layout/header.php';
include __DIR__ . '/layout/sidebar.php';

?>

<h1>Payment Not Completed</h1>

<p>
    <?= htmlspecialchars(
        $error ?? 'Your payment could not be completed.'
    ) ?>
</p>

<p>
    You can return to your cart to review your order.</p>

<p>
    <a href="<?= BASE_URL ?>/views/cart.php">Return to Cart</a>
</p>

<?php include __DIR__ . '/layout/footer.php'; ?>
