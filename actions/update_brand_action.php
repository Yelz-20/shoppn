<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/brand.php');
}

$id   = $_POST['brand_id'] ?? '';
$name = trim(strip_tags($_POST['brand_name'] ?? ''));

if (!ctype_digit((string)$id) || (int)$id <= 0) {
    $_SESSION['error'] = 'Invalid brand.';
    redirect(BASE_URL . '/views/admin/brand.php');
}

if ($name === '' || strlen($name) > 100) {
    $_SESSION['error'] = 'Please enter a valid brand name.';
    redirect(BASE_URL . '/views/admin/brand.php?edit_id=' . (int)$id);
}

$controller = new ProductController();
$ok = $controller->updateBrand((int)$id, $name);

$_SESSION[$ok ? 'success' : 'error'] = $ok ? 'Brand updated.' : 'Update failed.';
redirect(BASE_URL . '/views/admin/brand.php');