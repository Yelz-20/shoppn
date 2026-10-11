<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Africa/Accra');

define('BASE_URL', '/~yelsom.sanid/shoppn');
define('ROOT_PATH', dirname(__DIR__));
define('DEBUG', true);

ini_set('log_errors', '1');
ini_set('error_log', ROOT_PATH . '/error/error.log');
ini_set('display_errors', DEBUG ? '1' : '0');
error_reporting(E_ALL);

require_once __DIR__ . '/db_class.php';

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function get_ip()
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function is_logged_in()
{
    return isset($_SESSION['customer_id']);
}

function is_admin()
{
    return isset($_SESSION['user_role']) && (int)$_SESSION['user_role'] === 1;
}

function require_login()
{
    if (!is_logged_in()) {
        $_SESSION['error'] = 'Please log in to continue.';
        redirect(BASE_URL . '/views/login.php');
    }
}

function require_admin()
{
    if (!is_admin()) {
        $_SESSION['error'] = 'Access denied. Admins only.';
        redirect(BASE_URL . '/index.php');
    }
}

function pull_flash($key)
{
    if (!empty($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }

    return null;
}