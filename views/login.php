<?php
require_once __DIR__ . '/../core/core.php';

$error = pull_flash('error');

include __DIR__ . '/layout/header.php';
?>
<div class="auth-wrap">
    <h1>Welcome back</h1>
    <p class="auth-sub">Log in to your Shoppn account.</p>

    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form id="login-form" class="form" action="<?= BASE_URL ?>/actions/login_action.php" method="POST" novalidate>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="you@example.com">
        <span class="field-error" id="email-error"></span>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Your password">
        <span class="field-error" id="password-error"></span>

        <button type="submit" id="login-btn">Log In</button>
    </form>

    <p class="auth-foot">New here? <a href="<?= BASE_URL ?>/views/register.php">Create an account</a></p>
</div>

<script src="<?= BASE_URL ?>/js/validate.js"></script>
<?php include __DIR__ . '/layout/footer.php'; ?>