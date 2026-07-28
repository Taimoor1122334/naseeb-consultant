<?php
require_once __DIR__ . '/_layout.php';
auth_require();

$keys = [
    'home_hero_badge', 'home_hero_title', 'home_hero_desc',
    'home_hero_btn1_text', 'home_hero_btn1_link', 'home_hero_btn2_text', 'home_hero_btn2_link',
    'home_stat_visa', 'home_stat_visa_label', 'home_stat_unis', 'home_stat_unis_label',
    'home_stat_students', 'home_stat_students_label',
    'home_form_title', 'home_form_desc',
    'home_why_subtitle', 'home_why_title', 'home_why_desc',
    'home_feat1_title', 'home_feat1_text', 'home_feat2_title', 'home_feat2_text', 'home_feat3_title', 'home_feat3_text',
    'home_dest_subtitle', 'home_dest_title', 'home_dest_desc',
    'home_testi_subtitle', 'home_testi_title', 'home_testi_desc',
    'home_cta_title', 'home_cta_desc', 'home_cta_btn1_text', 'home_cta_btn1_link', 'home_cta_btn2_text', 'home_cta_btn2_link',
    'home_ticker',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    try {
        foreach ($keys as $key) {
            if (isset($_POST[$key])) {
                set_content($key, trim((string) $_POST[$key]));
            }
        }
        flash_set('ok', 'Home page content saved.');
        redirect('home.php');
    } catch (Throwable $e) {
        flash_set('err', $e->getMessage());
        redirect('home.php');
    }
}

$data = [];
foreach ($keys as $key) {
    $data[$key] = get_content($key);
}

