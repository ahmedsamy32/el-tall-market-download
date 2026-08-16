<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function render_public_header(string $title, string $description = '', bool $showHeader = true): void
{
    $title = $title !== '' ? $title . ' - ' . APP_NAME : APP_NAME;
    $description = $description !== '' ? $description : APP_DESCRIPTION;
    $canonical = current_url();
    $ogImage = absolute_url(site_logo_url());
    $metaRobots = str_starts_with(current_request_path(), '/admin') ? 'noindex, nofollow' : 'index, follow';
    $cspNonce = csp_nonce();
    $normalizeSocialUrl = static function (?string $value): ?string {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (!preg_match('~^https?://~i', $value)) {
            $value = 'https://' . $value;
        }

        return preg_match('~^https?://~i', $value) ? $value : null;
    };
    $facebookUrl = $normalizeSocialUrl(get_setting('social_facebook', ''));
    $instagramUrl = $normalizeSocialUrl(get_setting('social_instagram', ''));
    $tiktokUrl = $normalizeSocialUrl(get_setting('social_tiktok', ''));
    $shopUrl = get_setting('shop_url', '');
    $socialClass = 'inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white transition hover:-translate-y-0.5 hover:bg-white/10';
    $jsonLd = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => APP_NAME,
        'url' => app_origin(),
        'description' => $description,
        'inLanguage' => 'ar-EG',
        'publisher' => [
            '@type' => 'Organization',
            'name' => APP_NAME,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $ogImage,
            ],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    ?>
    <!doctype html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?= escape($description) ?>">
        <meta name="robots" content="<?= escape($metaRobots) ?>">
        <meta name="theme-color" content="#0f172a">
        <title><?= escape($title) ?></title>
        <link rel="canonical" href="<?= escape($canonical) ?>">
        <meta property="og:type" content="website">
        <meta property="og:title" content="<?= escape($title) ?>">
        <meta property="og:description" content="<?= escape($description) ?>">
        <meta property="og:url" content="<?= escape($canonical) ?>">
        <meta property="og:site_name" content="<?= escape(APP_NAME) ?>">
        <meta property="og:locale" content="ar_EG">
        <meta property="og:image" content="<?= escape($ogImage) ?>">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="<?= escape($title) ?>">
        <meta name="twitter:description" content="<?= escape($description) ?>">
        <meta name="twitter:image" content="<?= escape($ogImage) ?>">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?= asset('css/tailwind.css') ?>">
        <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
        <link rel="icon" href="<?= escape(site_favicon_url()) ?>">
        <link rel="apple-touch-icon" href="<?= escape(site_favicon_url()) ?>">
        <script nonce="<?= escape($cspNonce) ?>" type="application/ld+json"><?= $jsonLd ?></script>
    </head>
    <body class="font-arabic bg-slate-950 text-slate-100 antialiased">
    <?php if ($showHeader): ?>
        <header class="w-full border-b border-white/10 bg-transparent">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="<?= escape(url('')) ?>" class="flex items-center gap-2 text-white sm:gap-3">
                    <img src="<?= escape(site_logo_url()) ?>" alt="<?= escape(APP_NAME) ?>" class="h-12 w-12 rounded-lg bg-white/5 p-1 sm:h-16 sm:w-16 lg:h-20 lg:w-20">
                    <span class="text-sm font-bold sm:text-base"><?= escape(APP_NAME) ?></span>
                </a>
                <div class="flex flex-col items-end gap-3">
                    <?php if ($facebookUrl || $instagramUrl || $tiktokUrl): ?>
                        <div class="flex items-center gap-2">
                            <?php if ($facebookUrl): ?>
                                <a href="<?= escape($facebookUrl) ?>" class="<?= $socialClass ?>" target="_blank" rel="noopener" aria-label="Facebook">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                        <path d="M7 10v4h3v8h4v-8h3l1-4h-4V8a1 1 0 0 1 1-1h3V3h-3a5 5 0 0 0-5 5v2H7" />
                                    </svg>
                                </a>
                            <?php endif; ?>
                            <?php if ($instagramUrl): ?>
                                <a href="<?= escape($instagramUrl) ?>" class="<?= $socialClass ?>" target="_blank" rel="noopener" aria-label="Instagram">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                        <rect x="3" y="3" width="18" height="18" rx="4" />
                                        <circle cx="12" cy="12" r="4" />
                                        <circle cx="17" cy="7" r="1" />
                                    </svg>
                                </a>
                            <?php endif; ?>
                            <?php if ($tiktokUrl): ?>
                                <a href="<?= escape($tiktokUrl) ?>" class="<?= $socialClass ?>" target="_blank" rel="noopener" aria-label="TikTok">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                        <path d="M9 9v7a4 4 0 1 0 4-4" />
                                        <path d="M13 9V4c1.3 1.8 2.9 2.6 5 2.7" />
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="flex items-center gap-3">
                        <a href="<?= escape(url('market')) ?>" class="inline-flex h-11 items-center justify-center rounded-2xl bg-cyan-400 px-5 text-sm font-black text-slate-950 transition hover:-translate-y-0.5">تسوق الآن</a>
                        <?php if (function_exists('is_admin_logged_in') && is_admin_logged_in()): ?>
                            <a href="<?= escape(url('admin/account')) ?>" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white" aria-label="الملف الشخصي">
                                <span class="text-lg">👤</span>
                            </a>
                            <form method="post" action="<?= escape(url('admin/logout')) ?>" class="inline-flex">
                                <?= csrf_field() ?>
                                <button type="submit" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white" aria-label="تسجيل الخروج">
                                    <span class="text-lg">⎋</span>
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="<?= escape(url('admin/login')) ?>" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white" aria-label="تسجيل الدخول">
                                <span class="text-lg">👤</span>
                            </a>
                        <?php endif; ?>
                        <button type="button" class="theme-toggle inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white" data-theme-toggle aria-label="Toggle theme">
                            <span class="theme-toggle-icon">☾</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>
    <?php endif; ?>
    <?php
}

