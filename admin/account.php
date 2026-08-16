<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$admin = current_admin();
if (!$admin) {
    redirect('admin/login');
}

$errors = [];

if (is_post()) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $errors[] = 'رمز الحماية غير صالح.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $fullName = trim((string) ($_POST['full_name'] ?? ''));
        $currentPassword = (string) ($_POST['current_password'] ?? '');
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        try {
            update_admin_profile(
                (int) $admin['id'],
                $username,
                $fullName,
                $currentPassword,
                $newPassword,
                $confirmPassword
            );
            flash('success', 'تم تحديث بيانات الحساب بنجاح.');
            redirect('admin/account');
        } catch (Throwable $exception) {
            $errors[] = $exception->getMessage();
        }
    }
}

render_admin_header('إعدادات الحساب');
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
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-600 dark:text-cyan-400">Account Settings</p>
            <h2 class="mt-2 text-3xl font-black">إعدادات الحساب</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">يمكنك تحديث اسم المستخدم والاسم الكامل وكلمة المرور.</p>
        </div>

        <form method="post" class="mt-8 space-y-6">
            <?= csrf_field() ?>

            <div class="grid gap-5 lg:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">اسم المستخدم</label>
                    <input type="text" name="username" value="<?= escape($_POST['username'] ?? (string) $admin['username']) ?>" class="input-surface w-full rounded-2xl px-4 py-3" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">الاسم الكامل</label>
                    <input type="text" name="full_name" value="<?= escape($_POST['full_name'] ?? (string) $admin['full_name']) ?>" class="input-surface w-full rounded-2xl px-4 py-3" required>
                </div>
            </div>

            <div class="grid gap-5 lg:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">كلمة المرور الحالية</label>
                    <input type="password" name="current_password" class="input-surface w-full rounded-2xl px-4 py-3" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">كلمة المرور الجديدة</label>
                    <input type="password" name="new_password" class="input-surface w-full rounded-2xl px-4 py-3">
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">تأكيد كلمة المرور الجديدة</label>
                <input type="password" name="confirm_password" class="input-surface w-full rounded-2xl px-4 py-3">
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">اتركها فارغة إذا لا تريد تغيير كلمة المرور.</p>
            </div>

            <button type="submit" class="rounded-2xl bg-slate-950 px-6 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800 dark:bg-cyan-500 dark:text-slate-950 dark:hover:bg-cyan-400">حفظ التغييرات</button>
        </form>
    </section>
</div>
<?php render_admin_footer(); ?>
