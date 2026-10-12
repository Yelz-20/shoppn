<?php /* Shared header: HTML + minimal PHP for session-based nav. No SQL. */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shoppn</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=<?= time() ?>">
</head>
<body>
<header class="site-header">
    <a class="logo" href="<?= BASE_URL ?>/index.php">
        <img src="<?= BASE_URL ?>/images/Shoppn%20Logo.jpg"
             alt="Shoppn"
             class="logo-img"
             width="40" height="40"
             style="width:40px;height:40px;max-width:40px;max-height:40px;object-fit:cover;border-radius:10px;">
        <span>Shoppn</span>
    </a>

    <form class="search" action="<?= BASE_URL ?>/views/search_results.php" method="GET">
        <input type="text" name="user_query" placeholder="Search products...">
        <button type="submit">Search</button>
    </form>

    <nav>
        <a href="<?= BASE_URL ?>/index.php">Home</a>
        <a href="<?= BASE_URL ?>/views/all_products.php">Products</a>
        <a href="<?= BASE_URL ?>/views/brands.php">Brands</a>
        <a href="<?= BASE_URL ?>/views/categories.php">Categories</a>

        <?php if (is_logged_in()): ?>
            <span>Welcome <?= htmlspecialchars($_SESSION['customer_name']) ?></span>
            <a href="<?= BASE_URL ?>/views/account/my_account.php">My Account</a>

            <?php if (is_admin()): ?>
                <a href="<?= BASE_URL ?>/views/admin/brand.php">Manage Brands</a>
                <a href="<?= BASE_URL ?>/views/admin/category.php">Manage Categories</a>
                <a href="<?= BASE_URL ?>/views/admin/product.php">Manage Products</a>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>/logout.php">Logout</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/views/register.php">Register</a>
            <a href="<?= BASE_URL ?>/views/login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<main class="container">