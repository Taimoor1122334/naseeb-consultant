<?php
require_once __DIR__ . '/_layout.php';

try {
    $destCount = (int) db()->query('SELECT COUNT(*) FROM destinations WHERE is_active = 1')->fetchColumn();
    $testiCount = (int) db()->query('SELECT COUNT(*) FROM testimonials WHERE is_active = 1')->fetchColumn();
    $contentCount = (int) db()->query('SELECT COUNT(*) FROM page_content')->fetchColumn();
} catch (Throwable $e) {
    flash_set('err', 'Database error: run install.php first. ' . $e->getMessage());
    $destCount = $testiCount = $contentCount = 0;
}

admin_header('Dashboard', 'index.php');
?>
<div class="card-grid" style="margin-bottom:1.25rem;">
    <div class="stat-box"><div class="n"><?= $contentCount ?></div><div class="l">Editable text fields</div></div>
    <div class="stat-box"><div class="n"><?= $destCount ?></div><div class="l">Active destinations</div></div>
    <div class="stat-box"><div class="n"><?= $testiCount ?></div><div class="l">Testimonials</div></div>
</div>

<div class="card">
    <h2>Edit website content</h2>
    <p style="color:var(--muted); margin:0 0 1rem;">Choose a section to update. Changes appear on the live site after save (refresh the website page).</p>
    <div class="actions">
        <a class="btn btn-navy" href="home.php">Home</a>
        <a class="btn btn-navy" href="about.php">About</a>
        <a class="btn btn-navy" href="services.php">Services</a>
        <a class="btn btn-navy" href="scholarships.php">Scholarships</a>
        <a class="btn btn-navy" href="destinations.php">Destinations</a>
        <a class="btn btn-navy" href="testimonials.php">Testimonials</a>
        <a class="btn btn-navy" href="contact.php">Contact &amp; Footer</a>
        <a class="btn btn-gold" href="settings.php">Logo &amp; WhatsApp</a>
    </div>
</div>

<div class="card">
    <h2>How it works</h2>
    <ol style="margin:0; padding-left:1.2rem; color:var(--muted); line-height:1.7;">
        <li>Edit text or upload a new image in this dashboard.</li>
        <li>Click <strong>Save</strong>.</li>
        <li>Open the public website and refresh to see updates.</li>
    </ol>
</div>
<?php admin_footer(); ?>
