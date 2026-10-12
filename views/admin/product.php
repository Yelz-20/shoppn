<?php
require_once __DIR__ . '/../../core/core.php';
require_admin();
require_once __DIR__ . '/../../controllers/ProductController.php';

$controller = new ProductController();

$editing = false;
$editProduct = null;

if (isset($_GET['edit_id']) && ctype_digit((string)$_GET['edit_id'])) {
    $editProduct = $controller->getProductById((int)$_GET['edit_id']);
    $editing = $editProduct !== false;
}

$categories = $controller->getAllCategories();
$brands = $controller->getAllBrands();
$products = $controller->getAllProducts();

include __DIR__ . '/../layout/header.php';
?>

<h1>Manage Products</h1>

<?php if ($s = pull_flash('success')): ?>
    <div class="alert success"><?= htmlspecialchars($s) ?></div>
<?php endif; ?>

<?php if ($e = pull_flash('error')): ?>
    <div class="alert error"><?= htmlspecialchars($e) ?></div>
<?php endif; ?>

<h2><?= $editing ? 'Edit Product' : 'Add Product' ?></h2>

<?php if (!$categories || !$brands): ?>
    <p>Please add at least one category and one brand before adding products.</p>
<?php else: ?>

<form class="form"
      id="productForm"
      action="<?= BASE_URL ?>/actions/<?= $editing ? 'update_product_action.php' : 'add_product_action.php' ?>"
      method="POST"
      enctype="multipart/form-data">

    <?php if ($editing): ?>
        <input type="hidden" name="product_id"
               value="<?= (int)$editProduct['product_id'] ?>">
    <?php endif; ?>

    <label for="product_title">Product Title</label>
    <input type="text"
           id="product_title"
           name="product_title"
           maxlength="255"
           value="<?= $editing ? htmlspecialchars($editProduct['product_title']) : '' ?>"
           required>

    <label for="product_price">Price</label>
    <input type="number"
           id="product_price"
           name="product_price"
           min="0"
           step="0.01"
           value="<?= $editing ? htmlspecialchars((string)$editProduct['product_price']) : '' ?>"
           required>

    <label for="product_desc">Description</label>
    <textarea id="product_desc"
              name="product_desc"
              rows="5"
              required><?= $editing ? htmlspecialchars($editProduct['product_desc']) : '' ?></textarea>

    <label for="product_keywords">Keywords</label>
    <input type="text"
           id="product_keywords"
           name="product_keywords"
           value="<?= $editing ? htmlspecialchars($editProduct['product_keywords']) : '' ?>"
           required>

    <label for="product_cat">Category</label>
    <select id="product_cat" name="product_cat" required>
        <option value="">Choose a category</option>

        <?php foreach ($categories as $category): ?>
            <option value="<?= (int)$category['cat_id'] ?>"
                <?= $editing && (int)$editProduct['product_cat'] === (int)$category['cat_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['cat_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="product_brand">Brand</label>
    <select id="product_brand" name="product_brand" required>
        <option value="">Choose a brand</option>

        <?php foreach ($brands as $brand): ?>
            <option value="<?= (int)$brand['brand_id'] ?>"
                <?= $editing && (int)$editProduct['product_brand'] === (int)$brand['brand_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($brand['brand_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php if ($editing && !empty($editProduct['product_image'])): ?>
        <p>Current image:</p>
        <img
            src="<?= BASE_URL ?>/images/products/<?= rawurlencode(basename($editProduct['product_image'])) ?>"
            alt="Current product image"
            style="width:150px;max-height:150px;object-fit:contain;"
        >
    <?php endif; ?>

    <label for="product_image">
        <?= $editing ? 'Replace Product Image (optional)' : 'Product Image' ?>
    </label>

    <input type="file"
           id="product_image"
           name="product_image"
           accept="image/jpeg,image/png,image/gif,image/webp"
           <?= $editing ? '' : 'required' ?>>

    <small>Allowed formats: JPEG, PNG, GIF and WebP. Maximum size: 5 MB.</small>

    <br><br>

    <button type="submit">
        <?= $editing ? 'Update Product' : 'Add Product' ?>
    </button>
</form>

<?php endif; ?>

<?php if ($editing): ?>
    <p><a href="<?= BASE_URL ?>/views/admin/product.php">Cancel edit</a></p>
<?php endif; ?>

<h2>All Products</h2>

<div style="overflow-x:auto;">
<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Product</th>
            <th>Category</th>
            <th>Brand</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
    <?php if (empty($products)): ?>
        <tr><td colspan="7">No products have been added yet.</td></tr>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= (int)$product['product_id'] ?></td>

                <td>
                    <?php if (!empty($product['product_image'])): ?>
                        <img
                            src="<?= BASE_URL ?>/images/products/<?= rawurlencode(basename($product['product_image'])) ?>"
                            alt="<?= htmlspecialchars($product['product_title']) ?>"
                            style="width:55px;height:55px;object-fit:contain;"
                        >
                    <?php endif; ?>
                </td>

                <td><?= htmlspecialchars($product['product_title']) ?></td>
                <td><?= htmlspecialchars($product['cat_name'] ?? '') ?></td>
                <td><?= htmlspecialchars($product['brand_name'] ?? '') ?></td>
                <td><?= number_format((float)$product['product_price'], 2) ?></td>

                <td>
                    <a href="<?= BASE_URL ?>/views/admin/product.php?edit_id=<?= (int)$product['product_id'] ?>">
                        Edit
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
</div>

<script>
document.getElementById('productForm')?.addEventListener('submit', function (event) {
    const input = document.getElementById('product_image');
    const file = input.files[0];

    if (!file) {
        <?php if (!$editing): ?>
        alert('Please select a product image.');
        event.preventDefault();
        return;
        <?php endif; ?>

        return;
    }

    const allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];

    if (!allowedTypes.includes(file.type)) {
        alert('Please select a JPEG, PNG, GIF, or WebP image.');
        event.preventDefault();
        return;
    }

    if (file.size > 5 * 1024 * 1024) {
        alert('The image must be smaller than 5 MB.');
        event.preventDefault();
    }
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>