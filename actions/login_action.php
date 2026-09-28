<?php
// Same idea as register_action.php but for logging in.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/login.php');
}

$email    = strtolower(trim(strip_tags($_POST['email'] ?? '')));
$password = $_POST['password'] ?? '';

// quick check before even hitting the database
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    $_SESSION['error'] = 'Enter your email and password.';
    redirect('views/login.php');
}

$controller = new CustomerController();
$result = $controller->login($email, $password);

if ($result['success']) {
    start_user_session($result['customer']);
    redirect('index.php');
}

// Deliberately vague on purpose — "invalid email or password" either way, so nobody can use this form to figure out which emails are registered
$_SESSION['error'] = $result['error'];
redirect('views/login.php');