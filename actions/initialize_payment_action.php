
<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once '/home/yelsom.sanid/paystack_config.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/cart.php');
}

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['payment_csrf']) ||
    !hash_equals($_SESSION['payment_csrf'], $_POST['csrf_token'])
) {
    $_SESSION['error'] = 'Invalid payment request.';
    redirect(BASE_URL . '/views/cart.php');
}

if (!function_exists('curl_init')) {
    $_SESSION['error'] = 'Payment service is unavailable.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

$controller = new CartController();

$customer_id = (int) $_SESSION['customer_id'];
$ip = get_ip();

$email = $controller->getCustomerEmail($customer_id);
$items = $controller->getCartItems($ip);
$total = $controller->getCartTotal($ip);

if (!$email || empty($items) || $total <= 0) {
    $_SESSION['error'] = 'Your cart or customer information is invalid.';
    redirect(BASE_URL . '/views/cart.php');
}

$amount_pesewas = (int) round($total * 100);

$reference = 'SHOPPN_' . bin2hex(random_bytes(16));

$callback_url =
    'http://' . $_SERVER['HTTP_HOST'] .
    BASE_URL . '/actions/process_payment_action.php';

$payload = [
    'email' => $email,
    'amount' => (string) $amount_pesewas,
    'currency' => 'GHS',
    'reference' => $reference,
    'callback_url' => $callback_url
];

$curl = curl_init('https://api.paystack.co/transaction/initialize');

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
        'Content-Type: application/json',
        'Cache-Control: no-cache'
    ],
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curl_error = curl_error($curl);

curl_close($curl);

if ($response === false || $http_code !== 200) {
    error_log('Paystack initialization error: ' . $curl_error);

    $_SESSION['error'] = 'Unable to start payment. Please try again.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

$result = json_decode($response, true);

if (
    empty($result['status']) ||
    empty($result['data']['authorization_url']) ||
    empty($result['data']['reference']) ||
    $result['data']['reference'] !== $reference
) {
    $_SESSION['error'] = 'Paystack could not initialize your payment.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

$_SESSION['pending_payment'] = [
    'reference' => $reference,
    'amount' => $amount_pesewas,
    'customer_id' => $customer_id,
    'ip' => $ip
];

header('Location: ' . $result['data']['authorization_url']);
exit;
