<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
$version = [
    'id' => 0,
    'version' => '',
    'update_notes' => '',
    'file_name' => '',
    'file_path' => '',
    'file_size' => 0,
    'platform' => 'apk',
    'is_latest' => 1,
];

$uploadMaxRaw = (string) ini_get('upload_max_filesize');
$postMaxRaw = (string) ini_get('post_max_size');
$uploadMaxLabel = format_ini_limit($uploadMaxRaw);
$postMaxLabel = format_ini_limit($postMaxRaw);
$effectiveBytes = effective_upload_limit_bytes();
$effectiveLabel = $effectiveBytes > 0 ? format_bytes($effectiveBytes) : 'غير محدود';
$memoryLimitLabel = format_ini_limit((string) ini_get('memory_limit'));
$maxFileUploads = (int) ini_get('max_file_uploads');
$maxInputTime = (int) ini_get('max_input_time');
$maxExecutionTime = (int) ini_get('max_execution_time');
$apkFiles = list_upload_files('apk');
$ipaFiles = list_upload_files('ipa');

if ($id > 0) {
    $existing = get_version_by_id($id);
    if (!$existing) {
        flash('error', 'الإصدار المطلوب غير موجود.');
        redirect('admin');
    }
    $version = array_merge($version, $existing);
}

$pageTitle = $id > 0 ? 'تعديل إصدار' : 'إضافة إصدار';
render_admin_header($pageTitle);
?>
<div class="space-y-6">
    <?php if ($message = flash('success')): ?>
        <div data-flash-message="<?= escape($message) ?>" data-flash-type="success"></div>
    <?php endif; ?>
    <?php if ($message = flash('error')): ?>
        <div data-flash-message="<?= escape($message) ?>" data-flash-type="error"></div>
    <?php endif; ?>

    <section class="glass-card rounded-[2rem] p-6 dark:bg-slate-900/80">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-600 dark:text-cyan-400">Release Manager</p>
                <h2 class="mt-2 text-3xl font-black"><?= escape($pageTitle) ?></h2>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">رفع آمن مع progress bar، والتحقق من الامتداد والنوع والحجم من جهة الخادم.</p>
            </div>
            <a href="<?= escape(url('admin')) ?>" class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200">عودة للوحة التحكم</a>
        </div>
        <form method="post" action="<?= escape(url('upload')) ?>" enctype="multipart/form-data" class="mt-8 space-y-6" data-xhr-form data-chunk-endpoint="<?= escape(url('admin/upload-chunk')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= (int) $version['id'] ?>">
            <div class="grid gap-5 lg:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Version Number</label>
                    <input type="text" name="version" value="<?= escape((string) $version['version']) ?>" placeholder="مثال: 1.2.4" class="input-surface w-full rounded-2xl px-4 py-3" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Platform</label>
                    <select name="platform" class="select-surface w-full rounded-2xl px-4 py-3" required>
                        <option value="apk" <?= (string) $version['platform'] === 'apk' ? 'selected' : '' ?>>APK / Android</option>
                        <option value="ipa" <?= (string) $version['platform'] === 'ipa' ? 'selected' : '' ?>>IPA / iPhone</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Update Notes</label>
                <textarea name="update_notes" rows="7" class="textarea-surface w-full rounded-2xl px-4 py-3 leading-8" placeholder="اكتب سجل التحديثات هنا" required><?= escape((string) $version['update_notes']) ?></textarea>
            </div>
            <div class="grid gap-5 lg:grid-cols-[1.1fr_0.9fr]">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">رفع ملف التطبيق <?= $id > 0 ? '(اختياري عند التعديل)' : '' ?></label>
                    <input type="file" name="app_file" accept=".apk,.ipa" class="input-surface w-full rounded-2xl px-4 py-3 text-sm">
                    <p class="mt-2 text-xs leading-6 text-slate-500 dark:text-slate-400">الحد الأقصى للرفع: <?= (int) APP_MAX_UPLOAD_MB ?> MB. الامتدادات المسموحة: APK و IPA فقط.</p>
                    <label class="mt-4 block text-sm font-semibold text-slate-700 dark:text-slate-300">أو اختر ملفًا من السيرفر:</label>
                    <select name="existing_file_path" class="input-surface mt-2 w-full rounded-2xl px-4 py-3 text-sm">
                        <option value="">— اختيار اختياري —</option>
                        <?php if (!$apkFiles && !$ipaFiles): ?>
                            <option value="">لا توجد ملفات داخل uploads.</option>
                        <?php endif; ?>
                        <?php if ($apkFiles): ?>
                            <optgroup label="APK">
                                <?php foreach ($apkFiles as $file): ?>
                                    <option value="<?= escape($file['relative_path']) ?>"><?= escape($file['label']) ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                        <?php if ($ipaFiles): ?>
                            <optgroup label="IPA">
                                <?php foreach ($ipaFiles as $file): ?>
                                    <option value="<?= escape($file['relative_path']) ?>"><?= escape($file['label']) ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                    </select>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">لو اخترت ملفًا من السيرفر سيتم تجاهل رفع الملف.</p>
                    <?php if (!empty($version['file_name'])): ?>
                        <p class="mt-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300">الملف الحالي: <span class="font-bold"><?= escape((string) $version['file_name']) ?></span></p>
                    <?php endif; ?>
                    <div class="mt-4 rounded-3xl border border-dashed border-slate-300 bg-white p-4 text-xs leading-6 text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                        <p class="text-sm font-bold text-slate-900 dark:text-white">حدود الرفع في السيرفر</p>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-800 dark:bg-slate-950">
                                <p class="text-[11px] text-slate-500">upload_max_filesize</p>
                                <p class="text-sm font-semibold"><?= escape($uploadMaxLabel) ?></p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-800 dark:bg-slate-950">
                                <p class="text-[11px] text-slate-500">post_max_size</p>
                                <p class="text-sm font-semibold"><?= escape($postMaxLabel) ?></p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-800 dark:bg-slate-950">
                                <p class="text-[11px] text-slate-500">الحد الفعلي</p>
                                <p class="text-sm font-semibold"><?= escape($effectiveLabel) ?></p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-800 dark:bg-slate-950">
                                <p class="text-[11px] text-slate-500">حد التطبيق</p>
                                <p class="text-sm font-semibold"><?= (int) APP_MAX_UPLOAD_MB ?> MB</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-800 dark:bg-slate-950">
                                <p class="text-[11px] text-slate-500">memory_limit</p>
                                <p class="text-sm font-semibold"><?= escape($memoryLimitLabel) ?></p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-800 dark:bg-slate-950">
                                <p class="text-[11px] text-slate-500">max_file_uploads</p>
                                <p class="text-sm font-semibold"><?= (int) $maxFileUploads ?></p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-800 dark:bg-slate-950">
                                <p class="text-[11px] text-slate-500">max_input_time</p>
                                <p class="text-sm font-semibold"><?= (int) $maxInputTime ?>s</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-800 dark:bg-slate-950">
                                <p class="text-[11px] text-slate-500">max_execution_time</p>
                                <p class="text-sm font-semibold"><?= (int) $maxExecutionTime ?>s</p>
                            </div>
                        </div>
                        <p class="mt-3 text-[11px] text-slate-500">الحد الفعلي للرفع هو الأصغر بين upload_max_filesize و post_max_size.</p>
                    </div>
                </div>
                <div class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950">
                    <label class="flex cursor-pointer items-center justify-between gap-4 rounded-2xl bg-white px-4 py-3 dark:bg-slate-900">
                        <span>
                            <span class="block text-sm font-bold">تعيين كآخر إصدار رسمي</span>
                            <span class="block text-xs text-slate-500 dark:text-slate-400">سيتم تحديث هذا الإصدار كافتراضي في صفحة التحميل.</span>
                        </span>
                        <input type="checkbox" name="is_latest" value="1" class="h-5 w-5 rounded border-slate-300 text-cyan-500" <?= (int) $version['is_latest'] === 1 ? 'checked' : '' ?>>
                    </label>
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-4 text-sm leading-7 text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                        <p class="font-bold text-slate-900 dark:text-white">ملاحظات سريعة</p>
                        <p class="mt-2">عند تغيير المنصة، ارفع ملفًا جديدًا بنفس الامتداد حتى يظل التحقق متوافقًا مع نوع الحزمة.</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950">
                <div class="mb-3 flex items-center justify-between text-sm font-semibold text-slate-600 dark:text-slate-300">
                    <span>Upload Progress</span>
                    <span data-progress-value>0%</span>
                </div>
                <div class="h-3 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                    <div data-progress-bar class="upload-progress h-full rounded-full bg-gradient-to-r from-cyan-500 to-emerald-500"></div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="rounded-2xl bg-slate-950 px-6 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800 dark:bg-cyan-500 dark:text-slate-950 dark:hover:bg-cyan-400"><?= $id > 0 ? 'حفظ التعديلات' : 'رفع الإصدار' ?></button>
                <a href="<?= escape(url('admin')) ?>" class="rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200">إلغاء</a>
            </div>
        </form>
    </section>
</div>
<?php render_admin_footer(); ?>
