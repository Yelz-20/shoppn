<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/login.php');
}

$email    = trim(strip_tags($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Please enter a valid email and password.';
    redirect(BASE_URL . '/views/login.php');
}

$controller = new CustomerController();
$result = $controller->login($email, $password);

if (!empty($result['success'])) {
    session_regenerate_id(true);
    $c = $result['customer'];
    $_SESSION['customer_id']    = (int)$c['customer_id'];
    $_SESSION['customer_name']  = $c['customer_name'];
    $_SESSION['customer_email'] = $c['customer_email'];
    $_SESSION['user_role']      = (int)$c['user_role'];
    redirect(BASE_URL . '/index.php');
}

$_SESSION['error'] = $result['error'] ?? 'Invalid email or password.';
redirect(BASE_URL . '/views/login.php');