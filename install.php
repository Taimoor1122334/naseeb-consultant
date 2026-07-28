<?php
/**
 * One-time installer: creates tables, default admin, and seeds current website content.
 * Visit: https://yoursite.com/install.php
 * DELETE this file after successful install.
 */

$configFile = __DIR__ . '/config/database.php';
$config = require $configFile;
$messages = [];
$errors = [];
$done = false;

/**
 * Connect to the existing MySQL database.
 * Shared hosts (StackCP/cPanel) already create the DB in the panel —
 * PHP usually cannot CREATE DATABASE there.
 */
function install_pdo(array $config): PDO
{
    if (($config['pass'] ?? '') === '' || ($config['pass'] ?? '') === 'CHANGE_THIS_PASSWORD') {
        throw new RuntimeException(
            'Set your MySQL password in the hosting panel first, then put the same password in config/database.php (replace CHANGE_THIS_PASSWORD).'
        );
    }

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $config['host'],
        $config['name'],
        $config['charset'] ?? 'utf8mb4'
    );

    try {
        return new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    } catch (PDOException $e) {
        $hint = ' Check config/database.php: host should be your Server name (e.g. sdb-68.hosting.stackcp.net), '
            . 'name and user should match the Database/Username, and pass must be the password you saved in the panel.';
        throw new RuntimeException('Could not connect to MySQL: ' . $e->getMessage() . $hint);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $adminUser = trim($_POST['admin_username'] ?? 'admin');
        $adminPass = (string) ($_POST['admin_password'] ?? '');

        if ($adminUser === '' || strlen($adminPass) < 6) {
            throw new RuntimeException('Admin username required and password must be at least 6 characters.');
        }

        // Use the database you already created in the hosting panel
        $pdo = install_pdo($config);

        $schema = file_get_contents(__DIR__ . '/sql/schema.sql');
        $pdo->exec($schema);

        // Admin
        $hash = password_hash($adminPass, PASSWORD_DEFAULT);
        $pdo->prepare('DELETE FROM admins')->execute();
        $pdo->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)')->execute([$adminUser, $hash]);

        // Settings
        $settings = [
            'site_name'        => 'Naseeb Consultant',
            'logo_path'        => 'logo.png',
            'phone'            => '0335 6896333',
            'email'            => 'naseebconsultant33@gmail.com',
            'address'          => 'Office #1, 3rd Floor, Shalimar Plaza, Moon Market, Iqbal Town, Lahore, Pakistan',
            'hours'            => 'Sun - Thu: 10:00 AM - 6:00 PM',
            'hours_note'       => '(Friday & Saturday Closed)',
            'whatsapp_number'  => '923356896333',
            'whatsapp_message' => 'AOA, I am interested in study abroad counseling with Naseeb Consultant.',
            'whatsapp_greeting'=> 'Aoa! Thank you for contacting Naseeb Consultant. How can we help you plan your international education today?',
            'footer_about'     => 'Authorized study abroad and student visa immigration counseling firm. Helping students shape successful futures at top institutions since 2011.',
            'facebook_url'     => '#',
            'twitter_url'      => '#',
            'instagram_url'    => '#',
            'linkedin_url'     => '#',
            'header_cta_text'  => 'Book Free Session',
            'header_cta_link'  => 'contact.html',
        ];

        $setStmt = $pdo->prepare(
            'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
        );
        foreach ($settings as $k => $v) {
            $setStmt->execute([$k, $v]);
        }

        // Home / shared page content
        $content = [
            'home_hero_badge'       => 'Established since 2011 in Pakistan',
            'home_hero_title'       => 'Your Pathway To <span>Global Education</span>',
            'home_hero_desc'        => 'Naseeb Consultant helps Pakistani students achieve their dreams of studying in top international universities across the UK, Turkey, China, Lithuania, Poland, France, Russia, and other European countries. Secure admissions, scholarships, and student visas with expert guidance.',
            'home_hero_btn1_text'   => 'Explore Countries',
            'home_hero_btn1_link'   => 'destinations.html',
            'home_hero_btn2_text'   => 'Learn About Us',
            'home_hero_btn2_link'   => 'about.html',
            'home_stat_visa'        => '98',
            'home_stat_visa_label'  => 'Visa Success',
            'home_stat_unis'        => '200',
            'home_stat_unis_label'  => 'Partner Unis',
            'home_stat_students'    => '5000',
            'home_stat_students_label' => 'Students Placed',
            'home_form_title'       => 'Free Consultation',
            'home_form_desc'        => 'Fill in details and our senior counselor will call you.',
            'home_why_subtitle'     => 'Excellence In Consultancy',
            'home_why_title'        => 'Why Students Choose Naseeb Consultant',
            'home_why_desc'         => 'Over a decade of successful visa assistance and strong university partnerships, providing students with professional, transparent counseling.',
            'home_feat1_title'      => 'Certified & Authorized',
            'home_feat1_text'       => 'Naseeb Consultant is officially registered and recognized by top international admissions boards and embassies, providing authentic documentation processing.',
            'home_feat2_title'      => 'Scholarship Specialists',
            'home_feat2_text'       => 'We specialize in admissions and scholarships for UK, Turkey, China, Lithuania, Poland, France, Russia, and other European destinations.',
            'home_feat3_title'      => 'End-to-End Support',
            'home_feat3_text'       => 'From university application and documents legalizations to IELTS preparation, interview practice, and visa file preparation, Naseeb Consultant handles everything.',
            'home_dest_subtitle'    => 'Top Destinations',
            'home_dest_title'       => 'Preferred Study Destinations',
            'home_dest_desc'        => 'Explore our top study abroad destinations, featuring fully funded scholarships, top-ranked universities, and excellent career settlement paths.',
            'home_testi_subtitle'   => 'Naseeb Consultant Success Stories',
            'home_testi_title'      => 'What Our Students Say',
            'home_testi_desc'       => 'Real stories from students in Lahore who successfully transitioned to their dream campus abroad with Naseeb Consultant.',
            'home_cta_title'        => 'Ready to Begin Your Visa Journey?',
            'home_cta_desc'         => "Don't let complex visa forms and university choices hold you back. Let Naseeb Consultant's experienced consultants handle your portfolio. Book your free in-person counseling session today.",
            'home_cta_btn1_text'    => 'Contact Lahore HQ',
            'home_cta_btn1_link'    => 'contact.html',
            'home_cta_btn2_text'    => 'Test Eligibility First',
            'home_cta_btn2_link'    => 'services.html',
            'home_ticker'           => "University of Manchester\nIstanbul University\nTsinghua University\nVilnius University\nUniversity of Warsaw\nSorbonne University\nLomonosov Moscow State University\nHumboldt University of Berlin",
            'contact_banner_title'  => 'Contact Lahore Headquarters',
            'contact_panel_title'   => 'Visit Lahore Office',
            'contact_panel_desc'    => 'Naseeb Consultant head office is located in Moon Market, Iqbal Town. Students are welcome to drop by for a free in-person session with certified counselors.',
            'contact_form_title'    => 'Schedule Consultation',
            'destinations_banner_title' => 'Study Destinations',
            'about_banner_title'    => 'About Our Profile',
            'services_banner_title' => 'Visa Services & Eligibility',
            'scholarships_banner_title' => 'Scholarship Highlights',
        ];

        $cStmt = $pdo->prepare(
            'INSERT INTO page_content (content_key, content_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE content_value = VALUES(content_value)'
        );
        foreach ($content as $k => $v) {
            $cStmt->execute([$k, $v]);
        }

        // Destinations
        $pdo->exec('DELETE FROM destinations');
        $destinations = [
            ['uk', 'United Kingdom', 'UNITED KINGDOM', 'Degree', 'World-class 1-year master\'s degrees and 3-year bachelor\'s programs with a 2-year graduate route work permit after studies.', '£13,000 GBP / Year', 'Sept & Jan', 'MOI / 6.0 Overall', 'images/uk.jpg', 1, 1],
            ['turkey', 'Turkey', 'TURKEY', 'Scholarships', 'Affordable European gateway with Türkiye Bursları scholarships covering tuition, accommodation, health insurance, and flights.', '$2,500 USD / Year', 'Sept & Feb', 'Accepted but not mandatory', 'images/turkey.jpg', 1, 2],
            ['china', 'China', 'CHINA', 'Study Visa', 'Rapidly growing hub for medicine, engineering, and business programs with CSC scholarships and affordable living costs.', '$3,000–$8,000 USD / Year', 'March & September', '5.5–6.0 or English-medium programs', 'images/china.avif', 1, 3],
            ['lithuania', 'Lithuania', 'LITHUANIA', 'Schengen EU', 'Affordable EU member state with English-taught programs, low living costs, and Schengen zone access.', '€2,000–€5,000 / Year', 'Sept & Feb', '5.5–6.0 overall', 'images/lithuania.jpg', 0, 4],
            ['poland', 'Poland', 'POLAND', 'Europe Core', 'Affordable cost of living and high-standard technical universities in the heart of Eastern Europe.', '€2,500 / Year', 'Oct & Feb', 'MOI accepted at select unis', 'images/poland.jpg', 0, 5],
            ['france', 'France', 'FRANCE', 'Campus France', 'World-renowned universities with affordable public tuition, rich culture, and strong post-study opportunities in the EU.', '€2,770 / Year (Public)', 'Sept & Jan', '6.0–6.5 overall', 'images/france.avif', 0, 6],
            ['russia', 'Russia', 'RUSSIA', 'MBBS & Tech', 'Highly affordable medical and technical degrees recognized globally, with low tuition and living expenses.', '$3,000–$6,000 USD / Year', 'Sept & Feb', 'Not required for most programs', 'images/russia.jpg', 0, 7],
            ['europe', 'Other European Countries', 'EUROPE', 'Schengen', 'Germany, Hungary, Spain, Sweden, and more — explore affordable EU programs with scholarship and work-study options.', '€0–€5,000 / Year', 'Sept & Jan (varies)', '5.5–6.5 depending on country', 'images/europ.avif', 0, 8],
        ];

        // Use destinations page longer descriptions where different — keep home-friendly ones above for UK/Turkey/China
        $destFull = [
            'uk' => ['United Kingdom', '(33 Unis)', 'Fast degrees (1-yr Master\'s, 3-yr Bachelor\'s) with 2 years post-graduation route work opportunities.', '£14,000 GBP / Year', 'Sept & Jan', '6.0 overall (MOI accepted)'],
            'turkey' => ['Turkey', '(Burslari)', 'Affordable European and Asian gateway. Full scholarships program including health insurance and flights.', '$2,500 USD / Year (Private)', 'Sept & Jan', 'Accepted but not mandatory'],
            'china' => ['China', '(CSC Scholarships)', 'Leading destination for medicine, engineering, and business with Chinese Government Scholarship (CSC) programs.', '$3,000–$8,000 USD / Year', 'March & September', '5.5–6.0 or English-medium'],
        ];

        $dStmt = $pdo->prepare(
            'INSERT INTO destinations (slug, name, badge, tagline, description, tuition, intakes, ielts, image_path, show_on_home, sort_order, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)'
        );
        foreach ($destinations as $d) {
            if (isset($destFull[$d[0]])) {
                $f = $destFull[$d[0]];
                $d[1] = $f[0];
                $d[3] = $f[1];
                $d[4] = $f[2];
                $d[5] = $f[3];
                $d[6] = $f[4];
                $d[7] = $f[5];
            }
            $dStmt->execute($d);
        }

        // Testimonials
        $pdo->exec('DELETE FROM testimonials');
        $tStmt = $pdo->prepare(
            'INSERT INTO testimonials (student_name, meta_line, quote_text, image_path, sort_order, is_active)
             VALUES (?, ?, ?, ?, ?, 1)'
        );
        $testimonials = [
            [
                'Fatima Sajid',
                'University of Manchester, United Kingdom',
                '"Naseeb Consultant made my dream of studying in the UK a reality. They helped me get admission to a top university and handled my student visa file perfectly. The team was professional and always available on WhatsApp. Best consultant in Lahore!"',
                'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=150',
                1,
            ],
            [
                'Ahmed Shah',
                'Istanbul University, Turkey',
                '"Applying to Turkey was really stressful, but Naseeb Consultant simplified everything. They helped me secure a Türkiye Bursları scholarship, prepared all my documents, and guided me through the embassy process. My visa was approved smoothly!"',
                'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=150',
                2,
            ],
            [
                'Zainab Siddiqui',
                'Tsinghua University, China',
                '"I wanted to study in China and Naseeb Consultant guided me through the entire CSC scholarship process. Their counselors helped with university selection, document preparation, and visa filing. Highly recommend Naseeb Consultant!"',
                'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150',
                3,
            ],
        ];
        foreach ($testimonials as $t) {
            $tStmt->execute($t);
        }

        $done = true;
        $messages[] = 'Installation complete. Database seeded with your current website content.';
        $messages[] = 'Login at /admin/login.php with the username and password you just set.';
        $messages[] = 'IMPORTANT: Delete install.php from the server now for security.';
    } catch (Throwable $ex) {
        $errors[] = $ex->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install CMS — Naseeb Consultant</title>
    <style>
        :root { --navy:#0f2744; --gold:#d97706; --bg:#f1f5f9; --card:#fff; --ok:#059669; --err:#dc2626; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: Georgia, 'Times New Roman', serif; background: linear-gradient(160deg,#0f2744 0%,#1e3a5f 45%,#334155 100%); min-height:100vh; color:#0f172a; }
        .wrap { max-width:560px; margin:4rem auto; padding:0 1rem; }
        .card { background:var(--card); border-radius:12px; padding:2rem; box-shadow:0 20px 50px rgba(0,0,0,.25); }
        h1 { margin:0 0 .5rem; font-size:1.75rem; color:var(--navy); }
        p.lead { color:#64748b; margin:0 0 1.5rem; font-family: system-ui, sans-serif; font-size:.95rem; }
        label { display:block; font-family:system-ui,sans-serif; font-size:.85rem; font-weight:600; margin:1rem 0 .4rem; }
        input { width:100%; padding:.75rem .9rem; border:1px solid #cbd5e1; border-radius:8px; font-size:1rem; }
        button { margin-top:1.5rem; width:100%; background:var(--gold); color:#fff; border:0; padding:.9rem; border-radius:8px; font-weight:700; font-size:1rem; cursor:pointer; font-family:system-ui,sans-serif; }
        button:hover { filter:brightness(.95); }
        .msg { padding:.85rem 1rem; border-radius:8px; margin-bottom:.75rem; font-family:system-ui,sans-serif; font-size:.9rem; }
        .ok { background:#ecfdf5; color:var(--ok); }
        .err { background:#fef2f2; color:var(--err); }
        .hint { margin-top:1.25rem; font-family:system-ui,sans-serif; font-size:.8rem; color:#64748b; line-height:1.5; }
        code { background:#f1f5f9; padding:.1rem .35rem; border-radius:4px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>Install CMS Dashboard</h1>
        <p class="lead">Creates MySQL tables and loads your current Naseeb Consultant website content so you can edit text and images from the admin panel.</p>

        <?php foreach ($errors as $err): ?>
            <div class="msg err"><?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>
        <?php foreach ($messages as $msg): ?>
            <div class="msg ok"><?= htmlspecialchars($msg) ?></div>
        <?php endforeach; ?>

        <?php if (!$done): ?>
            <p class="hint">1) Create the MySQL database in your hosting panel.<br>
            2) Set a database password and click Save.<br>
            3) Put host, database name, username, and password into <code>config/database.php</code>.<br>
            4) Then submit this form (admin login for the CMS dashboard).</p>
            <form method="post">
                <label for="admin_username">Admin username</label>
                <input id="admin_username" name="admin_username" value="admin" required>

                <label for="admin_password">Admin password (min 6 chars)</label>
                <input id="admin_password" name="admin_password" type="password" required minlength="6" placeholder="Choose a strong password">

                <button type="submit">Install &amp; Seed Content</button>
            </form>
        <?php else: ?>
            <p class="hint"><a href="admin/login.php">Go to Admin Login →</a></p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
