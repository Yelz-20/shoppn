<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/product.php');
}

// Collect and validate product information.
$cat = filter_input(INPUT_POST, 'product_cat', FILTER_VALIDATE_INT);
$brand = filter_input(INPUT_POST, 'product_brand', FILTER_VALIDATE_INT);
$title = trim($_POST['product_title'] ?? '');
$priceInput = $_POST['product_price'] ?? '';
$desc = trim($_POST['product_desc'] ?? '');
$keywords = trim($_POST['product_keywords'] ?? '');

$controller = new ProductController();

if (
    !$cat ||
    !$brand ||
    $title === '' ||
    strlen($title) > 255 ||
    !is_numeric($priceInput) ||
    (float) $priceInput < 0 ||
    $desc === '' ||
    $keywords === ''
) {
    $_SESSION['error'] = 'Please enter valid product information.';
    redirect(BASE_URL . '/views/admin/product.php');
}

// Verify that the selected category exists.
$categoryExists = false;

foreach ($controller->getAllCategories() as $category) {
    if ((int) $category['cat_id'] === $cat) {
        $categoryExists = true;
        break;
    }
}

// Verify that the selected brand exists.
$brandExists = false;

foreach ($controller->getAllBrands() as $item) {
    if ((int) $item['brand_id'] === $brand) {
        $brandExists = true;
        break;
    }
}

if (!$categoryExists || !$brandExists) {
    $_SESSION['error'] = 'Please select a valid category and brand.';
    redirect(BASE_URL . '/views/admin/product.php');
}

// Validate the uploaded image.
if (
    !isset($_FILES['product_image']) ||
    $_FILES['product_image']['error'] !== UPLOAD_ERR_OK
) {
    $_SESSION['error'] = 'Please select a product image.';
    redirect(BASE_URL . '/views/admin/product.php');
}

$file = $_FILES['product_image'];
$maxSize = 5 * 1024 * 1024;

// Enforce the 5 MB image size limit.
if ($file['size'] <= 0 || $file['size'] > $maxSize) {
    $_SESSION['error'] = 'The image must be smaller than 5 MB.';
    redirect(BASE_URL . '/views/admin/product.php');
}

// Check the actual MIME type instead of trusting the filename.
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);

$allowed = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp'
];

// Allow only valid JPEG, PNG, GIF, and WebP images.
if (
    !isset($allowed[$mime]) ||
    @getimagesize($file['tmp_name']) === false
) {
    $_SESSION['error'] =
        'Only JPEG, PNG, GIF, and WebP images are allowed.';

    redirect(BASE_URL . '/views/admin/product.php');
}

// Set the destination directory.
$uploadDir = __DIR__ . '/../images/products/';

// Create the destination directory if it does not exist.
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
        $_SESSION['error'] =
            'The product image folder could not be created.';

        redirect(BASE_URL . '/views/admin/product.php');
    }
}

// Generate a unique filename to prevent accidental overwriting.
$filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];

$destination = $uploadDir . $filename;

// Move the uploaded image to the destination directory.
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    $_SESSION['error'] =
        'The product image could not be uploaded.';

    redirect(BASE_URL . '/views/admin/product.php');
}

// Save the product information in the database.
$id = $controller->addProduct(
    $cat,
    $brand,
    $title,
    (float) $priceInput,
    $desc,
    $filename,
    $keywords
);

// Handle the database result.
if ($id) {
    $_SESSION['success'] = 'Product added successfully.';
} else {
    // Remove the image if the database insert fails.
    if (is_file($destination)) {
        unlink($destination);
    }

    $_SESSION['error'] = 'Unable to add the product.';
}

// Return to the product management page.
redirect(BASE_URL . '/views/admin/product.php');