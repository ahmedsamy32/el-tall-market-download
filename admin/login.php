<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

if (is_admin_logged_in()) {
    redirect('admin');
}

$error = null;
$rateKey = rate_limit_key('login', client_ip());
$retryAfter = rate_limit_retry_after($rateKey);

if ($retryAfter > 0) {
    $error = 'تم حظر تسجيل الدخول مؤقتًا. حاول مرة أخرى بعد ' . (string) ceil($retryAfter / 60) . ' دقيقة.';
}

if ($retryAfter <= 0 && is_post()) {
    $rateLimitEligible = false;
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'رمز الحماية غير صالح.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $error = 'الرجاء إدخال اسم المستخدم وكلمة المرور.';
        } elseif (login_admin($username, $password)) {
            flash('success', 'تم تسجيل الدخول بنجاح.');
            rate_limit_clear($rateKey);
            redirect('admin');
        } else {
            $error = 'بيانات الدخول غير صحيحة.';
            $rateLimitEligible = true;
        }
    }

    if ($rateLimitEligible && $error !== null) {
        rate_limit_register_failure($rateKey, 5, 600, 900);
    }
}

render_public_header('تسجيل دخول الإدارة', APP_DESCRIPTION);
?>
<div class="bg-noise min-h-screen px-4 py-10">
    <div class="mx-auto flex min-h-[calc(100vh-8rem)] max-w-2xl items-center justify-center">
        <div class="w-full rounded-[2rem] bg-white p-8 shadow-2xl dark:bg-slate-800 sm:p-10">
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-black text-slate-950 dark:text-white">لوحة التحكم</h1>
                <p class="mt-3 text-lg text-slate-600 dark:text-slate-200">رفع وتحديث تطبيق <?= escape(APP_NAME) ?> بسهولة</p>
            </div>

            <?php if ($message = flash('success')): ?>
                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 dark:border-green-900/60 dark:bg-green-950/60 dark:text-green-200">
                    ✅ <?= escape($message) ?>
                </div>
            <?php endif; ?>
            <?php if ($message = flash('error')): ?>
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 dark:border-red-400 dark:bg-red-900/30 dark:text-red-100">
                    ❌ <?= escape($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($error !== null): ?>
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 dark:border-red-400 dark:bg-red-900/30 dark:text-red-100">
                    ❌ <?= escape($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-5">
                <?= csrf_field() ?>
                
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-100">اسم المستخدم</label>
                    <input type="text" name="username" value="<?= escape($_POST['username'] ?? APP_DEFAULT_ADMIN_USERNAME) ?>" class="w-full rounded-2xl border-2 border-slate-300 bg-white px-4 py-3 text-base text-slate-900 placeholder-slate-500 focus:border-cyan-500 focus:outline-none dark:border-slate-500 dark:bg-slate-700 dark:text-white dark:placeholder-slate-300 dark:focus:border-cyan-400" autocomplete="username" required>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-100">كلمة المرور</label>
                    <input type="password" name="password" class="w-full rounded-2xl border-2 border-slate-300 bg-white px-4 py-3 text-base text-slate-900 placeholder-slate-500 focus:border-cyan-500 focus:outline-none dark:border-slate-500 dark:bg-slate-700 dark:text-white dark:placeholder-slate-300 dark:focus:border-cyan-400" autocomplete="current-password" required>
                </div>

                <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 px-6 py-3 text-lg font-bold text-white transition hover:shadow-lg hover:shadow-cyan-500/50 dark:from-cyan-600 dark:to-blue-700 dark:hover:shadow-cyan-600/50">
                    🔐 تسجيل الدخول
                </button>
            </form>

            <div class="mt-8 border-t border-slate-300 pt-6 text-center dark:border-slate-600">
                <p class="text-sm text-slate-600 dark:text-slate-300">
                    © 2026 <?= escape(APP_NAME) ?> - جميع الحقوق محفوظة
                </p>
            </div>
        </div>
    </div>
</div>
<?php render_public_footer(); ?>
