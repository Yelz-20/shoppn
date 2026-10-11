<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/category.php');
}

$id   = $_POST['cat_id'] ?? '';
$name = trim(strip_tags($_POST['cat_name'] ?? ''));

if (!ctype_digit((string)$id) || (int)$id <= 0) {
    $_SESSION['error'] = 'Invalid category.';
    redirect(BASE_URL . '/views/admin/category.php');
}

if ($name === '' || strlen($name) > 100) {
    $_SESSION['error'] = 'Please enter a valid category name.';
    redirect(BASE_URL . '/views/admin/category.php?edit_id=' . (int)$id);
}

$controller = new ProductController();
$ok = $controller->updateCategory((int)$id, $name);

$_SESSION[$ok ? 'success' : 'error'] = $ok ? 'Category updated.' : 'Update failed.';
redirect(BASE_URL . '/views/admin/category.php');