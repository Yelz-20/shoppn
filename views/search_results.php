<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$query = trim($_GET['user_query'] ?? '');

$controller = new ProductController();

$products = $query !== ''
    ? $controller->searchProducts($query)
    : [];

include __DIR__ . '/layout/header.php';
include __DIR__ . '/layout/sidebar.php';
?>

<h1>Search Results</h1>

<?php if ($query !== ''): ?>
    <p>
        Results for:
        <strong><?= htmlspecialchars($query) ?></strong>
    </p>
<?php endif; ?>

<div class="product-grid"
     style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:20px;">

    <?php if (empty($products)): ?>

        <p>No products found. Try another search term.</p>

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
                    <strong><?= number_format((float)$product['product_price'], 2) ?></strong>
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