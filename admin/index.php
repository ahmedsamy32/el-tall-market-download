<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$versions = get_latest_versions();
$totalVersions = count($versions);
$totalApk = count(array_filter($versions, fn ($version) => (string) $version['platform'] === 'apk'));
$totalIpa = count(array_filter($versions, fn ($version) => (string) $version['platform'] === 'ipa'));
$latestApk = get_latest_version_by_platform('apk');
$latestIpa = get_latest_version_by_platform('ipa');
$uploadMax = ini_get('upload_max_filesize') ?: 'غير معروف';
$postMax = ini_get('post_max_size') ?: 'غير معروف';
$maxFileUploads = ini_get('max_file_uploads') ?: 'غير معروف';
$maxInputTime = ini_get('max_input_time') ?: 'غير معروف';
$maxExecution = ini_get('max_execution_time') ?: 'غير معروف';
$memoryLimit = ini_get('memory_limit') ?: 'غير معروف';
$apkFiles = list_upload_files('apk');
$ipaFiles = list_upload_files('ipa');

render_admin_header('لوحة التحكم - ' . APP_NAME);
?>
<div class="min-h-screen bg-noise px-4 py-8">
    <div class="mx-auto max-w-4xl">
        <!-- Page Title and Description -->
        <section class="mb-12 text-center">
            <h1 class="text-5xl font-black text-slate-950 dark:text-white">لوحة التحكم - <?= escape(APP_NAME) ?></h1>
            <p class="mt-4 text-lg text-slate-600 dark:text-slate-300">من خلال هذه الصفحة يمكنك رفع وتحديث تطبيق <?= escape(APP_NAME) ?> بسهولة.</p>
        </section>

        <section class="mb-8 rounded-3xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-bold text-slate-950 dark:text-white">حدود السيرفر الحالية</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">القيم التالية مأخوذة من إعدادات PHP في السيرفر.</p>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                    <span>upload_max_filesize</span>
                    <span class="font-bold text-slate-900 dark:text-white"><?= escape($uploadMax) ?></span>
                </div>
                <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                    <span>post_max_size</span>
                    <span class="font-bold text-slate-900 dark:text-white"><?= escape($postMax) ?></span>
                </div>
                <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                    <span>max_file_uploads</span>
                    <span class="font-bold text-slate-900 dark:text-white"><?= escape($maxFileUploads) ?></span>
                </div>
                <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                    <span>max_input_time</span>
                    <span class="font-bold text-slate-900 dark:text-white"><?= escape($maxInputTime) ?></span>
                </div>
                <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                    <span>max_execution_time</span>
                    <span class="font-bold text-slate-900 dark:text-white"><?= escape($maxExecution) ?></span>
                </div>
                <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                    <span>memory_limit</span>
                    <span class="font-bold text-slate-900 dark:text-white"><?= escape($memoryLimit) ?></span>
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500 dark:text-slate-400">لو `upload_max_filesize` أو `post_max_size` أقل من 110MB فلن يكتمل رفع الملف.</p>
        </section>

        <!-- Success/Error Messages -->
        <?php if ($message = flash('success')): ?>
            <div class="mb-8 rounded-2xl border border-green-200 bg-green-50 px-6 py-4 text-green-700 dark:border-green-900/60 dark:bg-green-950/60 dark:text-green-200">
                <p class="text-lg font-bold">✅ <?= escape($message) ?></p>
            </div>
        <?php endif; ?>
        <?php if ($message = flash('error')): ?>
            <div class="mb-8 rounded-2xl border border-red-200 bg-red-50 px-6 py-4 text-red-700 dark:border-red-900/60 dark:bg-red-950/60 dark:text-red-200">
                <p class="text-lg font-bold">❌ <?= escape($message) ?></p>
            </div>
        <?php endif; ?>

        <!-- Upload Android Section -->
        <section class="mb-10 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900">
            <h2 class="mb-6 text-2xl font-bold text-slate-950 dark:text-white">✅ رفع نسخة Android</h2>
            <p class="mb-6 text-slate-600 dark:text-slate-300">رفع تطبيق Android (APK)</p>
            
            <form method="post" action="<?= escape(url('upload')) ?>" enctype="multipart/form-data" class="space-y-5" data-xhr-form data-chunk-endpoint="<?= escape(url('admin/upload-chunk')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="platform" value="apk">

                <div>
                    <label class="mb-3 block text-sm font-bold text-slate-700 dark:text-slate-300">اختار الملف:</label>
                    <input type="file" name="app_file" accept=".apk" class="w-full rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-6 file:rounded-full file:border-0 file:bg-cyan-500 file:px-4 file:py-2 file:font-bold file:text-white dark:border-slate-700 dark:bg-slate-950">
                    <div class="mt-2 rounded-2xl bg-blue-50 p-4 text-sm text-blue-700 dark:bg-blue-950/30 dark:text-blue-300">
                        📱 Upload APK
                    </div>
                    <label class="mt-4 block text-sm font-bold text-slate-700 dark:text-slate-300">أو اختر ملفًا من السيرفر:</label>
                    <select name="existing_file_path" class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        <option value="">— اختيار اختياري —</option>
                        <?php foreach ($apkFiles as $file): ?>
                            <option value="<?= escape($file['relative_path']) ?>"><?= escape($file['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">لو اخترت ملفًا من السيرفر سيتم تجاهل رفع الملف.</p>
                </div>

                <div>
                    <label class="mb-3 block text-sm font-bold text-slate-700 dark:text-slate-300">رقم الإصدار:</label>
                    <input type="text" name="version" placeholder="مثال: 1.0.0" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-cyan-500 focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                </div>

                <div>
                    <label class="mb-3 block text-sm font-bold text-slate-700 dark:text-slate-300">ملاحظات التحديث:</label>
                    <textarea name="update_notes" rows="4" placeholder="اكتب ملاحظات التحديث هنا..." class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-cyan-500 focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                </div>

                <label class="flex items-center gap-3 rounded-2xl border border-slate-300 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-950">
                    <input type="checkbox" name="is_latest" value="1" class="h-5 w-5 rounded border-slate-300 text-cyan-500 dark:border-slate-700">
                    <span class="font-bold text-slate-700 dark:text-slate-300">تعيين كآخر إصدار</span>
                </label>

                <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-3 font-bold text-white transition hover:shadow-lg hover:shadow-blue-500/50">
                    📱 رفع التحديث
                </button>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300">
                    <div class="mb-3 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span>تحميل نسخة Android</span>
                        <span data-progress-value>0%</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                        <div data-progress-bar class="upload-progress h-full rounded-full bg-gradient-to-r from-cyan-500 to-emerald-500"></div>
                    </div>
                </div>
            </form>
        </section>

        <!-- Upload iOS Section -->
        <section class="mb-10 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900">
            <h2 class="mb-6 text-2xl font-bold text-slate-950 dark:text-white">✅ رفع نسخة iOS</h2>
            <p class="mb-6 text-slate-600 dark:text-slate-300">رفع تطبيق iOS</p>
            
            <form method="post" action="<?= escape(url('upload')) ?>" enctype="multipart/form-data" class="space-y-5" data-xhr-form data-chunk-endpoint="<?= escape(url('admin/upload-chunk')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="platform" value="ipa">

                <div>
                    <label class="mb-3 block text-sm font-bold text-slate-700 dark:text-slate-300">اختار الملف:</label>
                    <input type="file" name="app_file" accept=".ipa" class="w-full rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-6 file:rounded-full file:border-0 file:bg-slate-600 file:px-4 file:py-2 file:font-bold file:text-white dark:border-slate-700 dark:bg-slate-950">
                    <div class="mt-2 rounded-2xl bg-slate-50 p-4 text-sm text-slate-700 dark:bg-slate-950 dark:text-slate-300">
                        📱 Upload iOS
                    </div>
                    <label class="mt-4 block text-sm font-bold text-slate-700 dark:text-slate-300">أو اختر ملفًا من السيرفر:</label>
                    <select name="existing_file_path" class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        <option value="">— اختيار اختياري —</option>
                        <?php foreach ($ipaFiles as $file): ?>
                            <option value="<?= escape($file['relative_path']) ?>"><?= escape($file['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">لو اخترت ملفًا من السيرفر سيتم تجاهل رفع الملف.</p>
                </div>

                <div>
                    <label class="mb-3 block text-sm font-bold text-slate-700 dark:text-slate-300">رقم الإصدار:</label>
                    <input type="text" name="version" placeholder="مثال: 1.0.0" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-cyan-500 focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                </div>

                <div>
                    <label class="mb-3 block text-sm font-bold text-slate-700 dark:text-slate-300">ملاحظات التحديث:</label>
                    <textarea name="update_notes" rows="4" placeholder="اكتب ملاحظات التحديث هنا..." class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-cyan-500 focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                </div>

                <label class="flex items-center gap-3 rounded-2xl border border-slate-300 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-950">
                    <input type="checkbox" name="is_latest" value="1" class="h-5 w-5 rounded border-slate-300 text-cyan-500 dark:border-slate-700">
                    <span class="font-bold text-slate-700 dark:text-slate-300">تعيين كآخر إصدار</span>
                </label>

                <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-slate-600 to-slate-700 px-6 py-3 font-bold text-white transition hover:shadow-lg hover:shadow-slate-600/50">
                    📱 رفع التحديث
                </button>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300">
                    <div class="mb-3 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span>تحميل نسخة iOS</span>
                        <span data-progress-value>0%</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                        <div data-progress-bar class="upload-progress h-full rounded-full bg-gradient-to-r from-cyan-500 to-emerald-500"></div>
                    </div>
                </div>
            </form>
        </section>

        <!-- Latest Versions Section -->
        <section class="mb-10 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900">
            <h2 class="mb-6 text-2xl font-bold text-slate-950 dark:text-white">🟢 معلومات النسخة</h2>
            <p class="mb-8 text-slate-600 dark:text-slate-300">آخر نسخة مرفوعة:</p>

            <div class="grid gap-6 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-300 bg-blue-50 p-6 dark:border-slate-700 dark:bg-blue-950/20">
                    <p class="text-sm font-bold text-blue-700 dark:text-blue-300">Android:</p>
                    <p class="mt-2 text-3xl font-black text-slate-950 dark:text-white">
                        <?= $latestApk ? escape($latestApk['version']) : 'لم يتم رفع نسخة' ?>
                    </p>
                    <?php if ($latestApk): ?>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                            الحجم: <?= escape(format_bytes((int) $latestApk['file_size'])) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="rounded-2xl border border-slate-300 bg-slate-50 p-6 dark:border-slate-700 dark:bg-slate-950">
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">iPhone:</p>
                    <p class="mt-2 text-3xl font-black text-slate-950 dark:text-white">
                        <?= $latestIpa ? escape($latestIpa['version']) : 'لم يتم رفع نسخة' ?>
                    </p>
                    <?php if ($latestIpa): ?>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                            الحجم: <?= escape(format_bytes((int) $latestIpa['file_size'])) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Versions Management Table -->
        <section class="mb-10 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900">
            <h2 class="mb-6 text-2xl font-bold text-slate-950 dark:text-white">📋 إدارة الإصدارات</h2>

            <?php if (empty($versions)): ?>
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-12 text-center text-slate-600 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-400">
                    <p class="text-lg">لا توجد إصدارات مسجلة حتى الآن</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-300 dark:border-slate-700">
                                <th class="px-4 py-4 text-right font-bold text-slate-700 dark:text-slate-300">الإصدار</th>
                                <th class="px-4 py-4 text-right font-bold text-slate-700 dark:text-slate-300">المنصة</th>
                                <th class="px-4 py-4 text-right font-bold text-slate-700 dark:text-slate-300">الحجم</th>
                                <th class="px-4 py-4 text-right font-bold text-slate-700 dark:text-slate-300">التاريخ</th>
                                <th class="px-4 py-4 text-right font-bold text-slate-700 dark:text-slate-300">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($versions as $version): ?>
                                <tr class="border-b border-slate-200 dark:border-slate-800">
                                    <td class="px-4 py-4 font-bold text-slate-950 dark:text-white"><?= escape($version['version']) ?></td>
                                    <td class="px-4 py-4">
                                        <span class="inline-block rounded-full px-3 py-1 text-xs font-bold <?= (string) $version['platform'] === 'apk' ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300' : 'bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-300' ?>">
                                            <?= strtoupper((string) $version['platform']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600 dark:text-slate-400"><?= escape(format_bytes((int) $version['file_size'])) ?></td>
                                    <td class="px-4 py-4 text-slate-600 dark:text-slate-400"><?= date('d/m/Y', strtotime((string) $version['created_at'])) ?></td>
                                    <td class="px-4 py-4">
                                        <div class="flex gap-2">
                                            <form action="<?= escape(url('upload')) ?>" method="post" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإصدار؟');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= (int) $version['id'] ?>">
                                                <button type="submit" class="rounded-full bg-red-50 px-4 py-2 text-xs font-bold text-red-700 hover:bg-red-100 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-950/60">
                                                    🗑 حذف
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <!-- Footer -->
        <footer class="border-t border-slate-200 pt-8 text-center text-slate-600 dark:border-slate-800 dark:text-slate-400">
            <p>© 2026 <?= escape(APP_NAME) ?> - جميع الحقوق محفوظة</p>
        </footer>
    </div>
</div>
<?php render_admin_footer(); ?>
