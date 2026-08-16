<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$errors = [];
$contactEmail = get_setting('contact_email', 'support@elltall.com');
$contactPhone = get_setting('contact_phone', '+20 123 456 7890');
$contactWebsite = get_setting('contact_website', 'www.elltall.com');
$contactAddress = get_setting('contact_address', 'التل الكبير - مصر');
$socialFacebook = get_setting('social_facebook', '');
$socialInstagram = get_setting('social_instagram', '');
$socialTiktok = get_setting('social_tiktok', '');
$shopUrl = get_setting('shop_url', '');

if (is_post()) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $errors[] = 'رمز الحماية غير صالح.';
    } else {
        $contactEmail = trim((string) ($_POST['contact_email'] ?? ''));
        $contactPhone = trim((string) ($_POST['contact_phone'] ?? ''));
        $contactWebsite = trim((string) ($_POST['contact_website'] ?? ''));
        $contactAddress = trim((string) ($_POST['contact_address'] ?? ''));
        $socialFacebook = trim((string) ($_POST['social_facebook'] ?? ''));
        $socialInstagram = trim((string) ($_POST['social_instagram'] ?? ''));
        $socialTiktok = trim((string) ($_POST['social_tiktok'] ?? ''));
        $shopUrl = trim((string) ($_POST['shop_url'] ?? ''));

        if ($contactEmail === '' || !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'الرجاء إدخال بريد إلكتروني صحيح.';
        }

        if ($contactPhone === '') {
            $errors[] = 'الرجاء إدخال رقم الهاتف.';
        }

        if ($contactWebsite === '') {
            $errors[] = 'الرجاء إدخال الموقع الإلكتروني.';
        }

        if ($contactAddress === '') {
            $errors[] = 'الرجاء إدخال العنوان.';
        }

        $normalizedFacebook = $socialFacebook !== '' && !preg_match('~^https?://~i', $socialFacebook)
            ? 'https://' . $socialFacebook
            : $socialFacebook;
        $normalizedInstagram = $socialInstagram !== '' && !preg_match('~^https?://~i', $socialInstagram)
            ? 'https://' . $socialInstagram
            : $socialInstagram;
        $normalizedTiktok = $socialTiktok !== '' && !preg_match('~^https?://~i', $socialTiktok)
            ? 'https://' . $socialTiktok
            : $socialTiktok;
        $normalizedShopUrl = $shopUrl !== '' && !preg_match('~^https?://~i', $shopUrl)
            ? 'https://' . $shopUrl
            : $shopUrl;

        if ($socialFacebook !== '' && !filter_var($normalizedFacebook, FILTER_VALIDATE_URL)) {
            $errors[] = 'رابط فيسبوك غير صحيح.';
        }

        if ($socialInstagram !== '' && !filter_var($normalizedInstagram, FILTER_VALIDATE_URL)) {
            $errors[] = 'رابط إنستجرام غير صحيح.';
        }

        if ($socialTiktok !== '' && !filter_var($normalizedTiktok, FILTER_VALIDATE_URL)) {
            $errors[] = 'رابط تيك توك غير صحيح.';
        }

        if ($shopUrl !== '' && !filter_var($normalizedShopUrl, FILTER_VALIDATE_URL)) {
            $errors[] = 'رابط تسوق الآن غير صحيح.';
        }

        if (empty($errors)) {
            set_setting('contact_email', $contactEmail);
            set_setting('contact_phone', $contactPhone);
            set_setting('contact_website', $contactWebsite);
            set_setting('contact_address', $contactAddress);
            set_setting('social_facebook', $normalizedFacebook);
            set_setting('social_instagram', $normalizedInstagram);
            set_setting('social_tiktok', $normalizedTiktok);
            set_setting('shop_url', $normalizedShopUrl);

            flash('success', 'تم تحديث بيانات التواصل بنجاح.');
            redirect('admin/contact');
        }
    }
}

render_admin_header('بيانات التواصل');
?>
<div class="space-y-6">
    <?php if ($message = flash('success')): ?>
        <div data-flash-message="<?= escape($message) ?>" data-flash-type="success"></div>
    <?php endif; ?>
    <?php if ($message = flash('error')): ?>
        <div data-flash-message="<?= escape($message) ?>" data-flash-type="error"></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700 dark:border-red-900/60 dark:bg-red-950/60 dark:text-red-200">
            <?php foreach ($errors as $error): ?>
                <p>❌ <?= escape($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <section class="glass-card rounded-[2rem] p-6 dark:bg-slate-900/80">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-600 dark:text-cyan-400">Contact Info</p>
            <h2 class="mt-2 text-3xl font-black">بيانات التواصل</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">يمكنك تعديل بيانات التواصل وروابط السوشيال التي تظهر في الهيدر وصفحات "عن التطبيق" و"الخصوصية" و"الشروط".</p>
        </div>

        <form method="post" class="mt-8 space-y-6">
            <?= csrf_field() ?>

            <div class="grid gap-5 lg:grid-cols-4">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">البريد الإلكتروني</label>
                    <input type="email" name="contact_email" value="<?= escape($contactEmail) ?>" class="input-surface w-full rounded-2xl px-4 py-3" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">الهاتف</label>
                    <input type="text" name="contact_phone" value="<?= escape($contactPhone) ?>" class="input-surface w-full rounded-2xl px-4 py-3" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">الموقع الإلكتروني</label>
                    <input type="text" name="contact_website" value="<?= escape($contactWebsite) ?>" class="input-surface w-full rounded-2xl px-4 py-3" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">العنوان</label>
                    <input type="text" name="contact_address" value="<?= escape($contactAddress) ?>" class="input-surface w-full rounded-2xl px-4 py-3" required>
                </div>
            </div>

            <div class="grid gap-5 lg:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">رابط فيسبوك</label>
                    <input type="text" name="social_facebook" value="<?= escape($socialFacebook) ?>" class="input-surface w-full rounded-2xl px-4 py-3" placeholder="https://facebook.com/...">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">رابط إنستجرام</label>
                    <input type="text" name="social_instagram" value="<?= escape($socialInstagram) ?>" class="input-surface w-full rounded-2xl px-4 py-3" placeholder="https://instagram.com/...">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">رابط تيك توك</label>
                    <input type="text" name="social_tiktok" value="<?= escape($socialTiktok) ?>" class="input-surface w-full rounded-2xl px-4 py-3" placeholder="https://tiktok.com/@...">
                </div>
            </div>

            <div class="grid gap-5 lg:grid-cols-1">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">رابط "تسوق الآن" (موقع خارجي)</label>
                    <input type="text" name="shop_url" value="<?= escape($shopUrl) ?>" class="input-surface w-full rounded-2xl px-4 py-3" placeholder="https://example.com/...">
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                <p>سيتم عرض البيانات في صفحات الموقع، وروابط السوشيال تظهر في الهيدر والفوتر.</p>
            </div>

            <button type="submit" class="rounded-2xl bg-slate-950 px-6 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800 dark:bg-cyan-500 dark:text-slate-950 dark:hover:bg-cyan-400">حفظ التغييرات</button>
        </form>
    </section>
</div>
<?php render_admin_footer(); ?>
