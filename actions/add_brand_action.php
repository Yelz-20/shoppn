<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/brand.php');
}

$name = trim(strip_tags($_POST['brand_name'] ?? ''));

if ($name === '' || strlen($name) > 100) {
    $_SESSION['error'] = 'Please enter a valid brand name.';
    redirect(BASE_URL . '/views/admin/brand.php');
}

$controller = new ProductController();
$id = $controller->addBrand($name);

$_SESSION[$id ? 'success' : 'error'] = $id ? 'Brand added.' : 'Failed to add brand.';
redirect(BASE_URL . '/views/admin/brand.php');