<?php
require_once __DIR__ . '/_layout.php';
auth_require();

$defaults = require __DIR__ . '/../includes/default_content.php';
$keys = array_values(array_filter(array_keys($defaults), function ($k) {
    return strpos($k, 'scholarships_') === 0;
}));
$uploadDir = __DIR__ . '/../uploads';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    try {
        foreach ($keys as $key) {
            if ($key === 'scholarships_image') {
                continue;
            }
            if (isset($_POST[$key])) {
                set_content($key, trim((string) $_POST[$key]));
            }
        }

        $existingImage = get_content('scholarships_image', $defaults['scholarships_image'] ?? '');
        $imagePath = handle_image_upload('scholarships_image_file', $uploadDir, $existingImage);
        set_content('scholarships_image', $imagePath);

        flash_set('ok', 'Scholarships page saved.');
        redirect('scholarships.php');
    } catch (Throwable $e) {
        flash_set('err', $e->getMessage());
        redirect('scholarships.php');
    }
}

$data = [];
foreach ($keys as $key) {
    $data[$key] = get_content($key, $defaults[$key] ?? '');
}

admin_header('Scholarships Page', 'scholarships.php');
?>
<form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="card">
        <h2>Banner & intro</h2>
        <label>Banner title</label>
        <input type="text" name="scholarships_banner_title" value="<?= e($data['scholarships_banner_title']) ?>">
        <label>Subtitle</label>
        <input type="text" name="scholarships_intro_subtitle" value="<?= e($data['scholarships_intro_subtitle']) ?>">
        <label>Title</label>
        <input type="text" name="scholarships_intro_title" value="<?= e($data['scholarships_intro_title']) ?>">
        <label>Paragraph 1</label>
        <textarea name="scholarships_intro_p1"><?= e($data['scholarships_intro_p1']) ?></textarea>
        <label>Paragraph 2</label>
        <textarea name="scholarships_intro_p2"><?= e($data['scholarships_intro_p2']) ?></textarea>
        <label>Tip title</label>
        <input type="text" name="scholarships_tip_title" value="<?= e($data['scholarships_tip_title']) ?>">
        <label>Tip text</label>
        <textarea name="scholarships_tip_text"><?= e($data['scholarships_tip_text']) ?></textarea>
        <label>Intro image</label>
        <input type="file" name="scholarships_image_file" accept="image/*">
        <?php if (!empty($data['scholarships_image'])): ?>
            <img class="preview" src="../<?= e($data['scholarships_image']) ?>" alt="">
            <p class="hint">Current: <?= e($data['scholarships_image']) ?></p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Programs section</h2>
        <label>Subtitle</label>
        <input type="text" name="scholarships_list_subtitle" value="<?= e($data['scholarships_list_subtitle']) ?>">
        <label>Title</label>
        <input type="text" name="scholarships_list_title" value="<?= e($data['scholarships_list_title']) ?>">
        <label>Description</label>
        <textarea name="scholarships_list_desc"><?= e($data['scholarships_list_desc']) ?></textarea>
    </div>

    <?php for ($i = 1; $i <= 2; $i++): ?>
    <div class="card">
        <h2>Scholarship program <?= $i ?></h2>
        <label>Title</label>
        <input type="text" name="scholarships_prog<?= $i ?>_title" value="<?= e($data["scholarships_prog{$i}_title"]) ?>">
        <label>Description</label>
        <textarea name="scholarships_prog<?= $i ?>_text"><?= e($data["scholarships_prog{$i}_text"]) ?></textarea>
        <div class="form-row">
            <div>
                <label>Bullet 1</label>
                <input type="text" name="scholarships_prog<?= $i ?>_b1" value="<?= e($data["scholarships_prog{$i}_b1"]) ?>">
            </div>
            <div>
                <label>Bullet 2</label>
                <input type="text" name="scholarships_prog<?= $i ?>_b2" value="<?= e($data["scholarships_prog{$i}_b2"]) ?>">
            </div>
        </div>
        <label>Bullet 3</label>
        <input type="text" name="scholarships_prog<?= $i ?>_b3" value="<?= e($data["scholarships_prog{$i}_b3"]) ?>">
    </div>
    <?php endfor; ?>

    <div class="card">
        <h2>How to apply steps</h2>
        <label>Section title</label>
        <input type="text" name="scholarships_steps_title" value="<?= e($data['scholarships_steps_title']) ?>">
        <?php for ($i = 1; $i <= 3; $i++): ?>
            <label>Step <?= $i ?> title</label>
            <input type="text" name="scholarships_step<?= $i ?>_title" value="<?= e($data["scholarships_step{$i}_title"]) ?>">
            <label>Step <?= $i ?> text</label>
            <textarea name="scholarships_step<?= $i ?>_text"><?= e($data["scholarships_step{$i}_text"]) ?></textarea>
        <?php endfor; ?>
    </div>

    <div class="actions">
        <button type="submit" class="btn btn-gold">Save scholarships page</button>
        <a class="btn btn-outline" href="../scholarships.html" target="_blank" rel="noopener">Preview page</a>
    </div>
</form>
<?php admin_footer(); ?>
