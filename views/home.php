<?php
// Just a placeholder for now until the product listing task adds real content; mainly here to prove the header/footer/flash messages work
require_once __DIR__ . '/../core/core.php';
$page_title = 'Home';

$error   = pull_flash('error');
$success = pull_flash('success');

include __DIR__ . '/layout/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

<h1>Welcome to Shoppn</h1>
<p>Products will appear here once the product display task is built.</p>

<?php include __DIR__ . '/layout/footer.php'; ?>