<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

function ensure_settings_table(): void
{
    $pdo = db();
    $pdo->exec(sprintf(
        'CREATE TABLE IF NOT EXISTS %s (
            setting_key VARCHAR(120) PRIMARY KEY,
            setting_value TEXT NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
        APP_DB_TABLE_SETTINGS
    ));
}

function get_setting(string $key, ?string $default = null): ?string
{
    ensure_settings_table();

    $statement = db()->prepare(sprintf('SELECT setting_value FROM %s WHERE setting_key = :key LIMIT 1', APP_DB_TABLE_SETTINGS));
    $statement->execute([':key' => $key]);
    $value = $statement->fetchColumn();

    if ($value === false) {
        return $default;
    }

    return (string) $value;
}

function set_setting(string $key, string $value): void
{
    ensure_settings_table();

    $statement = db()->prepare(sprintf(
        'INSERT INTO %s (setting_key, setting_value) VALUES (:key, :value)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP',
        APP_DB_TABLE_SETTINGS
    ));
    $statement->execute([
        ':key' => $key,
        ':value' => $value,
    ]);
}

function delete_setting(string $key): void
{
    ensure_settings_table();

    $statement = db()->prepare(sprintf('DELETE FROM %s WHERE setting_key = :key', APP_DB_TABLE_SETTINGS));
    $statement->execute([':key' => $key]);
}

function branding_upload_max_bytes(): int
{
    return APP_BRANDING_MAX_MB * 1024 * 1024;
}

function validate_branding_upload(array $file): array
{
    $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException('فشل رفع الملف.');
    }

    $size = (int) ($file['size'] ?? 0);
    if ($size <= 0) {
        throw new RuntimeException('الملف المرفوع فارغ.');
    }

    if ($size > branding_upload_max_bytes()) {
        throw new RuntimeException('حجم الملف أكبر من الحد المسموح.');
    }

    $originalName = (string) ($file['name'] ?? 'site-image');
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($extension, APP_BRANDING_ALLOWED_EXTENSIONS, true)) {
        throw new RuntimeException('صيغة الملف غير مدعومة.');
    }

    $tmpPath = (string) ($file['tmp_name'] ?? '');
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($tmpPath) ?: 'application/octet-stream';
    $allowedMimes = APP_BRANDING_ALLOWED_MIME_TYPES[$extension] ?? [];

    if (!empty($allowedMimes) && !in_array($mimeType, $allowedMimes, true)) {
        throw new RuntimeException('نوع الملف غير مسموح.');
    }

    $safeName = preg_replace('/[^A-Za-z0-9._-]+/', '-', pathinfo($originalName, PATHINFO_FILENAME));
    $safeName = trim((string) $safeName, '-_.');
    if ($safeName === '') {
        $safeName = 'site-image';
    }

    ensure_upload_directory();
    $folder = APP_UPLOAD_DIRECTORY . '/branding';
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
        'relative_path' => 'uploads/branding/' . $storedName,
        'size' => $size,
        'mime' => $mimeType,
    ];
}

function store_branding_upload(array $file, string $settingKey): string
{
    $info = validate_branding_upload($file);
    $oldPath = get_setting($settingKey);

    if ($oldPath && str_starts_with($oldPath, 'uploads/branding/')) {
        $absoluteOld = dirname(__DIR__) . '/' . ltrim($oldPath, '/');
        if (is_file($absoluteOld)) {
            @unlink($absoluteOld);
        }
    }

    set_setting($settingKey, $info['relative_path']);

    return $info['relative_path'];
}

function remove_branding_setting(string $settingKey): void
{
    $oldPath = get_setting($settingKey);
    if ($oldPath && str_starts_with($oldPath, 'uploads/branding/')) {
        $absoluteOld = dirname(__DIR__) . '/' . ltrim($oldPath, '/');
        if (is_file($absoluteOld)) {
            @unlink($absoluteOld);
        }
    }

    delete_setting($settingKey);
}

function site_logo_url(): string
{
    $path = get_setting('site_logo');
    if ($path) {
        return url(ltrim($path, '/'));
    }

    return asset('images/logo.svg');
}

function site_favicon_url(): string
{
    $path = get_setting('site_favicon');
    if ($path) {
        return url(ltrim($path, '/'));
    }

    return asset('images/logo.svg');
}

function site_banner_url(): ?string
{
    $path = get_setting('site_banner');
    if ($path) {
        return url(ltrim($path, '/'));
    }

    return null;
}

function site_download_banner_url(): ?string
{
    $path = get_setting('site_download_banner');
    if ($path) {
        return url(ltrim($path, '/'));
    }

    return null;
}

function site_banners(): array
{
    $value = get_setting('site_banners');
    if ($value) {
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded;
        }
    }
    
    // Fallback: migrate legacy site_banner if it exists
    $singlePath = get_setting('site_banner');
    if ($singlePath) {
        $singleLink = get_setting('site_banner_link', '');
        return [
            [
                'id' => 'legacy',
                'image' => $singlePath,
                'link' => $singleLink,
            ]
        ];
    }

    return [];
}

function add_site_banner(array $file, string $link): void
{
    $info = validate_branding_upload($file);
    $banners = site_banners();
    
    $banners[] = [
        'id' => uniqid('banner_'),
        'image' => $info['relative_path'],
        'link' => $link,
    ];
    
    set_setting('site_banners', json_encode($banners));
}

function remove_site_banner(string $id): void
{
    $banners = site_banners();
    $updated = [];
    foreach ($banners as $banner) {
        if ($banner['id'] === $id) {
            $path = $banner['image'];
            if ($path && str_starts_with($path, 'uploads/branding/')) {
                $absoluteOld = dirname(__DIR__) . '/' . ltrim($path, '/');
                if (is_file($absoluteOld)) {
                    @unlink($absoluteOld);
                }
            }
        } else {
            $updated[] = $banner;
        }
    }
    
    if ($id === 'legacy') {
        remove_branding_setting('site_banner');
        delete_setting('site_banner_link');
    }
    
    set_setting('site_banners', json_encode($updated));
}



