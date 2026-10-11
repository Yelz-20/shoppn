<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$controller = new ProductController();
$categories = $controller->getAllCategories();

include __DIR__ . '/layout/header.php';
?>

<h1>Categories</h1>
<p>Browse the product categories available at Shoppn.</p>

<table class="admin-table">
    <thead>
        <tr>
            <th>Category</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($categories)): ?>
            <tr>
                <td>No categories are available yet.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?= htmlspecialchars($category['cat_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . '/layout/footer.php'; ?>