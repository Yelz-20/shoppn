<?php
// The register form itself, just HTML plus enough PHP to loop over the country list and show old values/errors. Doesn't touch the database, that all happens over in register_action.php.
require_once __DIR__ . '/../core/core.php';

if (is_logged_in()) {
    redirect('index.php'); // no point showing the register form if already logged in
}

$page_title = 'Register';
$error = pull_flash('error');
$old   = pull_flash('old') ?: []; // whatever they typed last time, so they don't lose it

$countries = ['Ghana', 'Nigeria', 'Kenya', 'South Africa', 'Ivory Coast', 'Senegal', 'Togo', 'Benin',
              'Cameroon', 'Uganda', 'Tanzania', 'Rwanda', 'Egypt', 'United Kingdom', 'United States', 'Other'];

include __DIR__ . '/layout/header.php';
?>

<div class="form-card">
    <h1>Create an account</h1>

    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <!-- novalidate because we're doing our own validation in JS + PHP -->
    <form id="register-form" action="../actions/register_action.php" method="POST" novalidate>
        <div class="field">
            <label for="name">Full name</label>
            <input type="text" id="name" name="name" maxlength="100" value="<?= e($old['name'] ?? '') ?>">
            <span class="field-error" id="name-error"></span>
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" maxlength="50" value="<?= e($old['email'] ?? '') ?>">
            <span class="field-error" id="email-error"></span>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <!-- not re-filling this one after a failed submit, for obvious reasons -->
            <input type="password" id="password" name="password" maxlength="72">
            <span class="hint">At least 8 characters, including a number.</span>
            <span class="field-error" id="password-error"></span>
        </div>

        <div class="field">
            <label for="country">Country</label>
            <select id="country" name="country">
                <option value="">Choose your country</option>
                <?php foreach ($countries as $c): ?>
                    <option value="<?= e($c) ?>" <?= (($old['country'] ?? '') === $c) ? 'selected' : '' ?>><?= e($c) ?></option>
                <?php endforeach; ?>
            </select>
            <span class="field-error" id="country-error"></span>
        </div>

        <div class="field">
            <label for="city">City</label>
            <input type="text" id="city" name="city" maxlength="30" value="<?= e($old['city'] ?? '') ?>">
            <span class="field-error" id="city-error"></span>
        </div>

        <div class="field">
            <label for="contact">Contact number</label>
            <input type="text" id="contact" name="contact" maxlength="15" value="<?= e($old['contact'] ?? '') ?>">
            <span class="field-error" id="contact-error"></span>
        </div>

        <button type="submit" id="register-btn">Create account</button>
    </form>

    <p class="form-note">Already have an account? <a href="login.php">Log in</a></p>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>