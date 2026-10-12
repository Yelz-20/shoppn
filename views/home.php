<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$controller = new ProductController();

if (isset($_GET['cat'])) {
    $catId = filter_input(INPUT_GET, 'cat', FILTER_VALIDATE_INT);
    $products = ($catId && $catId > 0)
        ? $controller->getProductsByCategory($catId)
        : [];
    $pageTitle = 'Products by Category';

} elseif (isset($_GET['brand'])) {
    $brandId = filter_input(INPUT_GET, 'brand', FILTER_VALIDATE_INT);
    $products = ($brandId && $brandId > 0)
        ? $controller->getProductsByBrand($brandId)
        : [];
    $pageTitle = 'Products by Brand';

} else {
    $products = $controller->getFeaturedProducts(6);
    $pageTitle = 'Featured Products';
}

$success = pull_flash('success');
$error = pull_flash('error');

include __DIR__ . '/layout/header.php';
include __DIR__ . '/layout/sidebar.php';
?>

<?php if ($success): ?>
    <div class="alert success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<h1>Welcome to Shoppn</h1>
<p>Discover products from our available brands and categories.</p>

<h2><?= htmlspecialchars($pageTitle) ?></h2>

<div class="product-grid"
     style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:20px;">

    <?php if (empty($products)): ?>

        <p>No products found. Please check back later or try another category.</p>

    <?php else: ?>

        <?php foreach ($products as $product): ?>

            <article class="product-card"
                     style="padding:15px;border:1px solid #e0e0e0;border-radius:10px;">

                <?php if (!empty($product['product_image'])): ?>
                    <img
                        src="<?= BASE_URL ?>/images/products/<?= rawurlencode(basename($product['product_image'])) ?>"
                        alt="<?= htmlspecialchars($product['product_title']) ?>"
                        loading="lazy"
                        style="width:100%;height:180px;object-fit:contain;"
                    >
                <?php endif; ?>

                <h3><?= htmlspecialchars($product['product_title']) ?></h3>

                <p>
                    Price:
                    <strong>
                        <?= number_format((float)$product['product_price'], 2) ?>
                    </strong>
                </p>

                <p>
                    <?= htmlspecialchars($product['brand_name'] ?? '') ?>
                </p>

                <p>
                    <a href="<?= BASE_URL ?>/views/single_product.php?pro_id=<?= (int)$product['product_id'] ?>">
                        Details
                    </a>
                </p>

                <p>
                    <a href="<?= BASE_URL ?>/index.php?add_cart=<?= (int)$product['product_id'] ?>">
                        Add to Cart
                    </a>
                </p>

            </article>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<?php include __DIR__ . '/layout/footer.php'; ?>