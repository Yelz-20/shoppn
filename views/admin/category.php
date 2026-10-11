<?php
require_once __DIR__ . '/../../core/core.php';
require_admin();
require_once __DIR__ . '/../../controllers/ProductController.php';

$controller = new ProductController();

$editing = false;
$editCategory = null;
if (isset($_GET['edit_id']) && ctype_digit($_GET['edit_id'])) {
    $editCategory = $controller->getCategoryById((int)$_GET['edit_id']);
    $editing = $editCategory !== false && $editCategory !== null;
}

$categories = $controller->getAllCategories();

include __DIR__ . '/../layout/header.php';
?>
<h1>Manage Categories</h1>

<?php if ($s = pull_flash('success')): ?>
    <div class="alert success"><?= htmlspecialchars($s) ?></div>
<?php endif; ?>
<?php if ($e = pull_flash('error')): ?>
    <div class="alert error"><?= htmlspecialchars($e) ?></div>
<?php endif; ?>

<h2><?= $editing ? 'Edit Category' : 'Add Category' ?></h2>
<form class="form"
      action="<?= BASE_URL ?>/actions/<?= $editing ? 'update_category_action.php' : 'add_category_action.php' ?>"
      method="POST">

    <?php if ($editing): ?>
        <input type="hidden" name="cat_id" value="<?= (int)$editCategory['cat_id'] ?>">
    <?php endif; ?>

    <label for="cat_name">Category Name</label>
    <input type="text" id="cat_name" name="cat_name" maxlength="100"
           value="<?= $editing ? htmlspecialchars($editCategory['cat_name']) : '' ?>" required>

    <button type="submit"><?= $editing ? 'Update Category' : 'Add Category' ?></button>
</form>

<?php if ($editing): ?>
    <p><a href="<?= BASE_URL ?>/views/admin/category.php">Cancel edit</a></p>
<?php endif; ?>

<h2>All Categories</h2>
<table class="admin-table">
    <thead><tr><th>ID</th><th>Name</th><th></th></tr></thead>
    <tbody>
        <?php if (empty($categories)): ?>
            <tr><td colspan="3">No categories yet.</td></tr>
        <?php else: ?>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= (int)$cat['cat_id'] ?></td>
                    <td><?= htmlspecialchars($cat['cat_name']) ?></td>
                    <td><a href="<?= BASE_URL ?>/views/admin/category.php?edit_id=<?= (int)$cat['cat_id'] ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../layout/footer.php'; ?>