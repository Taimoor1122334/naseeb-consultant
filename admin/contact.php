<?php
require_once __DIR__ . '/_layout.php';
auth_require();

$settingKeys = [
    'phone', 'email', 'address', 'hours', 'hours_note',
    'footer_about', 'facebook_url', 'twitter_url', 'instagram_url', 'linkedin_url',
];
$contentKeys = [
    'contact_banner_title', 'contact_panel_title', 'contact_panel_desc', 'contact_form_title',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    try {
        foreach ($settingKeys as $key) {
            if (isset($_POST[$key])) {
                set_setting($key, trim((string) $_POST[$key]));
            }
        }
        foreach ($contentKeys as $key) {
            if (isset($_POST[$key])) {
                set_content($key, trim((string) $_POST[$key]));
            }
        }
        flash_set('ok', 'Contact & footer details saved.');
        redirect('contact.php');
    } catch (Throwable $e) {
        flash_set('err', $e->getMessage());
        redirect('contact.php');
    }
}

$s = [];
foreach ($settingKeys as $key) {
    $s[$key] = get_setting($key);
}
$c = [];
foreach ($contentKeys as $key) {
    $c[$key] = get_content($key);
}

admin_header('Contact & Footer', 'contact.php');
?>
<form method="post">
    <?= csrf_field() ?>

    <div class="card">
        <h2>Contact details</h2>
        <label>Phone / WhatsApp display</label>
        <input type="text" name="phone" value="<?= e($s['phone']) ?>">
        <label>Email</label>
        <input type="email" name="email" value="<?= e($s['email']) ?>">
        <label>Office address</label>
        <textarea name="address"><?= e($s['address']) ?></textarea>
        <div class="form-row">
            <div>
                <label>Opening hours</label>
                <input type="text" name="hours" value="<?= e($s['hours']) ?>">
            </div>
            <div>
                <label>Hours note</label>
                <input type="text" name="hours_note" value="<?= e($s['hours_note']) ?>">
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Contact page text</h2>
        <label>Banner title</label>
        <input type="text" name="contact_banner_title" value="<?= e($c['contact_banner_title']) ?>">
        <label>Panel title</label>
        <input type="text" name="contact_panel_title" value="<?= e($c['contact_panel_title']) ?>">
        <label>Panel description</label>
        <textarea name="contact_panel_desc"><?= e($c['contact_panel_desc']) ?></textarea>
        <label>Form title</label>
        <input type="text" name="contact_form_title" value="<?= e($c['contact_form_title']) ?>">
    </div>

    <div class="card">
        <h2>Footer</h2>
        <label>About text under logo</label>
        <textarea name="footer_about"><?= e($s['footer_about']) ?></textarea>
        <div class="form-row">
            <div>
                <label>Facebook URL</label>
                <input type="url" name="facebook_url" value="<?= e($s['facebook_url']) ?>">
            </div>
            <div>
                <label>Twitter / X URL</label>
                <input type="url" name="twitter_url" value="<?= e($s['twitter_url']) ?>">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label>Instagram URL</label>
                <input type="url" name="instagram_url" value="<?= e($s['instagram_url']) ?>">
            </div>
            <div>
                <label>LinkedIn URL</label>
                <input type="url" name="linkedin_url" value="<?= e($s['linkedin_url']) ?>">
            </div>
        </div>
    </div>

    <div class="actions">
        <button type="submit" class="btn btn-gold">Save contact &amp; footer</button>
    </div>
</form>
<?php admin_footer(); ?>
