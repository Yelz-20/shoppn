<?php
require_once __DIR__ . '/../../controllers/ProductController.php';

$sidebarController = new ProductController();
$sidebarCategories = $sidebarController->getAllCategories();
$sidebarBrands = $sidebarController->getAllBrands();
?>

<aside class="product-sidebar"
       style="padding:20px;background:#f5f8f5;border-radius:10px;margin-bottom:20px;">

    <h3>Categories</h3>

    <ul>
        <?php foreach ($sidebarCategories as $category): ?>
            <li>
                <a href="<?= BASE_URL ?>/index.php?cat=<?= (int)$category['cat_id'] ?>">
                    <?= htmlspecialchars($category['cat_name']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <h3>Brands</h3>

    <ul>
        <?php foreach ($sidebarBrands as $brand): ?>
            <li>
                <a href="<?= BASE_URL ?>/index.php?brand=<?= (int)$brand['brand_id'] ?>">
                    <?= htmlspecialchars($brand['brand_name']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

</aside>