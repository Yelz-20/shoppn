<?php
// Loaded at the top of literally every page. Starts the session, sets up a few constants, and holds the small helper functions used everywhere else. Nothing in here touches the database or prints HTML because it's just plumbing.

define('ROOT_PATH', dirname(__DIR__));

date_default_timezone_set('Africa/Accra');

// Send PHP errors to our own log file instead of printing them on screen , substr($_SERVER['SCRIPT_NAME'], 0, strlen($_SERVER['SCRIPT_NAME']) - strlen($__rel)) . '/'
ini_set('log_errors', '1');
ini_set('error_log', ROOT_PATH . '/error/error.log');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Figures out the web path to the project root automatically, so links still work whether we're on localhost/shoppn/ or on the live server under /~username/shoppn/
$__rel = str_replace('\\', '/', substr(realpath($_SERVER['SCRIPT_FILENAME']), strlen(realpath(ROOT_PATH))));
define('BASE_URL', '/shoppn/');
unset($__rel);

require_once __DIR__ . '/db_class.php';

// ---- little helpers used all over the views ----

// Wrap any user-typed text in this before printing it, stops XSS
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function log_error($message) {
    error_log($message);
}

function get_ip() {
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// Send the browser somewhere else and stop the script. every action file ends with a call to this.
function redirect($path) {
    if (!preg_match('#^https?://#', $path)) {
        $path = BASE_URL . ltrim($path, '/');
    }
    header('Location: ' . $path);
    exit;
}

// Grabs a session value and deletes it in the same step. This is used for error messages so they only show up once, not every time the page reloads
function pull_flash($key) {
    $value = $_SESSION[$key] ?? null;
    unset($_SESSION[$key]);
    return $value;
}

// ---- login/permission checks ----

function is_logged_in() {
    return isset($_SESSION['customer_id']);
}

function is_admin() {
    return isset($_SESSION['user_role']) && (int)$_SESSION['user_role'] === 1;
}

function require_login() {
    if (!is_logged_in()) {
        $_SESSION['error'] = 'Please log in to continue.';
        redirect('views/login.php');
    }
}

function require_admin() {
    if (!is_admin()) {
        $_SESSION['error'] = 'Access denied. Admins only.';
        redirect('index.php');
    }
}

// Puts the logged-in customer's info into the session. Regenerating the session id here is a small security habit to stop old sessions being reused to hijack a login.
function start_user_session(array $customer) {
    session_regenerate_id(true);
    $_SESSION['customer_id']    = (int)$customer['customer_id'];
    $_SESSION['customer_name']  = $customer['customer_name'];
    $_SESSION['customer_email'] = $customer['customer_email'];
    $_SESSION['user_role']      = (int)$customer['user_role'];
}