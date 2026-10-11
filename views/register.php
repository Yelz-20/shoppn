<?php
require_once __DIR__ . '/../core/core.php';

$error = pull_flash('error');

include __DIR__ . '/layout/header.php';
?>
<div class="auth-wrap">
    <h1>Create an account</h1>
    <p class="auth-sub">Join Shoppn to start shopping.</p>

    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form id="register-form" class="form" action="<?= BASE_URL ?>/actions/register_action.php" method="POST" novalidate>
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" maxlength="100" placeholder="John Doe">
        <span class="field-error" id="name-error"></span>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" maxlength="50" placeholder="johndoe@example.com">
        <span class="field-error" id="email-error"></span>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="At least 8 characters, one number">
        <span class="field-error" id="password-error"></span>

        <label for="country">Country</label>
        <select id="country" name="country">
            <option value="">Select your country</option>
            <option>Ghana</option>
            <option>Nigeria</option>
            <option>Kenya</option>
            <option>South Africa</option>
            <option>United Kingdom</option>
            <option>United States</option>
            <option>Other</option>
        </select>
        <span class="field-error" id="country-error"></span>

        <label for="city">City</label>
        <input type="text" id="city" name="city" maxlength="30" placeholder="Accra">
        <span class="field-error" id="city-error"></span>

        <label for="contact">Contact Number</label>
        <input type="text" id="contact" name="contact" maxlength="15" placeholder="0241234567">
        <span class="field-error" id="contact-error"></span>

        <button type="submit" id="register-btn">Create Account</button>
    </form>

    <p class="auth-foot">Already have an account? <a href="<?= BASE_URL ?>/views/login.php">Log in</a></p>
</div>

<script src="<?= BASE_URL ?>/js/validate.js"></script>
<?php include __DIR__ . '/layout/footer.php'; ?>