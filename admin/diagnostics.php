<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$uploadMaxRaw = (string) ini_get('upload_max_filesize');
$postMaxRaw = (string) ini_get('post_max_size');
$uploadMaxLabel = format_ini_limit($uploadMaxRaw);
$postMaxLabel = format_ini_limit($postMaxRaw);
$effectiveBytes = effective_upload_limit_bytes();
$effectiveLabel = $effectiveBytes > 0 ? format_bytes($effectiveBytes) : 'غير محدود';
$memoryLimitLabel = format_ini_limit((string) ini_get('memory_limit'));
$maxFileUploads = ini_get('max_file_uploads') ?: 'غير معروف';
$maxInputTime = ini_get('max_input_time') ?: 'غير معروف';
$maxExecutionTime = ini_get('max_execution_time') ?: 'غير معروف';

$uploadTmpDir = ini_get('upload_tmp_dir');
if ($uploadTmpDir === false || $uploadTmpDir === '') {
    $uploadTmpDir = sys_get_temp_dir();
}

$uploadDir = APP_UPLOAD_DIRECTORY;
$uploadDirExists = is_dir($uploadDir);
$diskFree = $uploadDirExists ? disk_free_space($uploadDir) : false;
$diskTotal = $uploadDirExists ? disk_total_space($uploadDir) : false;
$diskFreeLabel = $diskFree !== false ? format_bytes((int) $diskFree) : 'غير معروف';
$diskTotalLabel = $diskTotal !== false ? format_bytes((int) $diskTotal) : 'غير معروف';

$serverSoftware = (string) ($_SERVER['SERVER_SOFTWARE'] ?? 'غير معروف');
$serverName = (string) ($_SERVER['SERVER_NAME'] ?? '');
$serverAddr = (string) ($_SERVER['SERVER_ADDR'] ?? '');
$proxyRay = (string) ($_SERVER['HTTP_CF_RAY'] ?? '');
$proxyForwardedFor = (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '');
$proxyConnectingIp = (string) ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? '');

render_admin_header('تشخيص الرفع');
?>
<div class="space-y-6">
    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <h1 class="text-3xl font-black text-slate-950 dark:text-white">تشخيص مشاكل الرفع</h1>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">الصفحة دي بتعرض حدود السيرفر الحالية وتسمح باختبار الرفع لمعرفة أين يحدث الانقطاع.</p>
    </section>

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <h2 class="text-lg font-bold text-slate-950 dark:text-white">حدود الرفع</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                <p class="text-xs text-slate-500">upload_max_filesize</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= escape($uploadMaxLabel) ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                <p class="text-xs text-slate-500">post_max_size</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= escape($postMaxLabel) ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                <p class="text-xs text-slate-500">الحد الفعلي</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= escape($effectiveLabel) ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                <p class="text-xs text-slate-500">حد التطبيق</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= (int) APP_MAX_UPLOAD_MB ?> MB</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                <p class="text-xs text-slate-500">memory_limit</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= escape($memoryLimitLabel) ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                <p class="text-xs text-slate-500">max_file_uploads</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= escape((string) $maxFileUploads) ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                <p class="text-xs text-slate-500">max_input_time</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= escape((string) $maxInputTime) ?>s</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                <p class="text-xs text-slate-500">max_execution_time</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= escape((string) $maxExecutionTime) ?>s</p>
            </div>
        </div>
        <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">الحد الفعلي للرفع هو الأصغر بين upload_max_filesize و post_max_size.</p>
    </section>

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <h2 class="text-lg font-bold text-slate-950 dark:text-white">بيئة السيرفر</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                <p class="text-xs text-slate-500">server_software</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= escape($serverSoftware) ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                <p class="text-xs text-slate-500">server_name</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= escape($serverName) ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                <p class="text-xs text-slate-500">server_addr</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= escape($serverAddr) ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                <p class="text-xs text-slate-500">upload_tmp_dir</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= escape((string) $uploadTmpDir) ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                <p class="text-xs text-slate-500">upload_directory</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= escape($uploadDir) ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-950">
                <p class="text-xs text-slate-500">disk_free / disk_total</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= escape($diskFreeLabel) ?> / <?= escape($diskTotalLabel) ?></p>
            </div>
        </div>
        <?php if ($proxyRay || $proxyForwardedFor || $proxyConnectingIp): ?>
            <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-900/60 dark:bg-amber-950/40 dark:text-amber-200">
                <p class="font-semibold">Proxy/Cloudflare headers detected.</p>
                <p class="mt-1 text-xs">cf_ray: <?= escape($proxyRay) ?> | x_forwarded_for: <?= escape($proxyForwardedFor) ?> | cf_connecting_ip: <?= escape($proxyConnectingIp) ?></p>
            </div>
        <?php endif; ?>
    </section>

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <h2 class="text-lg font-bold text-slate-950 dark:text-white">اختبار الرفع</h2>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">ارفع نفس الملف هنا. لو الرفع فشل قبل ما PHP يستقبل الطلب، سترى Status=0 أو رد فارغ.</p>

        <form method="post" action="<?= escape(url('admin/upload-probe')) ?>" enctype="multipart/form-data" class="mt-5 space-y-5" data-diagnostics-form>
            <?= csrf_field() ?>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">اختر ملفًا للاختبار</label>
                <input type="file" name="probe_file" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-950" required>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300">
                <div class="mb-3 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <span>Upload Progress</span>
                    <span data-progress-value>0%</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                    <div data-progress-bar class="upload-progress h-full rounded-full bg-gradient-to-r from-cyan-500 to-emerald-500"></div>
                </div>
            </div>
            <button type="submit" class="rounded-2xl bg-slate-950 px-6 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800 dark:bg-cyan-500 dark:text-slate-950 dark:hover:bg-cyan-400">تشغيل الاختبار</button>
        </form>

        <div class="mt-5">
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">نتيجة الاختبار</p>
            <pre data-diagnostics-output class="mt-2 max-h-80 overflow-auto rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xs text-slate-700 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200">No test executed yet.</pre>
        </div>
    </section>
</div>
<script src="<?= asset('js/diagnostics.js') ?>"></script>
<?php render_admin_footer(); ?>
