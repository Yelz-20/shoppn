<?php
// Login form. Same deal as register.php, just displays things.
require_once __DIR__ . '/../core/core.php';

if (is_logged_in()) {
    redirect('index.php');
}

$page_title = 'Login';
$error = pull_flash('error');

include __DIR__ . '/layout/header.php';
?>

<div class="form-card">
    <h1>Log in</h1>

    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <form id="login-form" action="../actions/login_action.php" method="POST" novalidate>
        <div class="field">
            <label for="login-email">Email</label>
            <input type="email" id="login-email" name="email">
            <span class="field-error" id="login-email-error"></span>
        </div>

        <div class="field">
            <label for="login-password">Password</label>
            <input type="password" id="login-password" name="password">
            <span class="field-error" id="login-password-error"></span>
        </div>

        <button type="submit" id="login-btn">Log in</button>
    </form>

    <p class="form-note">New here? <a href="register.php">Create an account</a></p>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>