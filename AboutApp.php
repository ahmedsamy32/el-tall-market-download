<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$contactEmail = get_setting('contact_email', 'support@elltall.com');
$contactPhone = get_setting('contact_phone', '+20 123 456 7890');
$contactWebsite = get_setting('contact_website', 'www.elltall.com');
$contactAddress = get_setting('contact_address', 'التل الكبير - مصر');
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
$contactEmailUrl = 'mailto:' . $contactEmail;
$contactPhoneUrl = 'tel:' . preg_replace('~[^0-9+]~', '', $contactPhone);
$contactWebsiteUrl = preg_match('~^https?://~i', $contactWebsite) ? $contactWebsite : 'https://' . $contactWebsite;

render_public_header('عن التطبيق', 'تعرف على تطبيق سوق التل ومميزاته وطرق التواصل الرسمية.');
?>
<div class="bg-noise min-h-screen">
    <main class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <section class="space-y-8">
            <div class="text-center text-white">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-300">About App</p>
                <h1 class="mt-3 text-4xl font-black sm:text-5xl">عن التطبيق</h1>
                <p class="mt-4 text-base text-slate-300"><?= escape(APP_NAME) ?> / <?= escape(APP_NAME_EN) ?></p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-cyan-500/20 text-cyan-200">ℹ</span>
                    <h2 class="text-xl font-bold text-white">عن التطبيق</h2>
                </div>
                <p class="mt-4 leading-8">سوق التل هو تطبيقك الامثل للتسوق الالكتروني، يوفر لك تجربة تسوق سهلة وممتعة مع مجموعة واسعة من المنتجات من افضل المتاجر والتجار المحليين. نقدم لك خدمة توصيل سريعة وامنة حتى باب منزلك.</p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-500/20 text-amber-200">★</span>
                    <h2 class="text-xl font-bold text-white">مميزات التطبيق</h2>
                </div>
                <div class="mt-4 space-y-4">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="font-bold text-white">تسوق متنوع</p>
                        <p class="mt-1 text-sm text-slate-300">الاف المنتجات من مختلف الفئات والمتاجر.</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="font-bold text-white">توصيل سريع</p>
                        <p class="mt-1 text-sm text-slate-300">خدمة توصيل سريعة وموثوقة لجميع المناطق.</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="font-bold text-white">دفع امن</p>
                        <p class="mt-1 text-sm text-slate-300">طرق دفع متعددة وامنة لحماية معاملاتك.</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="font-bold text-white">عروض مستمرة</p>
                        <p class="mt-1 text-sm text-slate-300">خصومات وعروض حصرية على مدار السنة.</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="font-bold text-white">دعم فني</p>
                        <p class="mt-1 text-sm text-slate-300">فريق دعم متاح لمساعدتك في اي وقت.</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-500/20 text-emerald-200">⚖</span>
                    <h2 class="text-xl font-bold text-white">القانونية</h2>
                </div>
                <div class="mt-4 space-y-2 text-slate-200">
                    <a href="<?= escape(url('privacy')) ?>" class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 hover:text-white">
                        <span>سياسة الخصوصية</span>
                        <span class="text-xs text-slate-400">›</span>
                    </a>
                    <a href="<?= escape(url('terms')) ?>" class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 hover:text-white">
                        <span>الشروط والاحكام</span>
                        <span class="text-xs text-slate-400">›</span>
                    </a>
                </div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-500/20 text-blue-200">☎</span>
                    <h2 class="text-xl font-bold text-white">تواصل معنا</h2>
                </div>
                <div class="mt-4 space-y-3 text-slate-200">
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                        <p class="text-xs text-slate-400">البريد الالكتروني</p>
                        <a href="<?= escape($contactEmailUrl) ?>" class="text-sm text-slate-200 hover:text-white">
                            <?= escape($contactEmail) ?>
                        </a>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                        <p class="text-xs text-slate-400">الهاتف</p>
                        <a href="<?= escape($contactPhoneUrl) ?>" class="text-sm text-slate-200 hover:text-white">
                            <?= escape($contactPhone) ?>
                        </a>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                        <p class="text-xs text-slate-400">الموقع الالكتروني</p>
                        <a href="<?= escape($contactWebsiteUrl) ?>" class="text-sm text-slate-200 hover:text-white" target="_blank" rel="noopener">
                            <?= escape($contactWebsite) ?>
                        </a>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                        <p class="text-xs text-slate-400">العنوان</p>
                        <p class="text-sm"><?= escape($contactAddress) ?></p>
                    </div>
                    <?php if ($facebookUrl || $instagramUrl || $tiktokUrl): ?>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                            <p class="text-xs text-slate-400">تابعنا</p>
                            <div class="mt-2 flex items-center gap-2">
                                <?php if ($facebookUrl): ?>
                                    <a href="<?= escape($facebookUrl) ?>" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white transition hover:-translate-y-0.5 hover:bg-white/10" target="_blank" rel="noopener" aria-label="Facebook">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                            <path d="M7 10v4h3v8h4v-8h3l1-4h-4V8a1 1 0 0 1 1-1h3V3h-3a5 5 0 0 0-5 5v2H7" />
                                        </svg>
                                    </a>
                                <?php endif; ?>
                                <?php if ($instagramUrl): ?>
                                    <a href="<?= escape($instagramUrl) ?>" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white transition hover:-translate-y-0.5 hover:bg-white/10" target="_blank" rel="noopener" aria-label="Instagram">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                            <rect x="3" y="3" width="18" height="18" rx="4" />
                                            <circle cx="12" cy="12" r="4" />
                                            <circle cx="17" cy="7" r="1" />
                                        </svg>
                                    </a>
                                <?php endif; ?>
                                <?php if ($tiktokUrl): ?>
                                    <a href="<?= escape($tiktokUrl) ?>" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white transition hover:-translate-y-0.5 hover:bg-white/10" target="_blank" rel="noopener" aria-label="TikTok">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                            <path d="M9 9v7a4 4 0 1 0 4-4" />
                                            <path d="M13 9V4c1.3 1.8 2.9 2.6 5 2.7" />
                                        </svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="text-center text-sm text-slate-400">
                <p>© 2026 سوق التل - جميع الحقوق محفوظة</p>
                <p class="mt-1">صنع بـ ❤ في مصر</p>
            </div>
        </section>
    </main>
</div>
<?php render_public_footer(); ?>
