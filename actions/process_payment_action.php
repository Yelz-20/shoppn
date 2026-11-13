
<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once '/home/yelsom.sanid/paystack_config.php';

require_login();

$pending = $_SESSION['pending_payment'] ?? null;
$reference = trim($_GET['reference'] ?? '');

if (
    !$pending ||
    empty($pending['reference']) ||
    !hash_equals($pending['reference'], $reference) ||
    (int) $pending['customer_id'] !== (int) $_SESSION['customer_id']
) {
    $_SESSION['error'] = 'Payment session could not be confirmed.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

if (!function_exists('curl_init')) {
    $_SESSION['error'] = 'Payment verification is unavailable.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

$url = 'https://api.paystack.co/transaction/verify/' .
       rawurlencode($reference);

$curl = curl_init($url);

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
        'Cache-Control: no-cache'
    ],
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curl_error = curl_error($curl);

curl_close($curl);

if ($response === false || $http_code !== 200) {
    error_log('Paystack verification error: ' . $curl_error);

    $_SESSION['error'] =
        'We could not confirm your payment. Keep your payment reference and contact support if you were charged.';

    redirect(BASE_URL . '/views/payment_failed.php');
}

$result = json_decode($response, true);

if (
    empty($result['status']) ||
    ($result['data']['status'] ?? '') !== 'success'
) {
    $_SESSION['error'] = 'Your payment was not successful.';
    unset($_SESSION['pending_payment']);

    redirect(BASE_URL . '/views/payment_failed.php');
}

$data = $result['data'];

$verified_amount = (int) ($data['amount'] ?? 0);
$expected_amount = (int) $pending['amount'];

if (
    ($data['reference'] ?? '') !== $reference ||
    strtoupper($data['currency'] ?? '') !== 'GHS' ||
    $verified_amount !== $expected_amount ||
    ($data['domain'] ?? '') !== 'test'
) {
    $_SESSION['error'] =
        'Payment details could not be confirmed. Please contact support with your reference: ' . $reference;

    redirect(BASE_URL . '/views/payment_failed.php');
}

$controller = new CartController();

$order_id = $controller->processPayment(
    (int) $pending['customer_id'],
    $pending['ip'],
    $reference,
    $verified_amount
);

if (!$order_id) {
    $_SESSION['error'] =
        'Your payment was verified, but your order could not be saved. Do not pay again. Contact support with reference: ' . $reference;

    redirect(BASE_URL . '/views/payment_failed.php');
}

$_SESSION['last_order_id'] = $order_id;

unset($_SESSION['pending_payment']);
unset($_SESSION['payment_csrf']);

redirect(BASE_URL . '/views/payment_success.php');
