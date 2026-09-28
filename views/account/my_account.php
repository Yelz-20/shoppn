<?php
// Landing page after a successful login/register. require_login() up top means if user is not logged in and try to open this URL directly, user will just get sent to the login page instead.
require_once __DIR__ . '/../../core/core.php';
require_login();

$page_title = 'My Account';
include __DIR__ . '/../layout/header.php';
?>

<h1>My account</h1>
<p><strong>Name:</strong> <?= e($_SESSION['customer_name']) ?></p>
<p><strong>Email:</strong> <?= e($_SESSION['customer_email']) ?></p>
<p><strong>Account type:</strong> <?= is_admin() ? 'Admin' : 'Customer' ?></p>

<?php include __DIR__ . '/../layout/footer.php'; ?>