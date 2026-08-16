<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$errors = [];
$logoPath = get_setting('site_logo');
$faviconPath = get_setting('site_favicon');
$bannerPath = get_setting('site_banner');
$bannerLink = get_setting('site_banner_link', '');
$downloadBannerPath = get_setting('site_download_banner');
$downloadBannerLink = get_setting('site_download_banner_link', '');

if (is_post()) {
    $expectsJson = wants_json();

    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        if ($expectsJson) {
            json_response(['success' => false, 'message' => 'رمز الحماية غير صالح.'], 419);
        }
        $errors[] = 'رمز الحماية غير صالح.';
    } else {
        $action = (string) ($_POST['action'] ?? '');

        try {
            if ($action === 'upload_logo') {
                $file = $_FILES['logo_file'] ?? null;
                if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                    throw new RuntimeException('الرجاء اختيار شعار الموقع.');
                }
                store_branding_upload($file, 'site_logo');

                if ($expectsJson) {
                    json_response([
                        'success' => true,
                        'message' => 'تم تحديث شعار الموقع بنجاح.',
                        'redirect' => url('admin/branding'),
                    ]);
                }

                flash('success', 'تم تحديث شعار الموقع بنجاح.');
                redirect('admin/branding');
            }

            if ($action === 'upload_favicon') {
                $file = $_FILES['favicon_file'] ?? null;
                if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                    throw new RuntimeException('الرجاء اختيار أيقونة الموقع.');
                }
                store_branding_upload($file, 'site_favicon');

                if ($expectsJson) {
                    json_response([
                        'success' => true,
                        'message' => 'تم تحديث أيقونة الموقع بنجاح.',
                        'redirect' => url('admin/branding'),
                    ]);
                }

                flash('success', 'تم تحديث أيقونة الموقع بنجاح.');
                redirect('admin/branding');
            }

            if ($action === 'remove_logo') {
                remove_branding_setting('site_logo');

                if ($expectsJson) {
                    json_response([
                        'success' => true,
                        'message' => 'تم حذف شعار الموقع.',
                        'redirect' => url('admin/branding'),
                    ]);
                }

                flash('success', 'تم حذف شعار الموقع.');
                redirect('admin/branding');
            }

            if ($action === 'remove_favicon') {
                remove_branding_setting('site_favicon');

                if ($expectsJson) {
                    json_response([
                        'success' => true,
                        'message' => 'تم حذف أيقونة الموقع.',
                        'redirect' => url('admin/branding'),
                    ]);
                }

                flash('success', 'تم حذف أيقونة الموقع.');
                redirect('admin/branding');
            }

            if ($action === 'add_site_banner') {
                $file = $_FILES['banner_file'] ?? null;
                if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                    throw new RuntimeException('الرجاء اختيار صورة البنر الإعلاني.');
                }
                
                $link = trim((string) ($_POST['banner_link'] ?? ''));
                $normalizedLink = $link !== '' && !preg_match('~^https?://~i', $link)
                    ? 'https://' . $link
                    : $link;

                if ($link !== '' && !filter_var($normalizedLink, FILTER_VALIDATE_URL)) {
                    throw new RuntimeException('رابط التوجيه غير صحيح.');
                }

                add_site_banner($file, $normalizedLink);

                if ($expectsJson) {
                    json_response([
                        'success' => true,
                        'message' => 'تم إضافة البنر الإعلاني بنجاح.',
                        'redirect' => url('admin/branding'),
                    ]);
                }

                flash('success', 'تم إضافة البنر الإعلاني بنجاح.');
                redirect('admin/branding');
            }

            if ($action === 'remove_site_banner') {
                $id = trim((string) ($_POST['banner_id'] ?? ''));
                if ($id === '') {
                    throw new RuntimeException('معرف البنر غير صحيح.');
                }

                remove_site_banner($id);

                if ($expectsJson) {
                    json_response([
                        'success' => true,
                        'message' => 'تم حذف البنر الإعلاني.',
                        'redirect' => url('admin/branding'),
                    ]);
                }

                flash('success', 'تم حذف البنر الإعلاني.');
                redirect('admin/branding');
            }


            if ($action === 'upload_download_banner') {
                $file = $_FILES['download_banner_file'] ?? null;
                if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                    throw new RuntimeException('الرجاء اختيار صورة بنر صفحة التحميل.');
                }
                store_branding_upload($file, 'site_download_banner');

                if ($expectsJson) {
                    json_response([
                        'success' => true,
                        'message' => 'تم تحديث بنر صفحة التحميل بنجاح.',
                        'redirect' => url('admin/branding'),
                    ]);
                }

                flash('success', 'تم تحديث بنر صفحة التحميل بنجاح.');
                redirect('admin/branding');
            }

            if ($action === 'update_download_banner_link') {
                $link = trim((string) ($_POST['download_banner_link'] ?? ''));
                $normalizedLink = $link !== '' && !preg_match('~^https?://~i', $link)
                    ? 'https://' . $link
                    : $link;

                if ($link !== '' && !filter_var($normalizedLink, FILTER_VALIDATE_URL)) {
                    throw new RuntimeException('رابط التوجيه غير صحيح.');
                }

                set_setting('site_download_banner_link', $normalizedLink);

                if ($expectsJson) {
                    json_response([
                        'success' => true,
                        'message' => 'تم تحديث رابط بنر صفحة التحميل بنجاح.',
                        'redirect' => url('admin/branding'),
                    ]);
                }

                flash('success', 'تم تحديث رابط بنر صفحة التحميل بنجاح.');
                redirect('admin/branding');
            }

            if ($action === 'remove_download_banner') {
                remove_branding_setting('site_download_banner');
                delete_setting('site_download_banner_link');

                if ($expectsJson) {
                    json_response([
                        'success' => true,
                        'message' => 'تم حذف بنر صفحة التحميل.',
                        'redirect' => url('admin/branding'),
                    ]);
                }

                flash('success', 'تم حذف بنر صفحة التحميل.');
                redirect('admin/branding');
            }

            throw new RuntimeException('الإجراء غير صالح.');
        } catch (Throwable $exception) {
            if ($expectsJson) {
                json_response(['success' => false, 'message' => $exception->getMessage()], 422);
            }
            $errors[] = $exception->getMessage();
        }
    }
}

