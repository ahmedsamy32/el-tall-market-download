<?php

declare(strict_types=1);

// إعدادات التطبيق العامة.
define('APP_NAME', 'سوق التل');
define('APP_NAME_EN', 'El tall market');
define('APP_DESCRIPTION', 'التطبيق الرسمي لسوق التل للتسوق والطلبات بسهولة وأمان.');
define('APP_BASE_URL', 'https://eltal-market.com');
define('APP_TIMEZONE', 'Africa/Cairo');
define('APP_MAX_UPLOAD_MB', 1024);
define('APP_ALLOWED_EXTENSIONS', ['apk', 'ipa']);
define('APP_ALLOWED_MIME_TYPES', [
    'apk' => ['application/vnd.android.package-archive', 'application/octet-stream'],
    'ipa' => ['application/octet-stream', 'application/zip', 'application/x-itunes-ipa'],
]);
define('APP_BRANDING_MAX_MB', 40);
define('APP_BRANDING_ALLOWED_EXTENSIONS', ['png', 'jpg', 'jpeg', 'webp', 'svg', 'ico']);
define('APP_BRANDING_ALLOWED_MIME_TYPES', [
    'png' => ['image/png'],
    'jpg' => ['image/jpeg'],
    'jpeg' => ['image/jpeg'],
    'webp' => ['image/webp'],
    'svg' => ['image/svg+xml'],
    'ico' => ['image/x-icon', 'image/vnd.microsoft.icon'],
]);
define('APP_DEFAULT_ADMIN_USERNAME', 'admin');
define('APP_DEFAULT_ADMIN_PASSWORD', 'ChangeMe@123');
define('APP_DEFAULT_ADMIN_FULLNAME', 'Site Administrator');
define('APP_UPLOAD_DIRECTORY', dirname(__DIR__) . '/uploads');
define('APP_UPLOAD_PUBLIC_PATH', 'uploads');
define('APP_DB_TABLE_VERSIONS', 'app_versions');
define('APP_DB_TABLE_ADMINS', 'admin_users');
define('APP_DB_TABLE_SETTINGS', 'app_settings');
