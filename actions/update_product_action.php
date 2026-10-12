<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/product.php');
}

$id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$cat = filter_input(INPUT_POST, 'product_cat', FILTER_VALIDATE_INT);
$brand = filter_input(INPUT_POST, 'product_brand', FILTER_VALIDATE_INT);
$title = trim($_POST['product_title'] ?? '');
$priceInput = $_POST['product_price'] ?? '';
$desc = trim($_POST['product_desc'] ?? '');
$keywords = trim($_POST['product_keywords'] ?? '');

$controller = new ProductController();

if (!$id || !$cat || !$brand) {
    $_SESSION['error'] = 'Invalid product, category, or brand.';
    redirect(BASE_URL . '/views/admin/product.php');
}

$existing = $controller->getProductById($id);

if (!$existing) {
    $_SESSION['error'] = 'Product not found.';
    redirect(BASE_URL . '/views/admin/product.php');
}

if (
    $title === '' || strlen($title) > 255 ||
    !is_numeric($priceInput) ||
    (float)$priceInput < 0 ||
    $desc === '' || $keywords === ''
) {
    $_SESSION['error'] = 'Please enter valid product information.';
    redirect(BASE_URL . '/views/admin/product.php?edit_id=' . $id);
}

$categoryExists = false;
foreach ($controller->getAllCategories() as $category) {
    if ((int)$category['cat_id'] === $cat) {
        $categoryExists = true;
        break;
    }
}

$brandExists = false;
foreach ($controller->getAllBrands() as $item) {
    if ((int)$item['brand_id'] === $brand) {
        $brandExists = true;
        break;
    }
}

if (!$categoryExists || !$brandExists) {
    $_SESSION['error'] = 'Please select a valid category and brand.';
    redirect(BASE_URL . '/views/admin/product.php?edit_id=' . $id);
}

// Default to the existing image.
$filename = $existing['product_image'];
$uploadDir = __DIR__ . '/../images/products/';

if (
    isset($_FILES['product_image']) &&
    $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE
) {
    $file = $_FILES['product_image'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'The new image upload failed.';
        redirect(BASE_URL . '/views/admin/product.php?edit_id=' . $id);
    }

    if ($file['size'] <= 0 || $file['size'] > 5 * 1024 * 1024) {
        $_SESSION['error'] = 'The image must be smaller than 5 MB.';
        redirect(BASE_URL . '/views/admin/product.php?edit_id=' . $id);
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp'
    ];

    if (
        !isset($allowed[$mime]) ||
        @getimagesize($file['tmp_name']) === false
    ) {
        $_SESSION['error'] = 'Only JPEG, PNG, GIF, and WebP images are allowed.';
        redirect(BASE_URL . '/views/admin/product.php?edit_id=' . $id);
    }

    if (!is_dir($uploadDir) &&
        !mkdir($uploadDir, 0755, true) &&
        !is_dir($uploadDir)
    ) {
        $_SESSION['error'] = 'The image folder could not be created.';
        redirect(BASE_URL . '/views/admin/product.php?edit_id=' . $id);
    }

    $newFilename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];

    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $newFilename)) {
        $_SESSION['error'] = 'The new image could not be saved.';
        redirect(BASE_URL . '/views/admin/product.php?edit_id=' . $id);
    }

    $filename = $newFilename;
}

$ok = $controller->updateProduct(
    $id,
    $cat,
    $brand,
    $title,
    (float)$priceInput,
    $desc,
    $filename,
    $keywords
);

if ($ok) {
    // Delete the previous image only after the database update succeeds.
    if (
        $filename !== $existing['product_image'] &&
        !empty($existing['product_image'])
    ) {
        $oldPath = $uploadDir . basename($existing['product_image']);

        if (is_file($oldPath)) {
            unlink($oldPath);
        }
    }

    $_SESSION['success'] = 'Product updated successfully.';
    redirect(BASE_URL . '/views/admin/product.php');
}

// Clean up a newly uploaded image if the database update failed.
if ($filename !== $existing['product_image']) {
    $newPath = $uploadDir . basename($filename);

    if (is_file($newPath)) {
        unlink($newPath);
    }
}

$_SESSION['error'] = 'Unable to update the product.';
redirect(BASE_URL . '/views/admin/product.php?edit_id=' . $id);