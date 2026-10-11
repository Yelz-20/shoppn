<?php
require_once __DIR__ . '/../../core/core.php';
require_admin();
require_once __DIR__ . '/../../controllers/ProductController.php';

$controller = new ProductController();

$editing = false;
$editBrand = null;
if (isset($_GET['edit_id']) && ctype_digit($_GET['edit_id'])) {
    $editBrand = $controller->getBrandById((int)$_GET['edit_id']);
    $editing = $editBrand !== false && $editBrand !== null;
}

$brands = $controller->getAllBrands();

include __DIR__ . '/../layout/header.php';
?>
<h1>Manage Brands</h1>

<?php if ($s = pull_flash('success')): ?>
    <div class="alert success"><?= htmlspecialchars($s) ?></div>
<?php endif; ?>
<?php if ($e = pull_flash('error')): ?>
    <div class="alert error"><?= htmlspecialchars($e) ?></div>
<?php endif; ?>

<h2><?= $editing ? 'Edit Brand' : 'Add Brand' ?></h2>
<form class="form"
      action="<?= BASE_URL ?>/actions/<?= $editing ? 'update_brand_action.php' : 'add_brand_action.php' ?>"
      method="POST">

    <?php if ($editing): ?>
        <input type="hidden" name="brand_id" value="<?= (int)$editBrand['brand_id'] ?>">
    <?php endif; ?>

    <label for="brand_name">Brand Name</label>
    <input type="text" id="brand_name" name="brand_name" maxlength="100"
           value="<?= $editing ? htmlspecialchars($editBrand['brand_name']) : '' ?>" required>

    <button type="submit"><?= $editing ? 'Update Brand' : 'Add Brand' ?></button>
</form>

<?php if ($editing): ?>
    <p><a href="<?= BASE_URL ?>/views/admin/brand.php">Cancel edit</a></p>
<?php endif; ?>

<h2>All Brands</h2>
<table class="admin-table">
    <thead><tr><th>ID</th><th>Name</th><th></th></tr></thead>
    <tbody>
        <?php if (empty($brands)): ?>
            <tr><td colspan="3">No brands yet.</td></tr>
        <?php else: ?>
            <?php foreach ($brands as $brand): ?>
                <tr>
                    <td><?= (int)$brand['brand_id'] ?></td>
                    <td><?= htmlspecialchars($brand['brand_name']) ?></td>
                    <td><a href="<?= BASE_URL ?>/views/admin/brand.php?edit_id=<?= (int)$brand['brand_id'] ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../layout/footer.php'; ?>