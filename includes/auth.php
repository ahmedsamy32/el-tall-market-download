<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

function ensure_admin_seeded(): void
{
    ensure_upload_directory();

    $pdo = db();
    $pdo->exec(sprintf(
        'CREATE TABLE IF NOT EXISTS %s (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            full_name VARCHAR(150) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
        APP_DB_TABLE_ADMINS
    ));

    $count = (int) $pdo->query(sprintf('SELECT COUNT(*) FROM %s', APP_DB_TABLE_ADMINS))->fetchColumn();

    if ($count > 0) {
        return;
    }

    $statement = $pdo->prepare(sprintf(
        'INSERT INTO %s (username, full_name, password_hash) VALUES (:username, :full_name, :password_hash)',
        APP_DB_TABLE_ADMINS
    ));

    $statement->execute([
        ':username' => APP_DEFAULT_ADMIN_USERNAME,
        ':full_name' => APP_DEFAULT_ADMIN_FULLNAME,
        ':password_hash' => password_hash(APP_DEFAULT_ADMIN_PASSWORD, PASSWORD_DEFAULT),
    ]);
}

function ensure_versions_table(): void
{
    $pdo = db();
    $pdo->exec(sprintf(
        'CREATE TABLE IF NOT EXISTS %s (
            id INT AUTO_INCREMENT PRIMARY KEY,
            version VARCHAR(50) NOT NULL,
            update_notes TEXT NOT NULL,
            file_name VARCHAR(255) DEFAULT NULL,
            file_path VARCHAR(255) DEFAULT NULL,
            file_size BIGINT UNSIGNED DEFAULT 0,
            platform ENUM(\'apk\', \'ipa\') NOT NULL DEFAULT \'apk\',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_latest TINYINT(1) NOT NULL DEFAULT 0,
            INDEX idx_platform_latest (platform, is_latest),
            INDEX idx_platform_created (platform, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
        APP_DB_TABLE_VERSIONS
    ));
}

function current_admin(): ?array
{
    if (empty($_SESSION['admin_id'])) {
        return null;
    }

    $statement = db()->prepare(sprintf('SELECT id, username, full_name, created_at FROM %s WHERE id = :id LIMIT 1', APP_DB_TABLE_ADMINS));
    $statement->execute([':id' => (int) $_SESSION['admin_id']]);
    $admin = $statement->fetch();

    return $admin ?: null;
}

function is_default_admin_credentials(array $admin): bool
{
    if (!isset($admin['password_hash'])) {
        return false;
    }

    return password_verify(APP_DEFAULT_ADMIN_PASSWORD, (string) $admin['password_hash']);
}

function is_admin_logged_in(): bool
{
    return current_admin() !== null;
}

function login_admin(string $username, string $password): bool
{
    $statement = db()->prepare(sprintf('SELECT * FROM %s WHERE username = :username LIMIT 1', APP_DB_TABLE_ADMINS));
    $statement->execute([':username' => $username]);
    $admin = $statement->fetch();

    if (!$admin || !password_verify($password, (string) $admin['password_hash'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int) $admin['id'];
    $_SESSION['admin_username'] = (string) $admin['username'];
    $_SESSION['admin_full_name'] = (string) $admin['full_name'];
    $_SESSION['admin_force_update'] = is_default_admin_credentials($admin);

    return true;
}

function logout_admin(): void
{
    unset($_SESSION['admin_id'], $_SESSION['admin_username'], $_SESSION['admin_full_name'], $_SESSION['admin_force_update']);
    session_regenerate_id(true);
}

function require_admin(): void
{
    $admin = current_admin();
    if ($admin) {
        if (is_default_admin_credentials($admin)) {
            $_SESSION['admin_force_update'] = true;
        }

        if (!empty($_SESSION['admin_force_update'])) {
            $script = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
            if ($script !== 'account.php') {
                flash('error', 'يرجى تغيير كلمة المرور الافتراضية قبل المتابعة.');
                redirect('admin/account');
            }
        }

        return;
    }

    flash('error', 'الرجاء تسجيل الدخول أولًا.');
    redirect('admin/login');
}

function update_admin_profile(
    int $id,
    string $username,
    string $fullName,
    string $currentPassword,
    string $newPassword,
    string $confirmPassword
): void {
    $username = trim($username);
    $fullName = trim($fullName);

    if ($username === '' || $fullName === '') {
        throw new InvalidArgumentException('الرجاء إدخال اسم المستخدم والاسم الكامل.');
    }

    if ($currentPassword === '') {
        throw new InvalidArgumentException('الرجاء إدخال كلمة المرور الحالية.');
    }

    $statement = db()->prepare(sprintf('SELECT id, username, password_hash FROM %s WHERE id = :id LIMIT 1', APP_DB_TABLE_ADMINS));
    $statement->execute([':id' => $id]);
    $admin = $statement->fetch();

    if (!$admin) {
        throw new RuntimeException('الحساب غير موجود.');
    }

    if (!password_verify($currentPassword, (string) $admin['password_hash'])) {
        throw new RuntimeException('كلمة المرور الحالية غير صحيحة.');
    }

    $usesDefaultPassword = is_default_admin_credentials($admin);
    $wantsPasswordChange = $newPassword !== '' || $confirmPassword !== '';
    $passwordHash = null;

    if ($usesDefaultPassword && !$wantsPasswordChange) {
        throw new RuntimeException('يجب تغيير كلمة المرور الافتراضية قبل المتابعة.');
    }

    if ($wantsPasswordChange) {
        if ($newPassword === '' || $confirmPassword === '') {
            throw new InvalidArgumentException('الرجاء إدخال كلمة المرور الجديدة وتأكيدها.');
        }

        if ($newPassword !== $confirmPassword) {
            throw new RuntimeException('كلمتا المرور غير متطابقتين.');
        }

        if (strlen($newPassword) < 8) {
            throw new RuntimeException('كلمة المرور الجديدة يجب ألا تقل عن 8 أحرف.');
        }

        if ($newPassword === APP_DEFAULT_ADMIN_PASSWORD) {
            throw new RuntimeException('اختر كلمة مرور مختلفة عن الافتراضية.');
        }

        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    }

    $check = db()->prepare(sprintf('SELECT COUNT(*) FROM %s WHERE username = :username AND id != :id', APP_DB_TABLE_ADMINS));
    $check->execute([':username' => $username, ':id' => $id]);
    if ((int) $check->fetchColumn() > 0) {
        throw new RuntimeException('اسم المستخدم مستخدم بالفعل.');
    }

    $sql = sprintf('UPDATE %s SET username = :username, full_name = :full_name', APP_DB_TABLE_ADMINS);
    $params = [
        ':username' => $username,
        ':full_name' => $fullName,
        ':id' => $id,
    ];

    if ($passwordHash !== null) {
        $sql .= ', password_hash = :password_hash';
        $params[':password_hash'] = $passwordHash;
    }

    $sql .= ' WHERE id = :id';
    $update = db()->prepare($sql);
    $update->execute($params);

    $_SESSION['admin_username'] = $username;
    $_SESSION['admin_full_name'] = $fullName;
    $_SESSION['admin_force_update'] = false;
}

function save_version_record(array $payload, ?array $uploadedFile = null): int
{
    $pdo = db();
    $platform = normalize_platform((string) ($payload['platform'] ?? 'apk'));
    $version = trim((string) ($payload['version'] ?? ''));
    $updateNotes = trim((string) ($payload['update_notes'] ?? ''));
    $isLatest = !empty($payload['is_latest']) ? 1 : 0;
    $existingId = !empty($payload['id']) ? (int) $payload['id'] : 0;
    $fileName = $payload['file_name'] ?? null;
    $filePath = $payload['file_path'] ?? null;
    $fileSize = isset($payload['file_size']) ? (int) $payload['file_size'] : 0;
    $hasNewFile = !empty($payload['has_new_file']);

    if ($version === '') {
        throw new InvalidArgumentException('Version is required.');
    }

    if ($existingId > 0) {
        $current = get_version_by_id($existingId);

        if (!$current) {
            throw new RuntimeException('Version record not found.');
        }

        if ($uploadedFile === null && !$hasNewFile) {
            $fileName = (string) $current['file_name'];
            $filePath = (string) $current['file_path'];
            $fileSize = (int) $current['file_size'];

            if ((string) $current['platform'] !== $platform) {
                throw new RuntimeException('Changing platform requires uploading a new file with the matching extension.');
            }
        }
    }

    if ($uploadedFile !== null && ($uploadedFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $fileInfo = validate_uploaded_app_file($uploadedFile, $platform);
        $fileName = $fileInfo['original_name'];
        $filePath = $fileInfo['relative_path'];
        $fileSize = $fileInfo['size'];
    }

    if ($existingId > 0) {
        if ((($uploadedFile !== null && ($uploadedFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) || $hasNewFile) && !empty($current['file_path'])) {
            $absolute = dirname(__DIR__) . '/' . ltrim((string) $current['file_path'], '/');
            if (is_file($absolute)) {
                @unlink($absolute);
            }
        }

        $statement = $pdo->prepare(sprintf(
            'UPDATE %s SET version = :version, update_notes = :update_notes, file_name = :file_name, file_path = :file_path, file_size = :file_size, platform = :platform WHERE id = :id',
            APP_DB_TABLE_VERSIONS
        ));
        $statement->execute([
            ':version' => $version,
            ':update_notes' => $updateNotes,
            ':file_name' => $fileName,
            ':file_path' => $filePath,
            ':file_size' => $fileSize,
            ':platform' => $platform,
            ':id' => $existingId,
        ]);

        if ($isLatest) {
            set_latest_version($existingId, $platform);
        }

        return $existingId;
    }

    $statement = $pdo->prepare(sprintf(
        'INSERT INTO %s (version, update_notes, file_name, file_path, file_size, platform, is_latest) VALUES (:version, :update_notes, :file_name, :file_path, :file_size, :platform, :is_latest)',
        APP_DB_TABLE_VERSIONS
    ));
    $statement->execute([
        ':version' => $version,
        ':update_notes' => $updateNotes,
        ':file_name' => $fileName,
        ':file_path' => $filePath,
        ':file_size' => $fileSize,
        ':platform' => $platform,
        ':is_latest' => $isLatest,
    ]);

    $newId = (int) $pdo->lastInsertId();

    if ($isLatest || !has_latest_version($platform)) {
        set_latest_version($newId, $platform);
    }

    return $newId;
}

function upload_error_message(int $error): string
{
    $uploadMaxRaw = (string) ini_get('upload_max_filesize');
    $postMaxRaw = (string) ini_get('post_max_size');
    $uploadMaxLabel = format_ini_limit($uploadMaxRaw);
    $postMaxLabel = format_ini_limit($postMaxRaw);
    $effectiveBytes = effective_upload_limit_bytes();
    $effectiveLabel = $effectiveBytes > 0 ? format_bytes($effectiveBytes) : 'غير محدود';

    return match ($error) {
        UPLOAD_ERR_INI_SIZE => 'حجم الملف أكبر من حد السيرفر. upload_max_filesize=' . $uploadMaxLabel . '، post_max_size=' . $postMaxLabel . '. الحد الفعلي: ' . $effectiveLabel . '.',
        UPLOAD_ERR_FORM_SIZE => 'حجم الملف أكبر من الحد المسموح في النموذج.',
        UPLOAD_ERR_PARTIAL => 'تم رفع الملف جزئيًا فقط. تأكد من الاتصال وحدود الاستضافة.',
        UPLOAD_ERR_NO_FILE => 'لم يتم اختيار ملف للرفع.',
        UPLOAD_ERR_NO_TMP_DIR => 'مجلد الملفات المؤقتة غير موجود على السيرفر.',
        UPLOAD_ERR_CANT_WRITE => 'فشل حفظ الملف على القرص.',
        UPLOAD_ERR_EXTENSION => 'إضافة في السيرفر أوقفت عملية الرفع.',
        default => 'فشل رفع الملف. كود الخطأ: ' . $error . '.',
    };
}

function validate_uploaded_app_file(array $file, string $platform): array
{
    ensure_upload_directory();

    $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException(upload_error_message($error));
    }

    $size = (int) ($file['size'] ?? 0);
    if ($size <= 0) {
        throw new RuntimeException('الملف المرفوع فارغ.');
    }

    if ($size > app_upload_max_bytes()) {
        throw new RuntimeException('حجم الملف أكبر من الحد المسموح في إعدادات التطبيق (' . APP_MAX_UPLOAD_MB . ' MB).');
    }

    $originalName = (string) ($file['name'] ?? 'app-file');
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($extension, APP_ALLOWED_EXTENSIONS, true) || $extension !== $platform) {
        throw new RuntimeException('الصيغة غير مدعومة. الرجاء رفع ملف .' . $platform . ' فقط.');
    }

    $tmpPath = (string) ($file['tmp_name'] ?? '');
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($tmpPath) ?: 'application/octet-stream';
    $allowedMimes = APP_ALLOWED_MIME_TYPES[$platform];

    if (!in_array($mimeType, $allowedMimes, true)) {
        throw new RuntimeException('نوع الملف غير مسموح به.');
    }

    $safeName = preg_replace('/[^A-Za-z0-9._-]+/', '-', pathinfo($originalName, PATHINFO_FILENAME));
    $safeName = trim((string) $safeName, '-_.');
    if ($safeName === '') {
        $safeName = 'app-file';
    }

    $folder = APP_UPLOAD_DIRECTORY . '/' . $platform;
    if (!is_dir($folder)) {
        mkdir($folder, 0775, true);
    }

    $storedName = $safeName . '-' . date('YmdHis') . '.' . $extension;
    $absolutePath = $folder . '/' . $storedName;

    if (!move_uploaded_file($tmpPath, $absolutePath)) {
        throw new RuntimeException('تعذر حفظ الملف بعد الرفع.');
    }

    return [
        'original_name' => $originalName,
        'relative_path' => 'uploads/' . $platform . '/' . $storedName,
        'size' => $size,
        'mime' => $mimeType,
    ];
}

function get_version_by_id(int $id): ?array
{
    $statement = db()->prepare(sprintf('SELECT * FROM %s WHERE id = :id LIMIT 1', APP_DB_TABLE_VERSIONS));
    $statement->execute([':id' => $id]);
    $row = $statement->fetch();

    return $row ?: null;
}

function get_latest_versions(): array
{
    $statement = db()->query(sprintf('SELECT * FROM %s ORDER BY is_latest DESC, created_at DESC, id DESC', APP_DB_TABLE_VERSIONS));
    return $statement->fetchAll() ?: [];
}

function get_latest_version_by_platform(string $platform): ?array
{
    $platform = normalize_platform($platform);
    $statement = db()->prepare(sprintf(
        'SELECT * FROM %s WHERE platform = :platform ORDER BY is_latest DESC, created_at DESC, id DESC LIMIT 1',
        APP_DB_TABLE_VERSIONS
    ));
    $statement->execute([':platform' => $platform]);
    $row = $statement->fetch();

    return $row ?: null;
}

function has_latest_version(string $platform): bool
{
    $platform = normalize_platform($platform);
    $statement = db()->prepare(sprintf('SELECT COUNT(*) FROM %s WHERE platform = :platform AND is_latest = 1', APP_DB_TABLE_VERSIONS));
    $statement->execute([':platform' => $platform]);

    return (int) $statement->fetchColumn() > 0;
}

function set_latest_version(int $id, string $platform): void
{
    $platform = normalize_platform($platform);
    $pdo = db();

    $pdo->prepare(sprintf('UPDATE %s SET is_latest = 0 WHERE platform = :platform', APP_DB_TABLE_VERSIONS))->execute([
        ':platform' => $platform,
    ]);

    $pdo->prepare(sprintf('UPDATE %s SET is_latest = 1 WHERE id = :id', APP_DB_TABLE_VERSIONS))->execute([
        ':id' => $id,
    ]);
}

function delete_version_record(int $id): bool
{
    $version = get_version_by_id($id);
    if (!$version) {
        return false;
    }

    $absolute = dirname(__DIR__) . '/' . ltrim((string) $version['file_path'], '/');
    if (!empty($version['file_path']) && is_file($absolute)) {
        @unlink($absolute);
    }

    $statement = db()->prepare(sprintf('DELETE FROM %s WHERE id = :id', APP_DB_TABLE_VERSIONS));
    $statement->execute([':id' => $id]);

    if ((int) $version['is_latest'] === 1) {
        $replacement = db()->prepare(sprintf(
            'SELECT id FROM %s WHERE platform = :platform ORDER BY created_at DESC, id DESC LIMIT 1',
            APP_DB_TABLE_VERSIONS
        ));
        $replacement->execute([':platform' => (string) $version['platform']]);
        $replacementId = (int) $replacement->fetchColumn();
        if ($replacementId > 0) {
            set_latest_version($replacementId, (string) $version['platform']);
        }
    }

    return true;
}
