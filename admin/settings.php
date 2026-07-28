<?php
require_once __DIR__ . '/_layout.php';
auth_require();

$uploadDir = __DIR__ . '/../uploads';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    try {
        $keys = [
            'site_name', 'header_cta_text', 'header_cta_link',
            'whatsapp_number', 'whatsapp_message', 'whatsapp_greeting',
        ];
        foreach ($keys as $key) {
            if (isset($_POST[$key])) {
                set_setting($key, trim((string) $_POST[$key]));
            }
        }

        $existingLogo = get_setting('logo_path', 'logo.png');
        $logoPath = handle_image_upload('logo', $uploadDir, $existingLogo);
        set_setting('logo_path', $logoPath);

        if (!empty($_POST['new_password'])) {
            $pass = (string) $_POST['new_password'];
            if (strlen($pass) < 6) {
                throw new RuntimeException('New password must be at least 6 characters.');
            }
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = db()->prepare('UPDATE admins SET password_hash = ? WHERE id = ?');
            $stmt->execute([$hash, $_SESSION['admin_id']]);
        }

        flash_set('ok', 'Site settings saved.');
        redirect('settings.php');
    } catch (Throwable $e) {
        flash_set('err', $e->getMessage());
        redirect('settings.php');
    }
}

admin_header('Site Settings', 'settings.php');
$logo = get_setting('logo_path', 'logo.png');
?>
<form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="card">
        <h2>Brand</h2>
        <label>Site name</label>
        <input type="text" name="site_name" value="<?= e(get_setting('site_name')) ?>">
        <label>Logo image</label>
        <input type="file" name="logo" accept="image/*">
        <?php if ($logo): ?>
            <img class="preview" src="../<?= e($logo) ?>" alt="Logo preview">
            <p class="hint">Current: <?= e($logo) ?></p>
        <?php endif; ?>
        <div class="form-row">
            <div>
                <label>Header CTA button text</label>
                <input type="text" name="header_cta_text" value="<?= e(get_setting('header_cta_text')) ?>">
            </div>
            <div>
                <label>Header CTA link</label>
                <input type="text" name="header_cta_link" value="<?= e(get_setting('header_cta_link')) ?>">
            </div>
        </div>
    </div>

    <div class="card">
        <h2>WhatsApp widget</h2>
        <label>WhatsApp number (with country code, no +)</label>
        <input type="text" name="whatsapp_number" value="<?= e(get_setting('whatsapp_number')) ?>" placeholder="923356896333">
        <label>Default chat message</label>
        <input type="text" name="whatsapp_message" value="<?= e(get_setting('whatsapp_message')) ?>">
        <label>Greeting bubble text</label>
        <textarea name="whatsapp_greeting"><?= e(get_setting('whatsapp_greeting')) ?></textarea>
    </div>

    <div class="card">
        <h2>Change admin password</h2>
        <label>New password (leave blank to keep current)</label>
        <input type="password" name="new_password" minlength="6" autocomplete="new-password">
    </div>

    <div class="actions">
        <button type="submit" class="btn btn-gold">Save settings</button>
    </div>
</form>
<?php admin_footer(); ?>
