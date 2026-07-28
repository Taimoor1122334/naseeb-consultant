<?php
/**
 * Adds missing CMS content fields for About / Services / Scholarships
 * without wiping existing content. Safe to run on an already-installed site.
 *
 * Visit once: https://yoursite.com/upgrade.php
 * Then DELETE this file.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$messages = [];
$errors = [];
$added = 0;

try {
    $defaults = require __DIR__ . '/includes/default_content.php';
    $stmt = db()->prepare(
        'INSERT IGNORE INTO page_content (content_key, content_value) VALUES (?, ?)'
    );
    foreach ($defaults as $key => $value) {
        $stmt->execute([$key, $value]);
        if ($stmt->rowCount() > 0) {
            $added++;
        }
    }

    // Destinations page search fields (also in defaults)
    $messages[] = "Upgrade complete. Added {$added} new content field(s). Existing values were left unchanged.";
    $messages[] = 'Open /admin and edit About, Services, and Scholarships.';
    $messages[] = 'IMPORTANT: Delete upgrade.php from the server now.';
} catch (Throwable $e) {
    $errors[] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upgrade CMS Content</title>
    <style>
        body { font-family: system-ui, sans-serif; background:#0f2744; color:#0f172a; margin:0; min-height:100vh; display:grid; place-items:center; }
        .card { background:#fff; max-width:520px; width:92%; padding:2rem; border-radius:12px; }
        .ok { background:#ecfdf5; color:#059669; padding:.8rem 1rem; border-radius:8px; margin:.5rem 0; }
        .err { background:#fef2f2; color:#dc2626; padding:.8rem 1rem; border-radius:8px; margin:.5rem 0; }
        a { color:#d97706; font-weight:700; }
    </style>
</head>
<body>
<div class="card">
    <h1>CMS Content Upgrade</h1>
    <?php foreach ($errors as $err): ?><div class="err"><?= htmlspecialchars($err) ?></div><?php endforeach; ?>
    <?php foreach ($messages as $msg): ?><div class="ok"><?= htmlspecialchars($msg) ?></div><?php endforeach; ?>
    <?php if (!$errors): ?>
        <p><a href="admin/login.php">Go to Admin →</a></p>
    <?php endif; ?>
</div>
</body>
</html>