render_admin_header('الهوية البصرية');
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
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-600 dark:text-cyan-400">Branding</p>
            <h2 class="mt-2 text-3xl font-black">شعار الموقع وأيقونته</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">يمكنك رفع شعار يظهر في الهيدر وصفحات الموقع، وأيقونة المتصفح (favicon).</p>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">شعار الموقع</h3>
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        <?= $logoPath ? 'مخصص' : 'افتراضي' ?>
                    </span>
                </div>
                <div class="mt-4 flex items-center gap-3 sm:gap-4">
                    <img src="<?= escape(site_logo_url()) ?>" alt="Site Logo" class="h-20 w-20 rounded-2xl bg-slate-100 p-2 dark:bg-slate-900 sm:h-24 sm:w-24 lg:h-32 lg:w-32">
                    <p class="text-sm text-slate-600 dark:text-slate-300">الصيغ المدعومة: PNG, JPG, WEBP, SVG, ICO.</p>
                </div>

                <form method="post" action="<?= escape(url('admin/branding')) ?>" enctype="multipart/form-data" class="mt-6 space-y-4" data-xhr-form>
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="upload_logo">
                    <input type="file" name="logo_file" accept=".png,.jpg,.jpeg,.webp,.svg,.ico" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-900" required>
                    <button type="submit" class="rounded-2xl bg-slate-950 px-6 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800 dark:bg-cyan-500 dark:text-slate-950 dark:hover:bg-cyan-400">تحديث الشعار</button>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                        <div class="mb-3 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                            <span>تحميل الشعار</span>
                            <span data-progress-value>0%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                            <div data-progress-bar class="upload-progress h-full rounded-full bg-gradient-to-r from-cyan-500 to-emerald-500"></div>
                        </div>
                    </div>
                </form>

                <?php if ($logoPath): ?>
                    <form method="post" class="mt-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="remove_logo">
                        <button type="submit" class="rounded-2xl border border-red-200 bg-red-50 px-5 py-2 text-xs font-bold text-red-700 hover:bg-red-100 dark:border-red-900/60 dark:bg-red-950/60 dark:text-red-200">حذف الشعار</button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">أيقونة الموقع (Favicon)</h3>
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        <?= $faviconPath ? 'مخصص' : 'افتراضي' ?>
                    </span>
                </div>
                <div class="mt-4 flex items-center gap-4">
                    <img src="<?= escape(site_favicon_url()) ?>" alt="Site Favicon" class="h-12 w-12 rounded-2xl bg-slate-100 p-2 dark:bg-slate-900">
                    <p class="text-sm text-slate-600 dark:text-slate-300">يفضل رفع أيقونة مربعة 32x32 أو 64x64.</p>
                </div>

                <form method="post" action="<?= escape(url('admin/branding')) ?>" enctype="multipart/form-data" class="mt-6 space-y-4" data-xhr-form>
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="upload_favicon">
                    <input type="file" name="favicon_file" accept=".png,.jpg,.jpeg,.webp,.svg,.ico" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-900" required>
                    <button type="submit" class="rounded-2xl bg-slate-950 px-6 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800 dark:bg-cyan-500 dark:text-slate-950 dark:hover:bg-cyan-400">تحديث الأيقونة</button>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                        <div class="mb-3 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                            <span>تحميل الأيقونة</span>
                            <span data-progress-value>0%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                            <div data-progress-bar class="upload-progress h-full rounded-full bg-gradient-to-r from-cyan-500 to-emerald-500"></div>
                        </div>
                    </div>
                </form>

                <?php if ($faviconPath): ?>
                    <form method="post" class="mt-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="remove_favicon">
                        <button type="submit" class="rounded-2xl border border-red-200 bg-red-50 px-5 py-2 text-xs font-bold text-red-700 hover:bg-red-100 dark:border-red-900/60 dark:bg-red-950/60 dark:text-red-200">حذف الأيقونة</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="glass-card rounded-[2rem] p-6 dark:bg-slate-900/80 mt-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-600 dark:text-cyan-400">Advertising Banners</p>
            <h2 class="mt-2 text-3xl font-black">البنرات الإعلانية في الصفحة الرئيسية</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">يمكنك رفع بنر إعلاني أو أكثر ليظهر في سلايدر (سلايدر متحرك) بأعلى الصفحة الرئيسية مع إمكانية تحديد رابط لكل بنر.</p>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-12">
            <!-- Left: Grid of Current Banners -->
            <div class="lg:col-span-7 rounded-3xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-950 space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">البنرات النشطة حالياً</h3>
                <?php 
                $bannersList = site_banners(); 
                if (empty($bannersList)): 
                ?>
                    <div class="flex h-[180px] w-full flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 dark:border-slate-800 dark:bg-slate-900">
                        <span class="text-3xl mb-2">🖼</span>
                        <span class="text-sm text-slate-500 dark:text-slate-400">لا توجد بنرات إعلانية حالياً</span>
                    </div>
                <?php else: ?>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <?php foreach ($bannersList as $banner): ?>
                            <?php $bannerUrl = url(ltrim($banner['image'], '/')); ?>
                            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 p-2 flex flex-col justify-between">
                                <div class="relative overflow-hidden rounded-xl h-[100px]">
                                    <img src="<?= escape($bannerUrl) ?>" alt="Banner Preview" class="w-full h-full object-cover">
                                </div>
                                <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 truncate px-1" title="<?= escape($banner['link']) ?>">
                                    <strong>الرابط:</strong> <?= $banner['link'] !== '' ? escape($banner['link']) : 'لا يوجد رابط' ?>
                                </div>
                                <div class="mt-3 flex justify-end">
                                    <form method="post" action="<?= escape(url('admin/branding')) ?>" data-xhr-form>
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="remove_site_banner">
                                        <input type="hidden" name="banner_id" value="<?= escape($banner['id']) ?>">
                                        <button type="submit" class="rounded-xl border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700 hover:bg-red-100 dark:border-red-900/60 dark:bg-red-950/60 dark:text-red-200 transition">
                                            حذف البنر
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Add New Banner Form -->
            <div class="lg:col-span-5 rounded-3xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-950">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">إضافة بنر جديد</h3>
                
                <form method="post" action="<?= escape(url('admin/branding')) ?>" enctype="multipart/form-data" class="space-y-4" data-xhr-form>
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="add_site_banner">
                    
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">صورة البنر (المقاس المقترح: أفقي 1200x300)</label>
                        <input type="file" name="banner_file" accept=".png,.jpg,.jpeg,.webp,.svg" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-900" required>
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">الحجم الأقصى: <?= APP_BRANDING_MAX_MB ?> ميجابايت.</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">رابط التوجيه (اختياري)</label>
                        <input type="text" name="banner_link" class="input-surface w-full rounded-2xl px-4 py-3" placeholder="https://example.com/...">
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-slate-950 px-6 py-3.5 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800 dark:bg-cyan-500 dark:text-slate-950 dark:hover:bg-cyan-400">إضافة البنر</button>
                    
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                        <div class="mb-3 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                            <span>تحميل البنر</span>
                            <span data-progress-value>0%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                            <div data-progress-bar class="upload-progress h-full rounded-full bg-gradient-to-r from-cyan-500 to-emerald-500"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="glass-card rounded-[2rem] p-6 dark:bg-slate-900/80 mt-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-600 dark:text-cyan-400">Download Page Banner</p>
            <h2 class="mt-2 text-3xl font-black">بنر صفحة تحميل التطبيق</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">يمكنك رفع صورة إعلان/بنر مخصص (يفضل أن يكون بنسب طولية أو مربعة) ليظهر في القائمة الجانبية اليسرى لصفحة تحميل التطبيق.</p>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <!-- Left Column: Upload / Preview -->
            <div class="rounded-3xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">صورة بنر صفحة التحميل</h3>
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        <?= $downloadBannerPath ? 'مخصص' : 'لا يوجد بنر' ?>
                    </span>
                </div>
                <div class="mt-4 flex flex-col gap-4">
                    <?php if ($downloadBannerPath): ?>
                        <img src="<?= escape(site_download_banner_url()) ?>" alt="Download Banner Preview" class="w-full h-auto max-h-[200px] object-contain rounded-2xl border border-slate-200 dark:border-slate-800">
                    <?php else: ?>
                        <div class="flex h-[120px] w-full items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 dark:border-slate-800 dark:bg-slate-900">
                            <span class="text-sm text-slate-500 dark:text-slate-400">لم يتم رفع بنر صفحة التحميل بعد</span>
                        </div>
                    <?php endif; ?>
                    <p class="text-sm text-slate-600 dark:text-slate-300">الصيغ المدعومة: PNG, JPG, WEBP, SVG. الحجم الأقصى: <?= APP_BRANDING_MAX_MB ?> ميجابايت.</p>
                </div>

                <form method="post" action="<?= escape(url('admin/branding')) ?>" enctype="multipart/form-data" class="mt-6 space-y-4" data-xhr-form>
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="upload_download_banner">
                    <input type="file" name="download_banner_file" accept=".png,.jpg,.jpeg,.webp,.svg" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-900" required>
                    <button type="submit" class="rounded-2xl bg-slate-950 px-6 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800 dark:bg-cyan-500 dark:text-slate-950 dark:hover:bg-cyan-400">تحديث صورة البنر</button>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                        <div class="mb-3 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                            <span>تحميل البنر</span>
                            <span data-progress-value>0%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                            <div data-progress-bar class="upload-progress h-full rounded-full bg-gradient-to-r from-cyan-500 to-emerald-500"></div>
                        </div>
                    </div>
                </form>

                <?php if ($downloadBannerPath): ?>
                    <form method="post" class="mt-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="remove_download_banner">
                        <button type="submit" class="rounded-2xl border border-red-200 bg-red-50 px-5 py-2 text-xs font-bold text-red-700 hover:bg-red-100 dark:border-red-900/60 dark:bg-red-950/60 dark:text-red-200">حذف البنر بالكامل</button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Right Column: Banner Link Settings -->
            <div class="rounded-3xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-950">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">رابط توجيه بنر صفحة التحميل</h3>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">الرابط الذي يفتح عندما يقوم الزائر بالضغط على البنر في صفحة تحميل التطبيق.</p>

                <form method="post" action="<?= escape(url('admin/branding')) ?>" class="mt-6 space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="update_download_banner_link">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">رابط التوجيه (URL)</label>
                        <input type="text" name="download_banner_link" value="<?= escape($downloadBannerLink) ?>" class="input-surface w-full rounded-2xl px-4 py-3" placeholder="https://example.com/...">
                    </div>
                    <button type="submit" class="rounded-2xl bg-slate-950 px-6 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800 dark:bg-cyan-500 dark:text-slate-950 dark:hover:bg-cyan-400">تحديث الرابط</button>
                </form>
            </div>
        </div>
    </section>
</div>
<?php render_admin_footer(); ?>
