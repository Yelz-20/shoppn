<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$proId = filter_input(INPUT_GET, 'pro_id', FILTER_VALIDATE_INT);

if (!$proId || $proId < 1) {
    redirect(BASE_URL . '/index.php');
}

$controller = new ProductController();
$product = $controller->getProductById($proId);

if (!$product) {
    $_SESSION['error'] = 'The requested product could not be found.';
    redirect(BASE_URL . '/index.php');
}

include __DIR__ . '/layout/header.php';
include __DIR__ . '/layout/sidebar.php';
?>

<div class="single-product"
     style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:30px;">

    <div>
        <?php if (!empty($product['product_image'])): ?>
            <img
                src="<?= BASE_URL ?>/images/products/<?= rawurlencode(basename($product['product_image'])) ?>"
                alt="<?= htmlspecialchars($product['product_title']) ?>"
                style="width:100%;max-height:400px;object-fit:contain;"
            >
        <?php endif; ?>
    </div>

    <div>
        <h1><?= htmlspecialchars($product['product_title']) ?></h1>

        <p>
            <strong>Category:</strong>
            <?= htmlspecialchars($product['cat_name'] ?? 'Uncategorized') ?>
        </p>

        <p>
            <strong>Brand:</strong>
            <?= htmlspecialchars($product['brand_name'] ?? 'Unknown') ?>
        </p>

        <h2>
            Price: <?= number_format((float)$product['product_price'], 2) ?>
        </h2>

        <h3>Description</h3>
        <p><?= nl2br(htmlspecialchars($product['product_desc'])) ?></p>

        <p>
            <strong>Keywords:</strong>
            <?= htmlspecialchars($product['product_keywords']) ?>
        </p>

        <p>
            <a href="<?= BASE_URL ?>/index.php?add_cart=<?= (int)$product['product_id'] ?>">
                Add to Cart
            </a>
        </p>

        <p><a href="<?= BASE_URL ?>/index.php">Continue Shopping</a></p>
    </div>

</div>

<?php include __DIR__ . '/layout/footer.php'; ?>