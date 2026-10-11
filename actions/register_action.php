<?php
// This is where the registration form is processed.
// It reads the submitted form data, validates it, sends it to the Controller,
// starts a session for a successful registration, then redirects the user.

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

// If someone opens this URL directly instead of submitting the registration form,
// send them back to the registration page.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/register.php');
}

// Clean and collect the submitted information.
// The password is not changed here because it is hashed in the Controller.
$name     = trim(strip_tags($_POST['name'] ?? ''));
$email    = strtolower(trim(strip_tags($_POST['email'] ?? '')));
$password = $_POST['password'] ?? '';
$country  = trim(strip_tags($_POST['country'] ?? ''));
$city     = trim(strip_tags($_POST['city'] ?? ''));
$contact  = trim(strip_tags($_POST['contact'] ?? ''));

// Validate the submitted information again on the server.
// JavaScript validation can be bypassed, so PHP must also validate it.
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

// If validation failed, save the error and return the user to the form.
if ($errors) {
    $_SESSION['error'] = implode(' ', $errors);
    $_SESSION['old'] = compact('name', 'email', 'country', 'city', 'contact');

    redirect(BASE_URL . '/views/register.php');
}

// Send the clean registration data to the Controller.
$controller = new CustomerController();

$result = $controller->register([
    'name'    => $name,
    'email'   => $email,
    'password'=> $password,
    'country' => $country,
    'city'    => $city,
    'contact' => $contact,
]);

// If registration worked, store the new customer details in the session.
// This automatically logs the new customer in.
if (!empty($result['success'])) {
    session_regenerate_id(true);

    $customer = $result['customer'];

    $_SESSION['customer_id']    = (int)$customer['customer_id'];
    $_SESSION['customer_name']  = $customer['customer_name'];
    $_SESSION['customer_email'] = $customer['customer_email'];
    $_SESSION['user_role']      = (int)$customer['user_role'];

    redirect(BASE_URL . '/views/account/my_account.php');
}

// If registration failed, show the error and preserve the non-password fields.
$_SESSION['error'] = $result['error'] ?? 'Registration failed. Please try again.';
$_SESSION['old'] = compact('name', 'email', 'country', 'city', 'contact');

redirect(BASE_URL . '/views/register.php');