/**
 * Naseeb Consultant — CMS content loader
 * Fetches /api/content.php and fills editable text + images.
 * If the API is unavailable, the static HTML remains unchanged.
 */
(function () {
    const API_URL = 'api/content.php';

    const setText = (selector, value, asHtml = false) => {
        if (value == null || value === '') return;
        document.querySelectorAll(selector).forEach((el) => {
            if (asHtml) el.innerHTML = value;
            else el.textContent = value;
        });
    };

    const setAttr = (selector, attr, value) => {
        if (value == null || value === '') return;
        document.querySelectorAll(selector).forEach((el) => {
            el.setAttribute(attr, value);
        });
    };

    const applyByDataCms = (content, settings) => {
        document.querySelectorAll('[data-cms]').forEach((el) => {
            const key = el.getAttribute('data-cms');
            if (!key) return;
            const value = Object.prototype.hasOwnProperty.call(content, key)
                ? content[key]
                : settings[key];
            if (value == null) return;
            const mode = el.getAttribute('data-cms-mode') || 'text';
            if (mode === 'html') el.innerHTML = value;
            else if (mode === 'href') el.setAttribute('href', value);
            else if (mode === 'src') el.setAttribute('src', value);
            else el.textContent = value;
        });

        document.querySelectorAll('[data-cms-setting]').forEach((el) => {
            const key = el.getAttribute('data-cms-setting');
            const value = settings[key];
            if (value == null) return;
            const mode = el.getAttribute('data-cms-mode') || 'text';
            if (mode === 'html') el.innerHTML = value;
            else if (mode === 'href') el.setAttribute('href', value);
            else if (mode === 'src') el.setAttribute('src', value);
            else el.textContent = value;
        });
    };

    const buildDestinationCard = (d, forHome) => {
        const img = d.image_path || 'images/uk.jpg';
        const tag = d.tagline ? ` <span>${escapeHtml(d.tagline)}</span>` : '';
        const applyHref = forHome ? 'destinations.html' : `contact.html?country=${encodeURIComponent(d.slug)}`;
        const applyLabel = forHome ? 'Explore Universities' : `Apply for ${escapeHtml(d.name)}`;
        return `
        <div class="country-card" data-dest-slug="${escapeAttr(d.slug)}">
            <div class="country-img-wrapper">
                <img src="${escapeAttr(img)}" class="country-img" alt="Study in ${escapeAttr(d.name)}">
                <div class="country-badge-flag">${escapeHtml(d.badge || d.name)}</div>
            </div>
            <div class="country-card-body">
                <h3 class="country-card-title">${escapeHtml(d.name)}${tag}</h3>
                <p style="font-size:0.88rem; color:var(--color-text-light); margin-bottom:1rem;">${escapeHtml(d.description || '')}</p>
                <ul class="country-details-list">
                    <li><span>Avg Tuition:</span> <span>${escapeHtml(d.tuition || '')}</span></li>
                    <li><span>Intakes:</span> <span>${escapeHtml(d.intakes || '')}</span></li>
                    <li><span>IELTS/PTE:</span> <span>${escapeHtml(d.ielts || '')}</span></li>
                </ul>
                <a href="${escapeAttr(applyHref)}" class="btn btn-outline-navy" style="width:100%;">${applyLabel}</a>
            </div>
        </div>`;
    };

    const renderDestinations = (destinations) => {
        const homeGrid = document.querySelector('#countries-showcase .country-grid');
        if (homeGrid) {
            const homeItems = destinations.filter((d) => Number(d.show_on_home) === 1);
            if (homeItems.length) {
                homeGrid.innerHTML = homeItems.map((d) => buildDestinationCard(d, true)).join('');
            }
        }

        const listGrid = document.getElementById('country-cards-grid');
        if (listGrid && destinations.length) {
            listGrid.innerHTML = destinations.map((d) => buildDestinationCard(d, false)).join('');
            // Re-bind search filter if present
            const search = document.getElementById('country-search');
            if (search) {
                search.dispatchEvent(new Event('input'));
            }
        }
    };

    const renderTestimonials = (testimonials) => {
        const container = document.getElementById('testimonial-slider-container');
        const dotsWrap = document.querySelector('.testimonials-wrapper .carousel-dots');
        if (!container || !testimonials.length) return;

        container.innerHTML = testimonials.map((t, i) => `
            <div class="testimonial-slide${i === 0 ? ' active' : ''}" id="testi-slide-${i + 1}">
                <div class="testimonial-quote-icon"><i class="fas fa-quote-left"></i></div>
                <p class="testimonial-text">${escapeHtml(t.quote_text)}</p>
                <div class="testimonial-author">
                    <img src="${escapeAttr(t.image_path || '')}" class="testimonial-img" alt="${escapeAttr(t.student_name)}">
                    <span class="author-name">${escapeHtml(t.student_name)}</span>
                    <span class="author-meta">${escapeHtml(t.meta_line || '')}</span>
                </div>
            </div>
        `).join('');

        if (dotsWrap) {
            dotsWrap.innerHTML = testimonials.map((_, i) =>
                `<span class="dot${i === 0 ? ' active' : ''}"></span>`
            ).join('');
        }

        // Re-init slider if main.js exposed a helper; otherwise trigger DOMContentLoaded pattern via custom event
        document.dispatchEvent(new CustomEvent('cms:testimonials-updated'));
    };

    const renderTicker = (tickerText) => {
        const track = document.querySelector('#university-ticker .ticker-track');
        if (!track || !tickerText) return;
        const names = tickerText.split(/\n+/).map((s) => s.trim()).filter(Boolean);
        if (!names.length) return;
        const items = names.concat(names); // duplicate for infinite scroll
        track.innerHTML = items.map((n) => `<div class="ticker-item"><span>${escapeHtml(n)}</span></div>`).join('');
    };

    const applyHomeStats = (content) => {
        const map = [
            { target: 'home_stat_visa', label: 'home_stat_visa_label', index: 0 },
            { target: 'home_stat_unis', label: 'home_stat_unis_label', index: 1 },
            { target: 'home_stat_students', label: 'home_stat_students_label', index: 2 },
        ];
        const items = document.querySelectorAll('.hero-stats .stat-item');
        map.forEach((m) => {
            const el = items[m.index];
            if (!el) return;
            const counter = el.querySelector('.counter-val');
            if (counter && content[m.target]) {
                counter.setAttribute('data-target', content[m.target]);
                counter.textContent = '0';
            }
            const label = el.querySelector('p');
            if (label && content[m.label]) label.textContent = content[m.label];
        });
        // Restart counters via custom event handled in main.js if available
        document.dispatchEvent(new CustomEvent('cms:stats-updated'));
    };

    const applyWhatsApp = (settings) => {
        const number = settings.whatsapp_number;
        const message = settings.whatsapp_message || '';
        if (number) {
            const href = `https://wa.me/${number}?text=${encodeURIComponent(message)}`;
            document.querySelectorAll('#whatsapp-chat-box .whatsapp-footer a, a[href*="wa.me"]').forEach((a) => {
                if (a.closest('#wa-portal-widget') || a.getAttribute('href')?.includes('wa.me')) {
                    if (a.closest('#wa-portal-widget')) a.setAttribute('href', href);
                }
            });
            const waBtn = document.querySelector('#whatsapp-chat-box .whatsapp-footer a');
            if (waBtn) waBtn.setAttribute('href', href);
        }
        if (settings.whatsapp_greeting) {
            const bubble = document.querySelector('.whatsapp-bubble');
            if (bubble) bubble.textContent = settings.whatsapp_greeting;
        }
        if (settings.site_name) {
            const waName = document.querySelector('.whatsapp-header-info h4');
            if (waName) waName.textContent = settings.site_name;
        }
    };

    const applyContactBlocks = (settings) => {
        if (settings.phone) {
            document.querySelectorAll('[data-cms-setting="phone"]').forEach((el) => {
                el.textContent = settings.phone;
            });
            // Footer phone span
            document.querySelectorAll('.footer-contact-info li').forEach((li) => {
                if (li.querySelector('.fa-phone-alt')) {
                    const span = li.querySelector('span');
                    if (span) span.textContent = settings.phone;
                }
            });
            document.querySelectorAll('a[href^="tel:"]').forEach((a) => {
                a.textContent = settings.phone;
                a.setAttribute('href', 'tel:' + String(settings.phone).replace(/\s+/g, ''));
            });
        }
        if (settings.email) {
            document.querySelectorAll('.footer-contact-info li').forEach((li) => {
                if (li.querySelector('.fa-envelope')) {
                    const span = li.querySelector('span');
                    if (span) span.textContent = settings.email;
                }
            });
            document.querySelectorAll('a[href^="mailto:"]').forEach((a) => {
                a.textContent = settings.email;
                a.setAttribute('href', 'mailto:' + settings.email);
            });
        }
        if (settings.address) {
            document.querySelectorAll('.footer-contact-info li').forEach((li) => {
                if (li.querySelector('.fa-map-marker-alt')) {
                    const span = li.querySelector('span');
                    if (span) span.textContent = settings.address;
                }
            });
        }
        if (settings.hours) {
            document.querySelectorAll('.footer-contact-info li').forEach((li) => {
                if (li.querySelector('.fa-clock')) {
                    const span = li.querySelector('span');
                    if (span) span.textContent = settings.hours;
                }
            });
        }
        if (settings.footer_about) {
            setText('.footer-about-text', settings.footer_about);
        }
        if (settings.logo_path) {
            setAttr('.logo-img, .footer-logo-img', 'src', settings.logo_path);
        }
        if (settings.header_cta_text) {
            const cta = document.getElementById('header-cta-btn');
            if (cta) cta.textContent = settings.header_cta_text;
        }
        if (settings.header_cta_link) {
            const cta = document.getElementById('header-cta-btn');
            if (cta) cta.setAttribute('href', settings.header_cta_link);
        }

        const socialMap = {
            facebook_url: 'Facebook',
            twitter_url: 'Twitter',
            instagram_url: 'Instagram',
            linkedin_url: 'LinkedIn',
        };
        Object.entries(socialMap).forEach(([key, label]) => {
            if (!settings[key]) return;
            document.querySelectorAll(`.social-btn[aria-label="${label}"]`).forEach((a) => {
                a.setAttribute('href', settings[key]);
            });
        });
    };

    const applyHomeFeatures = (content) => {
        const cards = document.querySelectorAll('#why-choose-us .feature-card');
        const feats = [
            ['home_feat1_title', 'home_feat1_text'],
            ['home_feat2_title', 'home_feat2_text'],
            ['home_feat3_title', 'home_feat3_text'],
        ];
        feats.forEach((pair, i) => {
            const card = cards[i];
            if (!card) return;
            const h3 = card.querySelector('h3');
            const p = card.querySelector('p');
            if (h3 && content[pair[0]]) h3.textContent = content[pair[0]];
            if (p && content[pair[1]]) p.textContent = content[pair[1]];
        });
    };

    const applyCta = (content) => {
        const banner = document.getElementById('action-banner');
        if (!banner) return;
        const h2 = banner.querySelector('h2');
        const p = banner.querySelector('p');
        const links = banner.querySelectorAll('a.btn');
        if (h2 && content.home_cta_title) h2.textContent = content.home_cta_title;
        if (p && content.home_cta_desc) p.textContent = content.home_cta_desc;
        if (links[0] && content.home_cta_btn1_text) {
            links[0].textContent = content.home_cta_btn1_text;
            if (content.home_cta_btn1_link) links[0].setAttribute('href', content.home_cta_btn1_link);
        }
        if (links[1] && content.home_cta_btn2_text) {
            links[1].textContent = content.home_cta_btn2_text;
            if (content.home_cta_btn2_link) links[1].setAttribute('href', content.home_cta_btn2_link);
        }
    };

    const applyHero = (content) => {
        const badge = document.querySelector('.hero-badge');
        if (badge && content.home_hero_badge) {
            badge.innerHTML = `<i class="fas fa-award"></i> ${escapeHtml(content.home_hero_badge)}`;
        }
        if (content.home_hero_title) setText('.hero-title', content.home_hero_title, true);
        if (content.home_hero_desc) setText('.hero-desc', content.home_hero_desc);
        const btns = document.querySelectorAll('.hero-buttons a.btn');
        if (btns[0] && content.home_hero_btn1_text) {
            btns[0].innerHTML = `${escapeHtml(content.home_hero_btn1_text)} <i class="fas fa-arrow-right"></i>`;
            if (content.home_hero_btn1_link) btns[0].setAttribute('href', content.home_hero_btn1_link);
        }
        if (btns[1] && content.home_hero_btn2_text) {
            btns[1].textContent = content.home_hero_btn2_text;
            if (content.home_hero_btn2_link) btns[1].setAttribute('href', content.home_hero_btn2_link);
        }
        if (content.home_form_title) setText('.glass-card-title', content.home_form_title);
        if (content.home_form_desc) setText('.glass-card-desc', content.home_form_desc);
        applyHomeStats(content);
    };

    const applySectionHeaders = (content) => {
        const why = document.getElementById('why-choose-us');
        if (why) {
            const sub = why.querySelector('.section-subtitle');
            const title = why.querySelector('.section-title');
            const desc = why.querySelector('.section-desc');
            if (sub && content.home_why_subtitle) sub.textContent = content.home_why_subtitle;
            if (title && content.home_why_title) title.textContent = content.home_why_title;
            if (desc && content.home_why_desc) desc.textContent = content.home_why_desc;
        }
        const dest = document.getElementById('countries-showcase');
        if (dest) {
            const sub = dest.querySelector('.section-subtitle');
            const title = dest.querySelector('.section-header h2');
            const desc = dest.querySelector('.section-desc');
            if (sub && content.home_dest_subtitle) sub.textContent = content.home_dest_subtitle;
            if (title && content.home_dest_title) title.textContent = content.home_dest_title;
            if (desc && content.home_dest_desc) desc.textContent = content.home_dest_desc;
        }
        const testi = document.getElementById('testimonials');
        if (testi) {
            const sub = testi.querySelector('.section-subtitle');
            const title = testi.querySelector('.section-header h2');
            const desc = testi.querySelector('.section-desc');
            if (sub && content.home_testi_subtitle) sub.textContent = content.home_testi_subtitle;
            if (title && content.home_testi_title) title.textContent = content.home_testi_title;
            if (desc && content.home_testi_desc) desc.textContent = content.home_testi_desc;
        }

        // Page banners
        const bannerMap = {
            'contact-banner': 'contact_banner_title',
            'destinations-banner': 'destinations_banner_title',
            // about / services / scholarships use page-banner without unique ids sometimes
        };
        Object.entries(bannerMap).forEach(([id, key]) => {
            const el = document.querySelector(`#${id} .page-banner-title`);
            if (el && content[key]) el.textContent = content[key];
        });

        // Generic page banners by path
        const path = (location.pathname.split('/').pop() || 'index.html').toLowerCase();
        const pageBanners = {
            'about.html': 'about_banner_title',
            'services.html': 'services_banner_title',
            'scholarships.html': 'scholarships_banner_title',
            'contact.html': 'contact_banner_title',
            'destinations.html': 'destinations_banner_title',
        };
        if (pageBanners[path] && content[pageBanners[path]]) {
            const title = document.querySelector('.page-banner-title');
            if (title) title.textContent = content[pageBanners[path]];
        }

        // Contact panel
        const panelTitle = document.querySelector('.contact-info-panel > h3');
        if (panelTitle && content.contact_panel_title) panelTitle.textContent = content.contact_panel_title;
        const panelDesc = document.querySelector('.contact-info-panel > p');
        if (panelDesc && content.contact_panel_desc) panelDesc.textContent = content.contact_panel_desc;
        const formTitle = document.querySelector('.contact-form-box > h3');
        if (formTitle && content.contact_form_title) formTitle.textContent = content.contact_form_title;

        // Contact address block detail
        const addrBlock = document.querySelector('.contact-item-details h4');
        // filled via settings in applyContactPageDetails
    };

    const applyAboutPage = (content) => {
        if (!document.getElementById('about-legacy')) return;

        const ceo = document.querySelector('.ceo-message-card');
        if (ceo) {
            const title = ceo.querySelector('h3');
            const quote = ceo.querySelector('p');
            const name = ceo.querySelector('h4');
            const role = ceo.querySelector('h4 + p');
            const initials = ceo.querySelector('div[style*="border-radius: 50%"]');
            if (title && content.about_ceo_title) title.textContent = content.about_ceo_title;
            if (quote && content.about_ceo_quote) quote.textContent = content.about_ceo_quote;
            if (name && content.about_ceo_name) name.textContent = content.about_ceo_name;
            if (role && content.about_ceo_role) role.textContent = content.about_ceo_role;
            if (initials && content.about_ceo_initials) initials.textContent = content.about_ceo_initials;
        }

        const storyCol = document.querySelector('#about-legacy .contact-layout > div:last-child');
        if (storyCol) {
            const sub = storyCol.querySelector('.section-subtitle');
            const title = storyCol.querySelector('h2');
            const paras = storyCol.querySelectorAll('p');
            const checks = storyCol.querySelectorAll('.features-grid span');
            if (sub && content.about_story_subtitle) sub.textContent = content.about_story_subtitle;
            if (title && content.about_story_title) title.textContent = content.about_story_title;
            if (paras[0] && content.about_story_p1) paras[0].textContent = content.about_story_p1;
            if (paras[1] && content.about_story_p2) paras[1].textContent = content.about_story_p2;
            if (checks[0] && content.about_check1) checks[0].textContent = content.about_check1;
            if (checks[1] && content.about_check2) checks[1].textContent = content.about_check2;
        }

        const values = document.getElementById('about-values');
        if (values) {
            const sub = values.querySelector('.section-subtitle');
            const title = values.querySelector('.section-header h2');
            const desc = values.querySelector('.section-desc');
            if (sub && content.about_values_subtitle) sub.textContent = content.about_values_subtitle;
            if (title && content.about_values_title) title.textContent = content.about_values_title;
            if (desc && content.about_values_desc) desc.textContent = content.about_values_desc;
            const cards = values.querySelectorAll('.feature-card');
            const map = [
                ['about_value1_title', 'about_value1_text'],
                ['about_value2_title', 'about_value2_text'],
                ['about_value3_title', 'about_value3_text'],
            ];
            map.forEach((pair, i) => {
                if (!cards[i]) return;
                const h3 = cards[i].querySelector('h3');
                const p = cards[i].querySelector('p');
                if (h3 && content[pair[0]]) h3.textContent = content[pair[0]];
                if (p && content[pair[1]]) p.textContent = content[pair[1]];
            });
        }

        const cred = document.getElementById('about-certifications');
        if (cred) {
            const sub = cred.querySelector('.section-subtitle');
            const title = cred.querySelector('.section-header h2');
            const desc = cred.querySelector('.section-desc');
            if (sub && content.about_cred_subtitle) sub.textContent = content.about_cred_subtitle;
            if (title && content.about_cred_title) title.textContent = content.about_cred_title;
            if (desc && content.about_cred_desc) desc.textContent = content.about_cred_desc;
            const boxes = cred.querySelectorAll('.features-grid > div');
            const cmap = [
                ['about_cred1_title', 'about_cred1_text'],
                ['about_cred2_title', 'about_cred2_text'],
                ['about_cred3_title', 'about_cred3_text'],
                ['about_cred4_title', 'about_cred4_text'],
            ];
            cmap.forEach((pair, i) => {
                if (!boxes[i]) return;
                const h4 = boxes[i].querySelector('h4');
                const p = boxes[i].querySelector('p');
                if (h4 && content[pair[0]]) h4.textContent = content[pair[0]];
                if (p && content[pair[1]]) p.textContent = content[pair[1]];
            });
        }
    };

    const applyServicesPage = (content) => {
        if (!document.getElementById('services-details')) return;

        const details = document.getElementById('services-details');
        const sub = details.querySelector('.section-subtitle');
        const title = details.querySelector('.section-header h2');
        const desc = details.querySelector('.section-desc');
        if (sub && content.services_subtitle) sub.textContent = content.services_subtitle;
        if (title && content.services_title) title.textContent = content.services_title;
        if (desc && content.services_desc) desc.textContent = content.services_desc;

        const cards = details.querySelectorAll('.service-card');
        for (let i = 0; i < 3; i++) {
            const card = cards[i];
            if (!card) continue;
            const n = i + 1;
            const h3 = card.querySelector('h3');
            const p = card.querySelector('p');
            const bullets = card.querySelectorAll('.service-bullets li');
            if (h3 && content[`services_card${n}_title`]) h3.textContent = content[`services_card${n}_title`];
            if (p && content[`services_card${n}_text`]) p.textContent = content[`services_card${n}_text`];
            [1, 2, 3].forEach((b) => {
                const li = bullets[b - 1];
                if (!li || !content[`services_card${n}_b${b}`]) return;
                li.innerHTML = `<i class="fas fa-check"></i> ${escapeHtml(content[`services_card${n}_b${b}`])}`;
            });
        }

        const tool = document.getElementById('eligibility-tool');
        if (tool) {
            const tsub = tool.querySelector('.section-subtitle');
            const ttitle = tool.querySelector('.section-header h2');
            const tdesc = tool.querySelector('.section-desc');
            if (tsub && content.services_tool_subtitle) tsub.textContent = content.services_tool_subtitle;
            if (ttitle && content.services_tool_title) ttitle.textContent = content.services_tool_title;
            if (tdesc && content.services_tool_desc) tdesc.textContent = content.services_tool_desc;

            const sidebar = tool.querySelector('.calc-sidebar');
            if (sidebar) {
                const sh = sidebar.querySelector('h3');
                const sp = sidebar.querySelector(':scope > p');
                if (sh && content.services_tool_sidebar_title) sh.textContent = content.services_tool_sidebar_title;
                if (sp && content.services_tool_sidebar_text) sp.textContent = content.services_tool_sidebar_text;
                const feats = sidebar.querySelectorAll('.sidebar-feat-item');
                if (feats[0]) {
                    const h5 = feats[0].querySelector('h5');
                    const p = feats[0].querySelector('p');
                    if (h5 && content.services_tool_feat1_title) h5.textContent = content.services_tool_feat1_title;
                    if (p && content.services_tool_feat1_text) p.textContent = content.services_tool_feat1_text;
                }
                if (feats[1]) {
                    const h5 = feats[1].querySelector('h5');
                    const p = feats[1].querySelector('p');
                    if (h5 && content.services_tool_feat2_title) h5.textContent = content.services_tool_feat2_title;
                    if (p && content.services_tool_feat2_text) p.textContent = content.services_tool_feat2_text;
                }
            }
        }
    };

    const applyScholarshipsPage = (content) => {
        if (!document.getElementById('scholarship-intro')) return;

        const intro = document.getElementById('scholarship-intro');
        const sub = intro.querySelector('.section-subtitle');
        const title = intro.querySelector('h2');
        const paras = intro.querySelectorAll('p');
        if (sub && content.scholarships_intro_subtitle) sub.textContent = content.scholarships_intro_subtitle;
        if (title && content.scholarships_intro_title) title.textContent = content.scholarships_intro_title;
        // paras[0], paras[1] are intro; tip has its own p
        const tipBox = intro.querySelector('[style*="border-left"]');
        const introParas = [];
        intro.querySelectorAll('.contact-layout > div:first-child > p').forEach((p) => introParas.push(p));
        // Fallback: first two p outside tip box
        const allP = Array.from(intro.querySelectorAll('p')).filter((p) => !tipBox || !tipBox.contains(p));
        if (allP[0] && content.scholarships_intro_p1) allP[0].textContent = content.scholarships_intro_p1;
        if (allP[1] && content.scholarships_intro_p2) allP[1].textContent = content.scholarships_intro_p2;
        if (tipBox) {
            const tipTitle = tipBox.querySelector('h4');
            const tipText = tipBox.querySelector('p');
            if (tipTitle && content.scholarships_tip_title) {
                tipTitle.innerHTML = `<i class="fas fa-lightbulb" style="color:var(--color-gold)"></i> ${escapeHtml(content.scholarships_tip_title)}`;
            }
            if (tipText && content.scholarships_tip_text) tipText.textContent = content.scholarships_tip_text;
        }
        const img = intro.querySelector('img');
        if (img && content.scholarships_image) img.setAttribute('src', content.scholarships_image);

        const opps = document.getElementById('scholarship-opportunities');
        if (opps) {
            const osub = opps.querySelector('.section-subtitle');
            const otitle = opps.querySelector('.section-header h2');
            const odesc = opps.querySelector('.section-desc');
            if (osub && content.scholarships_list_subtitle) osub.textContent = content.scholarships_list_subtitle;
            if (otitle && content.scholarships_list_title) otitle.textContent = content.scholarships_list_title;
            if (odesc && content.scholarships_list_desc) odesc.textContent = content.scholarships_list_desc;

            const cards = opps.querySelectorAll('.services-grid .service-card');
            for (let i = 0; i < 2; i++) {
                const card = cards[i];
                if (!card) continue;
                const n = i + 1;
                const h3 = card.querySelector('h3');
                const p = card.querySelector('p');
                const bullets = card.querySelectorAll('.service-bullets li');
                if (h3 && content[`scholarships_prog${n}_title`]) {
                    const icon = h3.querySelector('i');
                    const iconHtml = icon ? icon.outerHTML + ' ' : '';
                    h3.innerHTML = iconHtml + escapeHtml(content[`scholarships_prog${n}_title`]);
                }
                if (p && content[`scholarships_prog${n}_text`]) p.textContent = content[`scholarships_prog${n}_text`];
                [1, 2, 3].forEach((b) => {
                    const li = bullets[b - 1];
                    if (!li || !content[`scholarships_prog${n}_b${b}`]) return;
                    li.innerHTML = `<i class="fas fa-check" style="color:var(--color-success)"></i> ${escapeHtml(content[`scholarships_prog${n}_b${b}`])}`;
                });
            }

            const stepsWrap = opps.querySelector('.features-grid');
            const stepsTitle = opps.querySelector('h3');
            if (stepsTitle && content.scholarships_steps_title) stepsTitle.textContent = content.scholarships_steps_title;
            if (stepsWrap) {
                const steps = stepsWrap.children;
                for (let i = 0; i < 3; i++) {
                    const step = steps[i];
                    if (!step) continue;
                    const n = i + 1;
                    const h4 = step.querySelector('h4');
                    const p = step.querySelector('p');
                    if (h4 && content[`scholarships_step${n}_title`]) h4.textContent = content[`scholarships_step${n}_title`];
                    if (p && content[`scholarships_step${n}_text`]) p.textContent = content[`scholarships_step${n}_text`];
                }
            }
        }
    };

    const applyDestinationsPageText = (content) => {
        const search = document.getElementById('country-search');
        if (search && content.destinations_search_placeholder) {
            search.setAttribute('placeholder', content.destinations_search_placeholder);
        }
        const hint = document.querySelector('.filter-search-container > div:last-child');
        if (hint && content.destinations_search_hint && hint.querySelector('i') === null) {
            hint.textContent = content.destinations_search_hint;
        }
    };

    const applyContactPageDetails = (settings) => {
        const items = document.querySelectorAll('.contact-item');
        items.forEach((item) => {
            const icon = item.querySelector('i');
            const p = item.querySelector('.contact-item-details p');
            if (!icon || !p) return;
            if (icon.classList.contains('fa-map-marker-alt') && settings.address) {
                p.innerHTML = escapeHtml(settings.address).replace(/,\s*/g, ',<br> ');
            }
            if (icon.classList.contains('fa-clock') && settings.hours) {
                const note = settings.hours_note ? `<br> ${escapeHtml(settings.hours_note)}` : '';
                p.innerHTML = `${escapeHtml(settings.hours)} ${note}`;
            }
        });
    };

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function escapeAttr(str) {
        return escapeHtml(str).replace(/'/g, '&#39;');
    }

    async function loadCms() {
        try {
            const res = await fetch(API_URL, { credentials: 'same-origin' });
            if (!res.ok) throw new Error('API HTTP ' + res.status);
            const data = await res.json();
            if (!data.ok) throw new Error(data.message || 'CMS error');

            const content = data.content || {};
            const settings = data.settings || {};

            applyByDataCms(content, settings);
            applyHero(content);
            applyHomeFeatures(content);
            applySectionHeaders(content);
            applyCta(content);
            renderTicker(content.home_ticker);
            renderDestinations(data.destinations || []);
            renderTestimonials(data.testimonials || []);
            applyContactBlocks(settings);
            applyContactPageDetails(settings);
            applyWhatsApp(settings);
            applyAboutPage(content);
            applyServicesPage(content);
            applyScholarshipsPage(content);
            applyDestinationsPageText(content);

            document.documentElement.classList.add('cms-loaded');
        } catch (err) {
            // Keep static HTML fallback
            console.warn('CMS content not loaded:', err.message || err);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadCms);
    } else {
        loadCms();
    }
})();
