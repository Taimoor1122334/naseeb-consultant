<?php
require_once __DIR__ . '/_layout.php';
auth_require();

$defaults = require __DIR__ . '/../includes/default_content.php';
$keys = array_values(array_filter(array_keys($defaults), function ($k) {
    return strpos($k, 'about_') === 0;
}));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    try {
        foreach ($keys as $key) {
            if (isset($_POST[$key])) {
                set_content($key, trim((string) $_POST[$key]));
            }
        }
        flash_set('ok', 'About page saved.');
        redirect('about.php');
    } catch (Throwable $e) {
        flash_set('err', $e->getMessage());
        redirect('about.php');
    }
}

$data = [];
foreach ($keys as $key) {
    $data[$key] = get_content($key, $defaults[$key] ?? '');
}

admin_header('About Page', 'about.php');
?>
<form method="post">
    <?= csrf_field() ?>

    <div class="card">
        <h2>Banner</h2>
        <label>Banner title</label>
        <input type="text" name="about_banner_title" value="<?= e($data['about_banner_title']) ?>">
    </div>

    <div class="card">
        <h2>CEO message</h2>
        <div class="form-row">
            <div>
                <label>Card title</label>
                <input type="text" name="about_ceo_title" value="<?= e($data['about_ceo_title']) ?>">
            </div>
            <div>
                <label>Initials (circle)</label>
                <input type="text" name="about_ceo_initials" value="<?= e($data['about_ceo_initials']) ?>" maxlength="3">
            </div>
        </div>
        <label>Quote</label>
        <textarea name="about_ceo_quote" rows="5"><?= e($data['about_ceo_quote']) ?></textarea>
        <div class="form-row">
            <div>
                <label>CEO name</label>
                <input type="text" name="about_ceo_name" value="<?= e($data['about_ceo_name']) ?>">
            </div>
            <div>
                <label>Role / title</label>
                <input type="text" name="about_ceo_role" value="<?= e($data['about_ceo_role']) ?>">
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Company story</h2>
        <label>Subtitle</label>
        <input type="text" name="about_story_subtitle" value="<?= e($data['about_story_subtitle']) ?>">
        <label>Title</label>
        <input type="text" name="about_story_title" value="<?= e($data['about_story_title']) ?>">
        <label>Paragraph 1</label>
        <textarea name="about_story_p1" rows="4"><?= e($data['about_story_p1']) ?></textarea>
        <label>Paragraph 2</label>
        <textarea name="about_story_p2" rows="3"><?= e($data['about_story_p2']) ?></textarea>
        <div class="form-row">
            <div>
                <label>Check item 1</label>
                <input type="text" name="about_check1" value="<?= e($data['about_check1']) ?>">
            </div>
            <div>
                <label>Check item 2</label>
                <input type="text" name="about_check2" value="<?= e($data['about_check2']) ?>">
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Founding pillars</h2>
        <label>Subtitle</label>
        <input type="text" name="about_values_subtitle" value="<?= e($data['about_values_subtitle']) ?>">
        <label>Title</label>
        <input type="text" name="about_values_title" value="<?= e($data['about_values_title']) ?>">
        <label>Description</label>
        <textarea name="about_values_desc"><?= e($data['about_values_desc']) ?></textarea>
        <div class="form-row">
            <div>
                <label>Pillar 1 title</label>
                <input type="text" name="about_value1_title" value="<?= e($data['about_value1_title']) ?>">
                <label>Pillar 1 text</label>
                <textarea name="about_value1_text"><?= e($data['about_value1_text']) ?></textarea>
            </div>
            <div>
                <label>Pillar 2 title</label>
                <input type="text" name="about_value2_title" value="<?= e($data['about_value2_title']) ?>">
                <label>Pillar 2 text</label>
                <textarea name="about_value2_text"><?= e($data['about_value2_text']) ?></textarea>
            </div>
        </div>
        <label>Pillar 3 title</label>
        <input type="text" name="about_value3_title" value="<?= e($data['about_value3_title']) ?>">
        <label>Pillar 3 text</label>
        <textarea name="about_value3_text"><?= e($data['about_value3_text']) ?></textarea>
    </div>

    <div class="card">
        <h2>Credentials</h2>
        <label>Subtitle</label>
        <input type="text" name="about_cred_subtitle" value="<?= e($data['about_cred_subtitle']) ?>">
        <label>Title</label>
        <input type="text" name="about_cred_title" value="<?= e($data['about_cred_title']) ?>">
        <label>Description</label>
        <textarea name="about_cred_desc"><?= e($data['about_cred_desc']) ?></textarea>
        <div class="form-row">
            <div>
                <label>Credential 1 title</label>
                <input type="text" name="about_cred1_title" value="<?= e($data['about_cred1_title']) ?>">
                <label>Credential 1 text</label>
                <input type="text" name="about_cred1_text" value="<?= e($data['about_cred1_text']) ?>">
            </div>
            <div>
                <label>Credential 2 title</label>
                <input type="text" name="about_cred2_title" value="<?= e($data['about_cred2_title']) ?>">
                <label>Credential 2 text</label>
                <input type="text" name="about_cred2_text" value="<?= e($data['about_cred2_text']) ?>">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label>Credential 3 title</label>
                <input type="text" name="about_cred3_title" value="<?= e($data['about_cred3_title']) ?>">
                <label>Credential 3 text</label>
                <input type="text" name="about_cred3_text" value="<?= e($data['about_cred3_text']) ?>">
            </div>
            <div>
                <label>Credential 4 title</label>
                <input type="text" name="about_cred4_title" value="<?= e($data['about_cred4_title']) ?>">
                <label>Credential 4 text</label>
                <input type="text" name="about_cred4_text" value="<?= e($data['about_cred4_text']) ?>">
            </div>
        </div>
    </div>

    <div class="actions">
        <button type="submit" class="btn btn-gold">Save about page</button>
        <a class="btn btn-outline" href="../about.html" target="_blank" rel="noopener">Preview page</a>
    </div>
</form>
<?php admin_footer(); ?>
