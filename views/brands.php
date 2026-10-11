<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$controller = new ProductController();
$brands = $controller->getAllBrands();

include __DIR__ . '/layout/header.php';
?>

<h1>Brands</h1>
<p>Browse the brands available at Shoppn.</p>

<table class="admin-table">
    <thead>
        <tr>
            <th>Brand</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($brands)): ?>
            <tr>
                <td>No brands are available yet.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($brands as $brand): ?>
                <tr>
                    <td><?= htmlspecialchars($brand['brand_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . '/layout/footer.php'; ?>