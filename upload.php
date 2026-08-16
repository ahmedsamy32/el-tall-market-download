<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_admin();

if (!is_post()) {
    redirect('admin');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    $message = 'رمز الحماية CSRF غير صالح.';
    if (wants_json()) {
        json_response(['success' => false, 'message' => $message], 419);
    }
    flash('error', $message);
    redirect('admin');
}

$action = (string) ($_POST['action'] ?? 'save');

try {
    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            throw new RuntimeException('المرجع المطلوب للحذف غير صحيح.');
        }

        if (!delete_version_record($id)) {
            throw new RuntimeException('تعذر العثور على الإصدار المطلوب حذفه.');
        }

        $message = 'تم حذف الإصدار بنجاح.';
    } elseif ($action === 'set_latest') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            throw new RuntimeException('المرجع المطلوب غير صحيح.');
        }

        $version = get_version_by_id($id);
        if (!$version) {
            throw new RuntimeException('الإصدار غير موجود.');
        }

        set_latest_version($id, (string) $version['platform']);
        $message = 'تم تعيين الإصدار الحالي كآخر إصدار رسمي.';
    } else {
        $id = (int) ($_POST['id'] ?? 0);
        $uploadedFile = $_FILES['app_file'] ?? null;
        $existingFilePath = trim((string) ($_POST['existing_file_path'] ?? ''));
        $hasUploadedFile = $uploadedFile && ($uploadedFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;
        $hasExistingFile = $existingFilePath !== '' && !$hasUploadedFile;

        if ($id === 0 && !$hasUploadedFile && !$hasExistingFile) {
            $error = (int) ($uploadedFile['error'] ?? UPLOAD_ERR_NO_FILE);
            throw new RuntimeException($uploadedFile ? upload_error_message($error) : 'الرجاء رفع ملف التطبيق أو اختيار ملف من السيرفر قبل الحفظ.');
        }

        $existingFileInfo = null;
        if ($hasExistingFile) {
            $existingFileInfo = resolve_existing_upload_file($existingFilePath, (string) ($_POST['platform'] ?? 'apk'));
        }

        $payload = [
            'id' => $id,
            'version' => (string) ($_POST['version'] ?? ''),
            'update_notes' => (string) ($_POST['update_notes'] ?? ''),
            'platform' => (string) ($_POST['platform'] ?? 'apk'),
            'is_latest' => !empty($_POST['is_latest']),
        ];

        if ($existingFileInfo) {
            $payload['file_name'] = $existingFileInfo['file_name'];
            $payload['file_path'] = $existingFileInfo['file_path'];
            $payload['file_size'] = $existingFileInfo['file_size'];
            $payload['has_new_file'] = true;
        }

        $versionId = save_version_record(
            $payload,
            $hasUploadedFile ? $uploadedFile : null
        );

        $message = '✅ تم رفع التطبيق بنجاح';
    }

    if (wants_json()) {
        json_response([
            'success' => true,
            'message' => $message,
            'redirect' => url('admin'),
        ]);
    }

    flash('success', $message);
    redirect('admin');
} catch (Throwable $exception) {
    if (wants_json()) {
        json_response([
            'success' => false,
            'message' => $exception->getMessage(),
        ], 422);
    }

    flash('error', $exception->getMessage());
    redirect('admin');
}