function render_public_footer(): void
{
    $normalizeSocialUrl = static function (?string $value): ?string {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (!preg_match('~^https?://~i', $value)) {
            $value = 'https://' . $value;
        }

        return preg_match('~^https?://~i', $value) ? $value : null;
    };
    $facebookUrl = $normalizeSocialUrl(get_setting('social_facebook', ''));
    $instagramUrl = $normalizeSocialUrl(get_setting('social_instagram', ''));
    $tiktokUrl = $normalizeSocialUrl(get_setting('social_tiktok', ''));
    $shopUrl = get_setting('shop_url', '');
    $socialClass = 'inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-white transition hover:-translate-y-0.5 hover:bg-white/10';
    ?>
    <footer class="border-t border-white/10 bg-slate-950/80">
        <div class="mx-auto flex max-w-7xl flex-col items-center gap-4 px-4 py-8 text-center sm:flex-row sm:justify-between sm:text-right">
            <div class="flex items-center gap-3 sm:gap-4">
                <img src="<?= escape(site_logo_url()) ?>" alt="<?= escape(APP_NAME) ?>" class="h-12 w-12 rounded-lg bg-white/5 p-1 sm:h-16 sm:w-16 lg:h-20 lg:w-20">
                <div>
                    <p class="text-sm font-bold text-white sm:text-base"><?= escape(APP_NAME) ?></p>
                    <p class="text-xs text-slate-400 sm:text-sm">واجهة التحميل الرسمية</p>
                </div>
            </div>
            <div class="flex flex-col items-center gap-3 text-sm text-slate-300 sm:items-end">
                <?php if ($facebookUrl || $instagramUrl || $tiktokUrl): ?>
                    <div class="flex items-center gap-2">
                        <?php if ($facebookUrl): ?>
                            <a href="<?= escape($facebookUrl) ?>" class="<?= $socialClass ?>" target="_blank" rel="noopener" aria-label="Facebook">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="M7 10v4h3v8h4v-8h3l1-4h-4V8a1 1 0 0 1 1-1h3V3h-3a5 5 0 0 0-5 5v2H7" />
                                </svg>
                            </a>
                        <?php endif; ?>
                        <?php if ($instagramUrl): ?>
                            <a href="<?= escape($instagramUrl) ?>" class="<?= $socialClass ?>" target="_blank" rel="noopener" aria-label="Instagram">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <rect x="3" y="3" width="18" height="18" rx="4" />
                                    <circle cx="12" cy="12" r="4" />
                                    <circle cx="17" cy="7" r="1" />
                                </svg>
                            </a>
                        <?php endif; ?>
                        <?php if ($tiktokUrl): ?>
                            <a href="<?= escape($tiktokUrl) ?>" class="<?= $socialClass ?>" target="_blank" rel="noopener" aria-label="TikTok">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="M9 9v7a4 4 0 1 0 4-4" />
                                    <path d="M13 9V4c1.3 1.8 2.9 2.6 5 2.7" />
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="flex flex-wrap items-center justify-center gap-3 sm:justify-end">
                    <a href="<?= escape(url('')) ?>" class="hover:text-white">الرئيسية</a>
                    <span class="text-white/10">•</span>
                    <a href="<?= escape(url('market')) ?>" class="text-cyan-400 hover:text-cyan-300 font-bold animate-pulse">تسوق الآن</a>
                    <span class="text-white/10">•</span>
                    <a href="<?= escape(url('download')) ?>" class="hover:text-white">تحميل التطبيق</a>
                    <span class="text-white/10">•</span>
                    <a href="<?= escape(url('about')) ?>" class="hover:text-white">عن التطبيق</a>
                    <span class="text-white/10">•</span>
                    <a href="<?= escape(url('privacy')) ?>" class="hover:text-white">سياسة الخصوصية</a>
                    <span class="text-white/10">•</span>
                    <a href="<?= escape(url('terms')) ?>" class="hover:text-white">الشروط والأحكام</a>
                </div>
            </div>
        </div>
        <div class="border-t border-white/10 py-4 text-center text-xs text-slate-400">
            © <?= date('Y') ?> <?= escape(APP_NAME) ?> - جميع الحقوق محفوظة
        </div>
    </footer>
    <script src="<?= asset('js/app.js') ?>"></script>
    </body>
    </html>
    <?php
}

