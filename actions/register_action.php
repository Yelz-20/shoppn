<?php
// This is where the register form actually lands. Reads $_POST, checks it, hands off to the Controller, then redirects somewhere. Nothing here touches SQL and nothing here prints HTML.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

// someone visiting this URL directly (not via the form) just gets bounced back
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/register.php');
}

// Clean up whatever was submitted, trim spaces, strip any stray HTML tags. Password is left alone since it gets hashed right after, not stored raw.
$name     = trim(strip_tags($_POST['name'] ?? ''));
$email    = strtolower(trim(strip_tags($_POST['email'] ?? '')));
$password = $_POST['password'] ?? '';
$country  = trim(strip_tags($_POST['country'] ?? ''));
$city     = trim(strip_tags($_POST['city'] ?? ''));
$contact  = trim(strip_tags($_POST['contact'] ?? ''));

// Same checks as validate.js, just run again here because JS can be turned off or bypassed. The server can't just trust the browser did its job
$errors = [];
if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
    $errors[] = 'Enter your full name (2 to 100 characters).';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 50) {
    $errors[] = 'Enter a valid email address (50 characters or fewer).';
}
if (!preg_match('/^(?=.*\d).{8,72}$/', $password)) {
    $errors[] = 'Password must be 8 to 72 characters and include a number.';
}
if ($country === '' || mb_strlen($country) > 30) {
    $errors[] = 'Choose your country.';
}
if (mb_strlen($city) < 2 || mb_strlen($city) > 30) {
    $errors[] = 'Enter your city (2 to 30 characters).';
}
if (!preg_match('/^[0-9+\-\s]{7,15}$/', $contact)) {
    $errors[] = 'Enter a valid contact number (7 to 15 digits).';
}

if ($errors) {
    // send them back with the errors and whatever they already typed, so they're not stuck retyping the whole form again
    $_SESSION['error'] = implode(' ', $errors);
    $_SESSION['old'] = compact('name', 'email', 'country', 'city', 'contact');
    redirect('views/register.php');
}

$controller = new CustomerController();
$result = $controller->register([
    'name' => $name, 'email' => $email, 'password' => $password,
    'country' => $country, 'city' => $city, 'contact' => $contact,
]);

if ($result['success']) {
    start_user_session($result['customer']);
    redirect('views/account/my_account.php');
}

// most likely reason this fails: email already taken
$_SESSION['error'] = $result['error'];
$_SESSION['old'] = compact('name', 'email', 'country', 'city', 'contact');
redirect('views/register.php');