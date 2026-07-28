<?php
/**
 * Public CMS API — returns all editable site content as JSON.
 * GET /api/content.php
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-store, max-age=0');

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

try {
    $settings = get_all_settings();
    $content  = get_all_content();

    $destStmt = db()->query(
        'SELECT id, slug, name, badge, tagline, description, tuition, intakes, ielts, image_path, show_on_home, sort_order
         FROM destinations WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
    );
    $destinations = $destStmt->fetchAll();
    foreach ($destinations as &$d) {
        $d['image_path'] = public_asset_url($d['image_path']);
        $d['show_on_home'] = (int) $d['show_on_home'];
        $d['sort_order'] = (int) $d['sort_order'];
        $d['id'] = (int) $d['id'];
    }
    unset($d);

    $testiStmt = db()->query(
        'SELECT id, student_name, meta_line, quote_text, image_path, sort_order
         FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
    );
    $testimonials = $testiStmt->fetchAll();
    foreach ($testimonials as &$t) {
        $t['image_path'] = public_asset_url($t['image_path']);
        $t['id'] = (int) $t['id'];
        $t['sort_order'] = (int) $t['sort_order'];
    }
    unset($t);

    if (!empty($settings['logo_path'])) {
        $settings['logo_path'] = public_asset_url($settings['logo_path']);
    }

    echo json_encode([
        'ok'           => true,
        'settings'     => $settings,
        'content'      => $content,
        'destinations' => $destinations,
        'testimonials' => $testimonials,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok'      => false,
        'error'   => 'CMS unavailable',
        'message' => $e->getMessage(),
    ]);
}
