<?php
require_once __DIR__ . '/../core/core.php';

$success = pull_flash('success');
$error   = pull_flash('error');

include __DIR__ . '/layout/header.php';
?>

<?php if ($success): ?>
    <div class="alert success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<h1>Welcome to Shoppn</h1>
<p>Products will appear here once the product display task is built.</p>

<?php include __DIR__ . '/layout/footer.php'; ?>