admin_header('Home Page', 'home.php');
?>
<form method="post">
    <?= csrf_field() ?>

    <div class="card">
        <h2>Hero section</h2>
        <label>Badge text</label>
        <input type="text" name="home_hero_badge" value="<?= e($data['home_hero_badge']) ?>">
        <label>Title (HTML allowed for &lt;span&gt;)</label>
        <input type="text" name="home_hero_title" value="<?= e($data['home_hero_title']) ?>">
        <label>Description</label>
        <textarea name="home_hero_desc"><?= e($data['home_hero_desc']) ?></textarea>
        <div class="form-row">
            <div>
                <label>Primary button text</label>
                <input type="text" name="home_hero_btn1_text" value="<?= e($data['home_hero_btn1_text']) ?>">
            </div>
            <div>
                <label>Primary button link</label>
                <input type="text" name="home_hero_btn1_link" value="<?= e($data['home_hero_btn1_link']) ?>">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label>Secondary button text</label>
                <input type="text" name="home_hero_btn2_text" value="<?= e($data['home_hero_btn2_text']) ?>">
            </div>
            <div>
                <label>Secondary button link</label>
                <input type="text" name="home_hero_btn2_link" value="<?= e($data['home_hero_btn2_link']) ?>">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label>Form title</label>
                <input type="text" name="home_form_title" value="<?= e($data['home_form_title']) ?>">
            </div>
            <div>
                <label>Form description</label>
                <input type="text" name="home_form_desc" value="<?= e($data['home_form_desc']) ?>">
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Hero stats</h2>
        <div class="form-row">
            <div>
                <label>Visa success number</label>
                <input type="number" name="home_stat_visa" value="<?= e($data['home_stat_visa']) ?>">
                <label>Label</label>
                <input type="text" name="home_stat_visa_label" value="<?= e($data['home_stat_visa_label']) ?>">
            </div>
            <div>
                <label>Partner unis number</label>
                <input type="number" name="home_stat_unis" value="<?= e($data['home_stat_unis']) ?>">
                <label>Label</label>
                <input type="text" name="home_stat_unis_label" value="<?= e($data['home_stat_unis_label']) ?>">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label>Students placed number</label>
                <input type="number" name="home_stat_students" value="<?= e($data['home_stat_students']) ?>">
            </div>
            <div>
                <label>Label</label>
                <input type="text" name="home_stat_students_label" value="<?= e($data['home_stat_students_label']) ?>">
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Why choose us</h2>
        <label>Subtitle</label>
        <input type="text" name="home_why_subtitle" value="<?= e($data['home_why_subtitle']) ?>">
        <label>Title</label>
        <input type="text" name="home_why_title" value="<?= e($data['home_why_title']) ?>">
        <label>Description</label>
        <textarea name="home_why_desc"><?= e($data['home_why_desc']) ?></textarea>
        <div class="form-row">
            <div>
                <label>Feature 1 title</label>
                <input type="text" name="home_feat1_title" value="<?= e($data['home_feat1_title']) ?>">
                <label>Feature 1 text</label>
                <textarea name="home_feat1_text"><?= e($data['home_feat1_text']) ?></textarea>
            </div>
            <div>
                <label>Feature 2 title</label>
                <input type="text" name="home_feat2_title" value="<?= e($data['home_feat2_title']) ?>">
                <label>Feature 2 text</label>
                <textarea name="home_feat2_text"><?= e($data['home_feat2_text']) ?></textarea>
            </div>
        </div>
        <label>Feature 3 title</label>
        <input type="text" name="home_feat3_title" value="<?= e($data['home_feat3_title']) ?>">
        <label>Feature 3 text</label>
        <textarea name="home_feat3_text"><?= e($data['home_feat3_text']) ?></textarea>
    </div>

    <div class="card">
        <h2>Destinations section headings</h2>
        <label>Subtitle</label>
        <input type="text" name="home_dest_subtitle" value="<?= e($data['home_dest_subtitle']) ?>">
        <label>Title</label>
        <input type="text" name="home_dest_title" value="<?= e($data['home_dest_title']) ?>">
        <label>Description</label>
        <textarea name="home_dest_desc"><?= e($data['home_dest_desc']) ?></textarea>
        <p class="hint">Country cards themselves are edited under Destinations.</p>
    </div>

    <div class="card">
        <h2>Testimonials headings</h2>
        <label>Subtitle</label>
        <input type="text" name="home_testi_subtitle" value="<?= e($data['home_testi_subtitle']) ?>">
        <label>Title</label>
        <input type="text" name="home_testi_title" value="<?= e($data['home_testi_title']) ?>">
        <label>Description</label>
        <textarea name="home_testi_desc"><?= e($data['home_testi_desc']) ?></textarea>
    </div>

    <div class="card">
        <h2>Bottom CTA banner</h2>
        <label>Title</label>
        <input type="text" name="home_cta_title" value="<?= e($data['home_cta_title']) ?>">
        <label>Description</label>
        <textarea name="home_cta_desc"><?= e($data['home_cta_desc']) ?></textarea>
        <div class="form-row">
            <div>
                <label>Button 1 text</label>
                <input type="text" name="home_cta_btn1_text" value="<?= e($data['home_cta_btn1_text']) ?>">
            </div>
            <div>
                <label>Button 1 link</label>
                <input type="text" name="home_cta_btn1_link" value="<?= e($data['home_cta_btn1_link']) ?>">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label>Button 2 text</label>
                <input type="text" name="home_cta_btn2_text" value="<?= e($data['home_cta_btn2_text']) ?>">
            </div>
            <div>
                <label>Button 2 link</label>
                <input type="text" name="home_cta_btn2_link" value="<?= e($data['home_cta_btn2_link']) ?>">
            </div>
        </div>
    </div>

    <div class="card">
        <h2>University ticker</h2>
        <label>One university name per line</label>
        <textarea name="home_ticker" rows="10"><?= e($data['home_ticker']) ?></textarea>
    </div>

    <div class="actions">
        <button type="submit" class="btn btn-gold">Save home content</button>
    </div>
</form>
<?php admin_footer(); ?>
