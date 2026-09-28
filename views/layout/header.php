<?php
// Top of every page: head tag, nav bar, search box. core.php has to already be loaded by whatever page includes this, since it uses BASE_URL / is_logged_in() / e() from there.
$page_title = $page_title ?? 'Home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?> | Shoppn</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
<header class="site-header">
    <a class="logo" href="<?= BASE_URL ?>index.php">Shoppn</a>

    <!-- search_results.php comes in a later task -->
    <form class="search" action="<?= BASE_URL ?>views/search_results.php" method="GET">
        <input type="text" name="user_query" placeholder="Search products">
        <button type="submit">Search</button>
    </form>

    <nav>
        <?php if (is_logged_in()): ?>
            <span>Welcome <?= e($_SESSION['customer_name']) ?></span>
            <a href="<?= BASE_URL ?>views/account/my_account.php">My Account</a>
            <a href="<?= BASE_URL ?>logout.php">Logout</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>views/register.php">Register</a>
            <a href="<?= BASE_URL ?>views/login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">