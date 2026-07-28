<?php
require_once __DIR__ . '/_layout.php';
auth_require();

$uploadDir = __DIR__ . '/../uploads';
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$action = $_GET['action'] ?? '';

if ($action === 'delete' && $editId > 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    db()->prepare('DELETE FROM testimonials WHERE id = ?')->execute([$editId]);
    flash_set('ok', 'Testimonial deleted.');
    redirect('testimonials.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== 'delete') {
    csrf_verify();
    try {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['student_name'] ?? ''));
        $meta = trim((string) ($_POST['meta_line'] ?? ''));
        $quote = trim((string) ($_POST['quote_text'] ?? ''));
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $imageUrl = trim((string) ($_POST['image_url'] ?? ''));

        if ($name === '' || $quote === '') {
            throw new RuntimeException('Name and quote are required.');
        }

        $existingImage = null;
        if ($id > 0) {
            $stmt = db()->prepare('SELECT image_path FROM testimonials WHERE id = ?');
            $stmt->execute([$id]);
            $existingImage = $stmt->fetchColumn() ?: null;
        }

        $imagePath = handle_image_upload('image', $uploadDir, $existingImage);
        // Prefer uploaded file; else keep/typed URL
        if ($imagePath === $existingImage && $imageUrl !== '') {
            $imagePath = $imageUrl;
        } elseif (!$imagePath && $imageUrl !== '') {
            $imagePath = $imageUrl;
        }

        if ($id > 0) {
            $stmt = db()->prepare(
                'UPDATE testimonials SET student_name=?, meta_line=?, quote_text=?, image_path=?, sort_order=?, is_active=? WHERE id=?'
            );
            $stmt->execute([$name, $meta, $quote, $imagePath, $sortOrder, $isActive, $id]);
            flash_set('ok', 'Testimonial updated.');
        } else {
            $stmt = db()->prepare(
                'INSERT INTO testimonials (student_name, meta_line, quote_text, image_path, sort_order, is_active)
                 VALUES (?,?,?,?,?,?)'
            );
            $stmt->execute([$name, $meta, $quote, $imagePath, $sortOrder, $isActive]);
            flash_set('ok', 'Testimonial added.');
        }
        redirect('testimonials.php');
    } catch (Throwable $e) {
        flash_set('err', $e->getMessage());
        redirect('testimonials.php' . ($editId ? '?edit=' . $editId : '?new=1'));
    }
}

$rows = db()->query('SELECT * FROM testimonials ORDER BY sort_order ASC, id ASC')->fetchAll();
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

admin_header('Testimonials', 'testimonials.php');
?>

<?php if ($editing || $isNew): ?>
<div class="card">
    <h2><?= $editing ? 'Edit testimonial' : 'Add testimonial' ?></h2>
    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= e((string) ($editing['id'] ?? 0)) ?>">
        <div class="form-row">
            <div>
                <label>Student name</label>
                <input type="text" name="student_name" required value="<?= e($editing['student_name'] ?? '') ?>">
            </div>
            <div>
                <label>University / country line</label>
                <input type="text" name="meta_line" value="<?= e($editing['meta_line'] ?? '') ?>">
            </div>
        </div>
        <label>Quote</label>
        <textarea name="quote_text" required><?= e($editing['quote_text'] ?? '') ?></textarea>
        <div class="form-row">
            <div>
                <label>Photo upload</label>
                <input type="file" name="image" accept="image/*">
                <?php if (!empty($editing['image_path']) && !preg_match('#^https?://#i', $editing['image_path'])): ?>
                    <img class="preview" src="../<?= e($editing['image_path']) ?>" alt="">
                <?php endif; ?>
            </div>
            <div>
                <label>Or photo URL</label>
                <input type="url" name="image_url" value="<?= e(preg_match('#^https?://#i', $editing['image_path'] ?? '') ? ($editing['image_path'] ?? '') : '') ?>" placeholder="https://...">
                <label>Sort order</label>
                <input type="number" name="sort_order" value="<?= e((string) ($editing['sort_order'] ?? 0)) ?>">
            </div>
        </div>
        <label class="checkbox-row"><input type="checkbox" name="is_active" <?= !isset($editing) || !empty($editing['is_active']) ? 'checked' : '' ?>> Active</label>
        <div class="actions">
            <button type="submit" class="btn btn-gold">Save testimonial</button>
            <a class="btn btn-outline" href="testimonials.php">Cancel</a>
        </div>
    </form>
</div>
<?php else: ?>
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
        <h2 style="margin:0;">Student testimonials</h2>
        <a class="btn btn-gold" href="testimonials.php?new=1">+ Add testimonial</a>
    </div>
    <div style="overflow-x:auto; margin-top:1rem;">
        <table>
            <thead>
                <tr><th>Photo</th><th>Name</th><th>Meta</th><th>Order</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td>
                        <?php if ($row['image_path']): ?>
                            <img class="thumb" src="<?= e(preg_match('#^https?://#i', $row['image_path']) ? $row['image_path'] : '../' . $row['image_path']) ?>" alt="">
                        <?php endif; ?>
                    </td>
                    <td><strong><?= e($row['student_name']) ?></strong></td>
                    <td><?= e($row['meta_line']) ?></td>
                    <td><?= (int) $row['sort_order'] ?></td>
                    <td>
                        <a class="btn btn-sm btn-outline" href="testimonials.php?edit=<?= (int) $row['id'] ?>">Edit</a>
                        <form method="post" action="testimonials.php?action=delete&edit=<?= (int) $row['id'] ?>" style="display:inline;" onsubmit="return confirm('Delete this testimonial?');">
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
