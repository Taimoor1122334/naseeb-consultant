<?php
require_once __DIR__ . '/_layout.php';
auth_require();

$defaults = require __DIR__ . '/../includes/default_content.php';
$keys = array_values(array_filter(array_keys($defaults), function ($k) {
    return strpos($k, 'services_') === 0;
}));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    try {
        foreach ($keys as $key) {
            if (isset($_POST[$key])) {
                set_content($key, trim((string) $_POST[$key]));
            }
        }
        flash_set('ok', 'Services page saved.');
        redirect('services.php');
    } catch (Throwable $e) {
        flash_set('err', $e->getMessage());
        redirect('services.php');
    }
}

$data = [];
foreach ($keys as $key) {
    $data[$key] = get_content($key, $defaults[$key] ?? '');
}

admin_header('Services Page', 'services.php');
?>
<form method="post">
    <?= csrf_field() ?>

    <div class="card">
        <h2>Banner & section intro</h2>
        <label>Banner title</label>
        <input type="text" name="services_banner_title" value="<?= e($data['services_banner_title']) ?>">
        <label>Subtitle</label>
        <input type="text" name="services_subtitle" value="<?= e($data['services_subtitle']) ?>">
        <label>Title</label>
        <input type="text" name="services_title" value="<?= e($data['services_title']) ?>">
        <label>Description</label>
        <textarea name="services_desc"><?= e($data['services_desc']) ?></textarea>
    </div>

    <?php for ($i = 1; $i <= 3; $i++): ?>
    <div class="card">
        <h2>Service card <?= $i ?></h2>
        <label>Title</label>
        <input type="text" name="services_card<?= $i ?>_title" value="<?= e($data["services_card{$i}_title"]) ?>">
        <label>Description</label>
        <textarea name="services_card<?= $i ?>_text"><?= e($data["services_card{$i}_text"]) ?></textarea>
        <div class="form-row">
            <div>
                <label>Bullet 1</label>
                <input type="text" name="services_card<?= $i ?>_b1" value="<?= e($data["services_card{$i}_b1"]) ?>">
            </div>
            <div>
                <label>Bullet 2</label>
                <input type="text" name="services_card<?= $i ?>_b2" value="<?= e($data["services_card{$i}_b2"]) ?>">
            </div>
        </div>
        <label>Bullet 3</label>
        <input type="text" name="services_card<?= $i ?>_b3" value="<?= e($data["services_card{$i}_b3"]) ?>">
    </div>
    <?php endfor; ?>

    <div class="card">
        <h2>Eligibility tool section</h2>
        <label>Subtitle</label>
        <input type="text" name="services_tool_subtitle" value="<?= e($data['services_tool_subtitle']) ?>">
        <label>Title</label>
        <input type="text" name="services_tool_title" value="<?= e($data['services_tool_title']) ?>">
        <label>Description</label>
        <textarea name="services_tool_desc"><?= e($data['services_tool_desc']) ?></textarea>
        <label>Sidebar title</label>
        <input type="text" name="services_tool_sidebar_title" value="<?= e($data['services_tool_sidebar_title']) ?>">
        <label>Sidebar text</label>
        <textarea name="services_tool_sidebar_text"><?= e($data['services_tool_sidebar_text']) ?></textarea>
        <div class="form-row">
            <div>
                <label>Feature 1 title</label>
                <input type="text" name="services_tool_feat1_title" value="<?= e($data['services_tool_feat1_title']) ?>">
                <label>Feature 1 text</label>
                <textarea name="services_tool_feat1_text"><?= e($data['services_tool_feat1_text']) ?></textarea>
            </div>
            <div>
                <label>Feature 2 title</label>
                <input type="text" name="services_tool_feat2_title" value="<?= e($data['services_tool_feat2_title']) ?>">
                <label>Feature 2 text</label>
                <textarea name="services_tool_feat2_text"><?= e($data['services_tool_feat2_text']) ?></textarea>
            </div>
        </div>
    </div>

    <div class="actions">
        <button type="submit" class="btn btn-gold">Save services page</button>
        <a class="btn btn-outline" href="../services.html" target="_blank" rel="noopener">Preview page</a>
    </div>
</form>
<?php admin_footer(); ?>