function render_admin_header(string $title): void
{
    $title = $title !== '' ? $title . ' - ' . APP_NAME : APP_NAME;
    $admin = current_admin();
    $cspNonce = csp_nonce();
    ?>
    <!doctype html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?= escape(APP_DESCRIPTION) ?>">
        <meta name="robots" content="noindex, nofollow">
        <title><?= escape($title) ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?= asset('css/tailwind.css') ?>">
        <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
        <link rel="icon" href="<?= escape(site_favicon_url()) ?>">
        <link rel="apple-touch-icon" href="<?= escape(site_favicon_url()) ?>">
    </head>
    <body class="font-arabic bg-slate-100 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100 admin-layout" data-page="admin">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_right,_rgba(14,165,233,0.16),_transparent_28%),radial-gradient(circle_at_bottom_left,_rgba(15,118,110,0.18),_transparent_28%)] dark:bg-[radial-gradient(circle_at_top_right,_rgba(34,211,238,0.14),_transparent_28%),radial-gradient(circle_at_bottom_left,_rgba(15,118,110,0.18),_transparent_28%)]">
        <div class="admin-menu-overlay" data-admin-menu-overlay></div>
        <div class="admin-layout-shell mx-auto flex min-h-screen max-w-[1600px] flex-col lg:flex-row">
            <aside class="admin-sidebar border-b border-slate-200 bg-white/95 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-950/95 lg:sticky lg:top-0 lg:h-screen lg:w-80 lg:border-b-0 lg:border-l">
                <div class="admin-sidebar-scroll">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-200 pb-4 dark:border-slate-800">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <a href="<?= escape(url('')) ?>" class="flex items-center gap-3">
                                <img src="<?= escape(site_logo_url()) ?>" alt="<?= escape(APP_NAME) ?>" class="h-12 w-12 rounded-lg bg-white/5 p-1 sm:h-16 sm:w-16 lg:h-20 lg:w-20">
                            </a>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-cyan-500">Admin Panel</p>
                                <h1 class="mt-2 text-xl font-black text-slate-950 dark:text-white sm:text-2xl"><?= escape(APP_NAME) ?></h1>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white p-2 text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 lg:hidden" data-admin-menu-close aria-label="إغلاق القائمة">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                    <path d="M18 6L6 18" />
                                    <path d="M6 6l12 12" />
                                </svg>
                                <span class="sr-only">إغلاق</span>
                            </button>
                            <a href="<?= escape(url('admin/login')) ?>" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 bg-white">لوحة التحكم</a>
                            <button type="button" class="theme-toggle inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800" aria-label="Toggle dark mode" data-theme-toggle>
                                <span class="theme-toggle-icon">☾</span>
                            </button>
                        </div>
                    </div>
                    <div class="mt-5 rounded-3xl bg-slate-950 p-5 text-white shadow-glow dark:bg-slate-900">
                    <p class="text-sm text-slate-300">مرحبًا</p>
                    <p class="mt-1 text-xl font-bold"><?= escape($admin['full_name'] ?? 'Administrator') ?></p>
                    <p class="mt-2 text-xs text-slate-400"><?= escape($admin['username'] ?? '') ?></p>
                </div>
                    <nav class="admin-sidebar-nav mt-5 space-y-2">
                    <?php foreach (admin_nav_items() as $item): ?>
                        <?php if (($item['method'] ?? 'get') === 'post'): ?>
                            <form method="post" action="<?= escape($item['href']) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700 dark:text-slate-300 dark:hover:bg-slate-900 dark:hover:text-cyan-300">
                                    <span class="icon-box"><?= icon_svg($item['icon']) ?></span>
                                    <span><?= escape($item['label']) ?></span>
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="<?= escape($item['href']) ?>" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700 dark:text-slate-300 dark:hover:bg-slate-900 dark:hover:text-cyan-300">
                                <span class="icon-box"><?= icon_svg($item['icon']) ?></span>
                                <span><?= escape($item['label']) ?></span>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </nav>
                </div>
            </aside>
            <main class="admin-main flex-1 p-4 lg:p-8">
                <div class="mb-4 flex items-center justify-between rounded-2xl border border-slate-200 bg-white/90 px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm dark:border-slate-800 dark:bg-slate-950/90 dark:text-slate-200 lg:hidden">
                    <span>لوحة التحكم</span>
                    <button type="button" class="rounded-xl border border-slate-200 bg-white p-2 text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200" data-admin-menu-toggle aria-label="فتح القائمة">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <path d="M3 6h18" />
                            <path d="M3 12h18" />
                            <path d="M3 18h18" />
                        </svg>
                        <span class="sr-only">القائمة</span>
                    </button>
                </div>
    <?php
}

function render_admin_footer(): void
{
    ?>
            </main>
        </div>
    </div>
    <script src="<?= asset('js/admin.js') ?>"></script>
    </body>
    </html>
    <?php
}
