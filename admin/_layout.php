<?php
/**
 * Shared admin layout helpers.
 */

require_once __DIR__ . '/../includes/auth.php';

function admin_header(string $title, string $active = ''): void
{
    auth_require();
    $user = auth_username();
    $flash = flash_get();
    $nav = [
        'index.php'         => 'Dashboard',
        'home.php'          => 'Home Page',
        'about.php'         => 'About Page',
        'services.php'      => 'Services Page',
        'scholarships.php'  => 'Scholarships',
        'destinations.php'  => 'Destinations',
        'testimonials.php'  => 'Testimonials',
        'contact.php'       => 'Contact & Footer',
        'settings.php'      => 'Site Settings',
    ];
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> — CMS</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar" id="admin-sidebar">
        <div class="sidebar-brand">
            <strong>Naseeb CMS</strong>
            <span>Content dashboard</span>
            <button type="button" class="mobile-nav-toggle-admin" style="margin-top:.75rem;" onclick="document.getElementById('admin-sidebar').classList.toggle('open')">Menu</button>
        </div>
        <nav>
            <?php foreach ($nav as $href => $label): ?>
                <a href="<?= e($href) ?>" class="<?= $active === $href ? 'active' : '' ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
            <div class="logout">
                <a href="logout.php">Log out (<?= e($user) ?>)</a>
            </div>
        </nav>
    </aside>
    <main class="main">
        <div class="topbar">
            <h1><?= e($title) ?></h1>
            <div class="meta"><a href="../index.html" target="_blank" rel="noopener">View website ↗</a></div>
        </div>
        <?php if ($flash): ?>
            <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
    <?php
}

function admin_footer(): void
{
    ?>
    </main>
</div>
</body>
</html>
    <?php
}
