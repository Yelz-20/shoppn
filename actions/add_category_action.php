<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/category.php');
}

$name = trim(strip_tags($_POST['cat_name'] ?? ''));

if ($name === '' || strlen($name) > 100) {
    $_SESSION['error'] = 'Please enter a valid category name.';
    redirect(BASE_URL . '/views/admin/category.php');
}

$controller = new ProductController();
$id = $controller->addCategory($name);

$_SESSION[$id ? 'success' : 'error'] = $id ? 'Category added.' : 'Failed to add category.';
redirect(BASE_URL . '/views/admin/category.php');