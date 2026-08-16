<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

if (!is_post()) {
    json_response([
        'success' => false,
        'message' => 'Method not allowed. Use POST with multipart/form-data.',
    ], 405);
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    json_response([
        'success' => false,
        'message' => 'رمز الحماية CSRF غير صالح.',
    ], 419);
}

$action = (string) ($_POST['action'] ?? 'chunk');
$uploadId = preg_replace('/[^A-Za-z0-9_-]+/', '', (string) ($_POST['upload_id'] ?? ''));

if ($uploadId === '') {
    json_response([
        'success' => false,
        'message' => 'معرّف الرفع غير صالح.',
    ], 422);
}

$chunkBase = APP_UPLOAD_DIRECTORY . '/_chunks/' . $uploadId;

if ($action === 'chunk') {
    $chunkIndex = (int) ($_POST['chunk_index'] ?? -1);
    $totalChunks = (int) ($_POST['total_chunks'] ?? 0);
    $chunkFile = $_FILES['chunk'] ?? null;

    if ($chunkIndex < 0 || $totalChunks <= 0) {
        json_response([
            'success' => false,
            'message' => 'بيانات الـ chunk غير صالحة.',
        ], 422);
    }

    if (!$chunkFile || ($chunkFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $error = (int) ($chunkFile['error'] ?? UPLOAD_ERR_NO_FILE);
        json_response([
            'success' => false,
            'message' => upload_error_message($error),
            'error_code' => $error,
        ], 422);
    }

    if (!is_dir($chunkBase)) {
        mkdir($chunkBase, 0775, true);
    }

    $chunkName = sprintf('chunk_%05d.part', $chunkIndex);
    $chunkPath = $chunkBase . '/' . $chunkName;

    if (!move_uploaded_file((string) $chunkFile['tmp_name'], $chunkPath)) {
        json_response([
            'success' => false,
            'message' => 'تعذر حفظ جزء من الملف على السيرفر.',
        ], 500);
    }

    json_response([
        'success' => true,
        'message' => 'تم استلام الجزء بنجاح.',
        'chunk_index' => $chunkIndex,
        'total_chunks' => $totalChunks,
    ]);
}

if ($action !== 'complete') {
    json_response([
        'success' => false,
        'message' => 'طلب غير صالح.',
    ], 422);
}

set_time_limit(0);

$totalChunks = (int) ($_POST['total_chunks'] ?? 0);
$fileName = (string) ($_POST['file_name'] ?? 'app-file');
$fileSize = (int) ($_POST['file_size'] ?? 0);
$platform = normalize_platform((string) ($_POST['platform'] ?? 'apk'));
$version = (string) ($_POST['version'] ?? '');
$updateNotes = (string) ($_POST['update_notes'] ?? '');
$isLatest = !empty($_POST['is_latest']);
$id = (int) ($_POST['id'] ?? 0);

if ($totalChunks <= 0) {
    json_response([
        'success' => false,
        'message' => 'عدد الأجزاء غير صالح.',
    ], 422);
}

if (!is_dir($chunkBase)) {
    json_response([
        'success' => false,
        'message' => 'مجلد الأجزاء غير موجود.',
    ], 404);
}

for ($i = 0; $i < $totalChunks; $i++) {
    $chunkPath = $chunkBase . '/' . sprintf('chunk_%05d.part', $i);
    if (!is_file($chunkPath)) {
        json_response([
            'success' => false,
            'message' => 'بعض أجزاء الملف مفقودة. حاول الرفع مرة أخرى.',
        ], 409);
    }
}

$assembledPath = $chunkBase . '/assembled.tmp';
$assembled = fopen($assembledPath, 'wb');
if ($assembled === false) {
    json_response([
        'success' => false,
        'message' => 'تعذر تجهيز ملف التجميع.',
    ], 500);
}

for ($i = 0; $i < $totalChunks; $i++) {
    $chunkPath = $chunkBase . '/' . sprintf('chunk_%05d.part', $i);
    $chunkStream = fopen($chunkPath, 'rb');
    if ($chunkStream === false) {
        fclose($assembled);
        json_response([
            'success' => false,
            'message' => 'تعذر قراءة جزء من الملف.',
        ], 500);
    }
    stream_copy_to_stream($chunkStream, $assembled);
    fclose($chunkStream);
}

fclose($assembled);

$assembledSize = (int) (is_file($assembledPath) ? filesize($assembledPath) : 0);
if ($assembledSize <= 0) {
    json_response([
        'success' => false,
        'message' => 'تعذر تجميع الملف.',
    ], 500);
}

if ($fileSize > 0 && $assembledSize !== $fileSize) {
    json_response([
        'success' => false,
        'message' => 'حجم الملف بعد التجميع غير مطابق.',
    ], 422);
}

if ($assembledSize > app_upload_max_bytes()) {
    json_response([
        'success' => false,
        'message' => 'حجم الملف أكبر من الحد المسموح في إعدادات التطبيق (' . APP_MAX_UPLOAD_MB . ' MB).',
    ], 422);
}

$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
if (!in_array($extension, APP_ALLOWED_EXTENSIONS, true) || $extension !== $platform) {
    json_response([
        'success' => false,
        'message' => 'الصيغة غير مدعومة. الرجاء رفع ملف .' . $platform . ' فقط.',
    ], 422);
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($assembledPath) ?: 'application/octet-stream';
$allowedMimes = APP_ALLOWED_MIME_TYPES[$platform] ?? [];
if (!in_array($mimeType, $allowedMimes, true)) {
    json_response([
        'success' => false,
        'message' => 'نوع الملف غير مسموح به.',
    ], 422);
}

$safeName = preg_replace('/[^A-Za-z0-9._-]+/', '-', pathinfo($fileName, PATHINFO_FILENAME));
$safeName = trim((string) $safeName, '-_.');
if ($safeName === '') {
    $safeName = 'app-file';
}

$folder = APP_UPLOAD_DIRECTORY . '/' . $platform;
if (!is_dir($folder)) {
    mkdir($folder, 0775, true);
}

$storedName = $safeName . '-' . date('YmdHis') . '.' . $extension;
$finalPath = $folder . '/' . $storedName;

if (!@rename($assembledPath, $finalPath)) {
    if (!@copy($assembledPath, $finalPath)) {
        json_response([
            'success' => false,
            'message' => 'تعذر حفظ الملف بعد التجميع.',
        ], 500);
    }
    @unlink($assembledPath);
}

$relativePath = 'uploads/' . $platform . '/' . $storedName;

$versionId = save_version_record([
    'id' => $id,
    'version' => $version,
    'update_notes' => $updateNotes,
    'platform' => $platform,
    'is_latest' => $isLatest,
    'file_name' => $fileName,
    'file_path' => $relativePath,
    'file_size' => $assembledSize,
    'has_new_file' => true,
]);

cleanup_chunks($chunkBase);

json_response([
    'success' => true,
    'message' => '✅ تم رفع التطبيق بنجاح',
    'id' => $versionId,
    'redirect' => url('admin'),
]);

function cleanup_chunks(string $path): void
{
    if (!is_dir($path)) {
        return;
    }

    $items = scandir($path);
    if (!is_array($items)) {
        return;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $full = $path . '/' . $item;
        if (is_dir($full)) {
            cleanup_chunks($full);
            continue;
        }
        @unlink($full);
    }

    @rmdir($path);
}
