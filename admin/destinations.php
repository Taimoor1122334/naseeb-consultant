<?php
require_once __DIR__ . '/_layout.php';
auth_require();

$uploadDir = __DIR__ . '/../uploads';
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$action = $_GET['action'] ?? '';

if ($action === 'delete' && $editId > 0) {
    auth_require();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        db()->prepare('DELETE FROM destinations WHERE id = ?')->execute([$editId]);
        flash_set('ok', 'Destination deleted.');
        redirect('destinations.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action !== 'delete')) {
    csrf_verify();
    try {
        if (!empty($_POST['save_page_text'])) {
            foreach (['destinations_banner_title', 'destinations_search_hint', 'destinations_search_placeholder'] as $key) {
                if (isset($_POST[$key])) {
                    set_content($key, trim((string) $_POST[$key]));
                }
            }
            flash_set('ok', 'Destinations page text saved.');
            redirect('destinations.php');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $name = trim((string) ($_POST['name'] ?? ''));
        $badge = trim((string) ($_POST['badge'] ?? ''));
        $tagline = trim((string) ($_POST['tagline'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $tuition = trim((string) ($_POST['tuition'] ?? ''));
        $intakes = trim((string) ($_POST['intakes'] ?? ''));
        $ielts = trim((string) ($_POST['ielts'] ?? ''));
        $showOnHome = isset($_POST['show_on_home']) ? 1 : 0;
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($name === '' || $slug === '') {
            throw new RuntimeException('Name and slug are required.');
        }
        $slug = strtolower(preg_replace('/[^a-z0-9\-]+/i', '-', $slug));

        $existingImage = null;
        if ($id > 0) {
            $stmt = db()->prepare('SELECT image_path FROM destinations WHERE id = ?');
            $stmt->execute([$id]);
            $existingImage = $stmt->fetchColumn() ?: null;
        }

        $imagePath = handle_image_upload('image', $uploadDir, $existingImage);

        if ($id > 0) {
            $stmt = db()->prepare(
                'UPDATE destinations SET slug=?, name=?, badge=?, tagline=?, description=?, tuition=?, intakes=?, ielts=?, image_path=?, show_on_home=?, sort_order=?, is_active=? WHERE id=?'
            );
            $stmt->execute([$slug, $name, $badge, $tagline, $description, $tuition, $intakes, $ielts, $imagePath, $showOnHome, $sortOrder, $isActive, $id]);
            flash_set('ok', 'Destination updated.');
        } else {
            $stmt = db()->prepare(
                'INSERT INTO destinations (slug, name, badge, tagline, description, tuition, intakes, ielts, image_path, show_on_home, sort_order, is_active)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?)'
            );
            $stmt->execute([$slug, $name, $badge, $tagline, $description, $tuition, $intakes, $ielts, $imagePath, $showOnHome, $sortOrder, $isActive]);
            flash_set('ok', 'Destination added.');
        }
        redirect('destinations.php');
    } catch (Throwable $e) {
        flash_set('err', $e->getMessage());
        redirect('destinations.php' . ($editId ? '?edit=' . $editId : '?new=1'));
    }
}

$rows = db()->query('SELECT * FROM destinations ORDER BY sort_order ASC, id ASC')->fetchAll();
$editing = null;
if ($editId > 0) {
    foreach ($rows as $row) {
        if ((int) $row['id'] === $editId) {
            $editing = $row;
            break;
        }
    }
}
$isNew = isset($_GET['new']);

admin_header('Destinations', 'destinations.php');
?>

<?php if ($editing || $isNew): ?>
<div class="card">
    <h2><?= $editing ? 'Edit destination' : 'Add destination' ?></h2>
    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= e((string) ($editing['id'] ?? 0)) ?>">
        <div class="form-row">
            <div>
                <label>Name</label>
                <input type="text" name="name" required value="<?= e($editing['name'] ?? '') ?>">
            </div>
            <div>
                <label>Slug (url key, e.g. uk)</label>
                <input type="text" name="slug" required value="<?= e($editing['slug'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label>Badge (on image)</label>
                <input type="text" name="badge" value="<?= e($editing['badge'] ?? '') ?>">
            </div>
            <div>
                <label>Tagline / span text</label>
                <input type="text" name="tagline" value="<?= e($editing['tagline'] ?? '') ?>">
            </div>
        </div>
        <label>Description</label>
        <textarea name="description"><?= e($editing['description'] ?? '') ?></textarea>
        <div class="form-row">
            <div>
                <label>Avg tuition</label>
                <input type="text" name="tuition" value="<?= e($editing['tuition'] ?? '') ?>">
            </div>
            <div>
                <label>Intakes</label>
                <input type="text" name="intakes" value="<?= e($editing['intakes'] ?? '') ?>">
            </div>
        </div>
        <label>IELTS / requirements</label>
        <input type="text" name="ielts" value="<?= e($editing['ielts'] ?? '') ?>">
        <div class="form-row">
            <div>
                <label>Sort order</label>
                <input type="number" name="sort_order" value="<?= e((string) ($editing['sort_order'] ?? 0)) ?>">
            </div>
            <div>
                <label>Image</label>
                <input type="file" name="image" accept="image/*">
                <?php if (!empty($editing['image_path'])): ?>
                    <img class="preview" src="../<?= e($editing['image_path']) ?>" alt="">
                    <p class="hint">Current: <?= e($editing['image_path']) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <label class="checkbox-row"><input type="checkbox" name="show_on_home" <?= !empty($editing['show_on_home']) ? 'checked' : '' ?>> Show on home page</label>
        <label class="checkbox-row"><input type="checkbox" name="is_active" <?= !isset($editing) || !empty($editing['is_active']) ? 'checked' : '' ?>> Active</label>
        <div class="actions">
            <button type="submit" class="btn btn-gold">Save destination</button>
            <a class="btn btn-outline" href="destinations.php">Cancel</a>
        </div>
    </form>
</div>
<?php else: ?>

<?php
$defaults = require __DIR__ . '/../includes/default_content.php';
$pageText = [
    'destinations_banner_title' => get_content('destinations_banner_title', $defaults['destinations_banner_title'] ?? ''),
    'destinations_search_hint' => get_content('destinations_search_hint', $defaults['destinations_search_hint'] ?? ''),
    'destinations_search_placeholder' => get_content('destinations_search_placeholder', $defaults['destinations_search_placeholder'] ?? ''),
];
?>
<div class="card">
    <h2>Destinations page text</h2>
    <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="save_page_text" value="1">
        <label>Banner title</label>
        <input type="text" name="destinations_banner_title" value="<?= e($pageText['destinations_banner_title']) ?>">
        <label>Search placeholder</label>
        <input type="text" name="destinations_search_placeholder" value="<?= e($pageText['destinations_search_placeholder']) ?>">
        <label>Search hint text</label>
        <input type="text" name="destinations_search_hint" value="<?= e($pageText['destinations_search_hint']) ?>">
        <div class="actions">
            <button type="submit" class="btn btn-gold">Save page text</button>
        </div>
    </form>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
        <h2 style="margin:0;">All destinations</h2>
        <a class="btn btn-gold" href="destinations.php?new=1">+ Add destination</a>
    </div>
    <div style="overflow-x:auto; margin-top:1rem;">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Home</th>
                    <th>Order</th>
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td>
                        <?php if ($row['image_path']): ?>
                            <img class="thumb" src="../<?= e($row['image_path']) ?>" alt="">
                        <?php endif; ?>
                    </td>
                    <td><strong><?= e($row['name']) ?></strong><br><span class="hint"><?= e($row['slug']) ?></span></td>
                    <td><?= $row['show_on_home'] ? 'Yes' : 'No' ?></td>
                    <td><?= (int) $row['sort_order'] ?></td>
                    <td><?= $row['is_active'] ? 'Yes' : 'No' ?></td>
                    <td>
                        <a class="btn btn-sm btn-outline" href="destinations.php?edit=<?= (int) $row['id'] ?>">Edit</a>
                        <form method="post" action="destinations.php?action=delete&edit=<?= (int) $row['id'] ?>" style="display:inline;" onsubmit="return confirm('Delete this destination?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<?php admin_footer(); ?>
