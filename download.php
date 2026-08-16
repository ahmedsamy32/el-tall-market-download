<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$latestApk = get_latest_version_by_platform('apk');
$latestIpa = get_latest_version_by_platform('ipa');
$versions = get_latest_versions();
$latestOverall = $versions[0] ?? null;
$latestRelease = $latestApk ?? $latestIpa;

render_public_header('تحميل التطبيق', 'حمل أحدث إصدار من تطبيق سوق التل لأجهزة Android و iOS مع معلومات النسخة.');
?>
<div class="bg-noise min-h-screen">
    <main class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <!-- Hero Title and Welcome -->
        <section class="mb-16 text-center text-white">
            <h1 class="text-5xl font-black leading-snug sm:text-6xl lg:text-7xl">سوق التل - حمل التطبيق الآن</h1>
            
            <div class="mx-auto mt-8 max-w-2xl space-y-4 text-lg leading-8 text-slate-300">
                <p>👋 مرحباً بك في سوق التل</p>
                <p>تطبيق سوق التل هو الحل الأمثل لشراء وبيع المنتجات بسهولة وأمان من خلال هاتفك المحمول.</p>
                <p>استمتع بتجربة سريعة وبسيطة لعرض المنتجات والتواصل مع البائعين.</p>
            </div>
        </section>

        <!-- Features and Banner in a single split container -->
        <?php 
        $downloadBannerUrl = site_download_banner_url();
        $downloadBannerLink = get_setting('site_download_banner_link', '');
        $bannerSrc = $downloadBannerUrl ?: asset('images/download_banner_default.png');
        ?>
        <div class="glass-card rounded-[2rem] border border-white/10 shadow-glow p-4 md:p-5 mb-16 overflow-hidden" style="min-height:650px;">
            <div class="download-split-grid">
                <!-- Right Side: Features List -->
                <div class="space-y-4 text-white" style="display:flex;flex-direction:column;justify-content:center;height:100%;">
                    <h2 class="text-2xl font-bold">مميزات التطبيق:</h2>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3 text-lg">
                            <span class="text-green-400">✔</span>
                            <span>سهولة الاستخدام</span>
                        </li>
                        <li class="flex items-center gap-3 text-lg">
                            <span class="text-green-400">✔</span>
                            <span>سرعة في التصفح</span>
                        </li>
                        <li class="flex items-center gap-3 text-lg">
                            <span class="text-green-400">✔</span>
                            <span>عرض منتجات متنوعة</span>
                        </li>
                        <li class="flex items-center gap-3 text-lg">
                            <span class="text-green-400">✔</span>
                            <span>تواصل مباشر مع البائع</span>
                        </li>
                        <li class="flex items-center gap-3 text-lg">
                            <span class="text-green-400">✔</span>
                            <span>تحديثات مستمرة لتحسين الأداء</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Left Side: Banner Image -->
                <div class="download-banner-wrapper" style="min-height:320px;padding:0.5rem;padding-bottom:0;">
                    <?php if ($downloadBannerLink !== ''): ?>
                        <a href="<?= escape($downloadBannerLink) ?>">
                    <?php else: ?>
                        <div>
                    <?php endif; ?>
                        
                        <img src="<?= escape($bannerSrc) ?>" alt="إعلان صفحة التحميل" style="max-height:600px;width:auto;object-fit:contain;" class="rounded-2xl shadow-md transition duration-500 hover:scale-[1.03]">
                        
                    <?php if ($downloadBannerLink !== ''): ?>
                        </a>
                    <?php else: ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Download buttons & details (Centered width) -->
        <div class="mx-auto max-w-4xl space-y-16">
            <!-- Large Download Buttons -->
            <section class="grid gap-4 sm:grid-cols-2">
                <a 
                    href="<?= $latestApk ? escape(url((string) $latestApk['file_path'])) : '#' ?>" 
                    class="group rounded-3xl bg-gradient-to-r from-blue-500 to-blue-600 px-8 py-6 text-center font-bold text-white transition hover:shadow-2xl hover:shadow-blue-500/50 <?= $latestApk ? '' : 'pointer-events-none opacity-40' ?>"
                >
                    <div class="text-3xl mb-2">📱</div>
                    <div>حمل نسخة Android</div>
                    <div class="text-sm opacity-90 mt-1">(APK)</div>
                </a>
                
                <a 
                    href="<?= $latestIpa ? escape(url((string) $latestIpa['file_path'])) : '#' ?>" 
                    class="group rounded-3xl bg-gradient-to-r from-slate-600 to-slate-700 px-8 py-6 text-center font-bold text-white transition hover:shadow-2xl hover:shadow-slate-600/50 <?= $latestIpa ? '' : 'pointer-events-none opacity-40' ?>"
                >
                    <div class="text-3xl mb-2">📱</div>
                    <div>حمل نسخة iPhone</div>
                    <div class="text-sm opacity-90 mt-1">(iOS)</div>
                </a>
            </section>

            <!-- Version Info -->
            <section class="space-y-4 text-white">
                <h2 class="text-2xl font-bold">معلومات النسخة:</h2>
                <div class="grid gap-4 sm:grid-cols-3 rounded-2xl bg-white/5 p-6 border border-white/10">
                    <div>
                        <p class="text-sm text-slate-400">الإصدار</p>
                        <p class="mt-2 text-2xl font-bold"><?= $latestRelease ? escape((string) $latestRelease['version']) : '—' ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">تاريخ التحديث</p>
                        <p class="mt-2 text-2xl font-bold">
                            <?= $latestRelease ? date('d/m/Y', strtotime((string) $latestRelease['created_at'])) : '—' ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">حجم التطبيق</p>
                        <p class="mt-2 text-2xl font-bold"><?= $latestRelease ? escape(format_bytes((int) $latestRelease['file_size'])) : '—' ?></p>
                    </div>
                </div>
            </section>

            <!-- Support Message -->
            <section class="text-center text-white" style="margin-top: 6rem;">
                <div class="rounded-2xl border border-yellow-400/30 bg-yellow-400/10 p-6">
                    <div class="text-2xl mb-3">⚠</div>
                    <p class="text-lg">لو عندك مشكلة في التحميل أو التثبيت، <strong>تواصل معنا</strong></p>
                    <p class="mt-2 text-slate-300">نحن هنا للمساعدة 😊</p>
                </div>
            </section>
        </div>
    </main>
</div>
<?php render_public_footer(); ?>
