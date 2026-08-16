<?php

declare(strict_types=1);

function start_app_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $secure = is_https_request();
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => $cookieParams['lifetime'] ?? 0,
        'path' => $cookieParams['path'] ?? '/',
        'domain' => $cookieParams['domain'] ?? '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('souk_altal_admin');
    session_start();
}

function escape(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function is_https_request(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }

    $forwarded = (string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '');
    if ($forwarded !== '') {
        $proto = strtolower(trim(explode(',', $forwarded)[0]));
        return $proto === 'https';
    }

    return false;
}

function app_origin(): string
{
    if (defined('APP_BASE_URL') && APP_BASE_URL !== '') {
        return rtrim((string) APP_BASE_URL, '/');
    }

    $scheme = is_https_request() ? 'https' : 'http';
    $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');

    return $scheme . '://' . $host;
}

function absolute_url(string $pathOrUrl): string
{
    if (preg_match('~^https?://~i', $pathOrUrl)) {
        return $pathOrUrl;
    }

    return app_origin() . '/' . ltrim($pathOrUrl, '/');
}

function current_request_path(): string
{
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $path = (string) (parse_url($uri, PHP_URL_PATH) ?? '/');

    return $path === '' ? '/' : $path;
}

function current_url(): string
{
    return absolute_url(current_request_path());
}

function csp_nonce(): string
{
    static $nonce = null;
    if ($nonce === null) {
        $nonce = base64_encode(random_bytes(16));
    }

    return $nonce;
}

function send_security_headers(): void
{
    if (headers_sent()) {
        return;
    }

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()');
    header('Cross-Origin-Resource-Policy: same-origin');
    header('Cross-Origin-Opener-Policy: same-origin');

    if (is_https_request()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }

    $nonce = csp_nonce();
    $csp = [
        "default-src 'self'",
        "base-uri 'self'",
        "frame-ancestors 'none'",
        "form-action 'self'",
        "object-src 'none'",
        "img-src 'self' data:",
        "font-src 'self' https://fonts.gstatic.com",
        "style-src 'self' https://fonts.googleapis.com",
        "script-src 'self' 'nonce-{$nonce}'",
        "connect-src 'self'",
        'upgrade-insecure-requests',
    ];
    header('Content-Security-Policy: ' . implode('; ', $csp));
}

function client_ip(): string
{
    $forwarded = (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '');
    if ($forwarded !== '') {
        $parts = array_filter(array_map('trim', explode(',', $forwarded)));
        if ($parts) {
            return (string) $parts[0];
        }
    }

    return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

function rate_limit_key(string $prefix, string $identifier): string
{
    return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
        . 'souk_altal_' . $prefix . '_' . sha1($identifier) . '.json';
}

function rate_limit_retry_after(string $key): int
{
    if (!is_file($key)) {
        return 0;
    }

    $data = json_decode((string) file_get_contents($key), true);
    if (!is_array($data)) {
        return 0;
    }

    $lockedUntil = (int) ($data['locked_until'] ?? 0);
    $now = time();

    return $lockedUntil > $now ? $lockedUntil - $now : 0;
}

function rate_limit_register_failure(string $key, int $maxAttempts, int $windowSeconds, int $lockSeconds): void
{
    $now = time();
    $data = [
        'count' => 0,
        'window_start' => $now,
        'locked_until' => 0,
    ];

    if (is_file($key)) {
        $loaded = json_decode((string) file_get_contents($key), true);
        if (is_array($loaded)) {
            $data = array_merge($data, $loaded);
        }
    }

    if ((int) $data['locked_until'] > $now) {
        return;
    }

    if ($now - (int) $data['window_start'] > $windowSeconds) {
        $data['count'] = 0;
        $data['window_start'] = $now;
    }

    $data['count'] = (int) $data['count'] + 1;

    if ($data['count'] >= $maxAttempts) {
        $data['locked_until'] = $now + $lockSeconds;
        $data['count'] = 0;
        $data['window_start'] = $now;
    }

    file_put_contents($key, json_encode($data), LOCK_EX);
}

function rate_limit_clear(string $key): void
{
    if (is_file($key)) {
        @unlink($key);
    }
}

function app_base_url(): string
{
    if (defined('APP_BASE_URL') && APP_BASE_URL !== '') {
        return rtrim((string) APP_BASE_URL, '/');
    }

    $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $scriptName = rtrim($scriptName, '/');

    if ($scriptName !== '' && $scriptName !== '/' && str_ends_with($scriptName, '/admin')) {
        $scriptName = rtrim(dirname($scriptName), '/');
    }

    return $scriptName === '' || $scriptName === '/' ? '' : $scriptName;
}

function url(string $path = ''): string
{
    $base = app_base_url();
    $path = ltrim($path, '/');

    return $base === '' ? '/' . $path : $base . '/' . $path;
}

function asset(string $path): string
{
    $path = ltrim($path, '/');
    $url = url('assets/' . $path);
    $absolute = dirname(__DIR__) . '/assets/' . $path;

    if (is_file($absolute)) {
        $url .= '?v=' . (string) filemtime($absolute);
    }

    return $url;
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function is_post(): bool
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $value = (string) $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return $value;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . escape(csrf_token()) . '">';
}

function verify_csrf_token(?string $token): bool
{
    return is_string($token) && hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token);
}

function format_bytes(int|string $bytes): string
{
    $bytes = (int) $bytes;
    $units = ['B', 'KB', 'MB', 'GB'];
    $index = 0;

    while ($bytes >= 1024 && $index < count($units) - 1) {
        $bytes /= 1024;
        $index++;
    }

    return number_format((float) $bytes, $index === 0 ? 0 : 2) . ' ' . $units[$index];
}

function ini_size_to_bytes(string $value): int
{
    $value = trim($value);
    if ($value === '' || $value === '-1') {
        return -1;
    }

    $unit = strtolower($value[strlen($value) - 1]);
    $number = (float) $value;

    if (!in_array($unit, ['g', 'm', 'k'], true)) {
        return (int) $number;
    }

    switch ($unit) {
        case 'g':
            $number *= 1024;
        case 'm':
            $number *= 1024;
        case 'k':
            $number *= 1024;
            break;
    }

    return (int) $number;
}

function format_ini_limit(string $value): string
{
    $bytes = ini_size_to_bytes($value);
    if ($bytes > 0) {
        return format_bytes($bytes);
    }

    $value = trim($value);
    if ($value === '' || $value === '-1') {
        return 'غير محدود';
    }

    return $value;
}

function effective_upload_limit_bytes(): int
{
    $uploadMax = ini_size_to_bytes((string) ini_get('upload_max_filesize'));
    $postMax = ini_size_to_bytes((string) ini_get('post_max_size'));

    $limits = array_filter([$uploadMax, $postMax], static fn (int $limit): bool => $limit > 0);

    return $limits ? min($limits) : -1;
}

function normalize_platform(string $platform): string
{
    $platform = strtolower(trim($platform));

    return in_array($platform, ['apk', 'ipa'], true) ? $platform : 'apk';
}

function app_upload_max_bytes(): int
{
    return APP_MAX_UPLOAD_MB * 1024 * 1024;
}

function list_upload_files(string $platform): array
{
    $platform = normalize_platform($platform);
    $folder = APP_UPLOAD_DIRECTORY . '/' . $platform;

    if (!is_dir($folder)) {
        return [];
    }

    $items = scandir($folder);
    if (!is_array($items)) {
        return [];
    }

    $files = [];

    foreach ($items as $name) {
        if ($name === '.' || $name === '..') {
            continue;
        }

        $path = $folder . '/' . $name;
        if (!is_file($path)) {
            continue;
        }

        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($extension, APP_ALLOWED_EXTENSIONS, true) || $extension !== $platform) {
            continue;
        }

        $size = (int) (filesize($path) ?: 0);
        $mtime = (int) (filemtime($path) ?: 0);

        $files[] = [
            'name' => $name,
            'relative_path' => 'uploads/' . $platform . '/' . $name,
            'size' => $size,
            'mtime' => $mtime,
            'label' => $name . ' • ' . format_bytes($size) . ' • ' . date('Y-m-d H:i', $mtime),
        ];
    }

    usort($files, static fn (array $a, array $b): int => $b['mtime'] <=> $a['mtime']);

    return $files;
}

function resolve_existing_upload_file(string $relativePath, string $platform): array
{
    $relativePath = ltrim($relativePath, '/');
    $platform = normalize_platform($platform);
    $expectedPrefix = 'uploads/' . $platform . '/';

    if (!str_starts_with($relativePath, $expectedPrefix)) {
        throw new RuntimeException('الملف المختار لا يطابق المنصة المحددة.');
    }

    $absolute = dirname(__DIR__) . '/' . $relativePath;
    $absoluteReal = realpath($absolute);
    $baseReal = realpath(APP_UPLOAD_DIRECTORY . '/' . $platform);

    if ($absoluteReal === false || $baseReal === false) {
        throw new RuntimeException('تعذر الوصول للملف المختار.');
    }

    if (!str_starts_with($absoluteReal, rtrim($baseReal, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR)) {
        throw new RuntimeException('مسار الملف غير صالح.');
    }

    if (!is_file($absoluteReal)) {
        throw new RuntimeException('الملف المختار غير موجود.');
    }

    $extension = strtolower(pathinfo($absoluteReal, PATHINFO_EXTENSION));
    if (!in_array($extension, APP_ALLOWED_EXTENSIONS, true) || $extension !== $platform) {
        throw new RuntimeException('الصيغة غير مدعومة للمنصة المحددة.');
    }

    $size = (int) (filesize($absoluteReal) ?: 0);

    return [
        'file_name' => basename($absoluteReal),
        'file_path' => $relativePath,
        'file_size' => $size,
    ];
}

function ensure_upload_directory(): void
{
    if (!is_dir(APP_UPLOAD_DIRECTORY)) {
        mkdir(APP_UPLOAD_DIRECTORY, 0775, true);
    }
}

function wants_json(): bool
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function admin_nav_items(): array
{
    return [
        ['label' => 'Dashboard', 'icon' => 'grid', 'href' => url('admin')],
        ['label' => 'Add Version', 'icon' => 'plus', 'href' => url('admin/version')],
        ['label' => 'Download Page', 'icon' => 'download', 'href' => url('download')],
        ['label' => 'Account', 'icon' => 'user', 'href' => url('admin/account')],
        ['label' => 'بيانات التواصل', 'icon' => 'phone', 'href' => url('admin/contact')],
        ['label' => 'Branding', 'icon' => 'image', 'href' => url('admin/branding')],
        ['label' => 'تشخيص الرفع', 'icon' => 'shield', 'href' => url('admin/diagnostics')],
        ['label' => 'Logout', 'icon' => 'logout', 'href' => url('admin/logout'), 'method' => 'post'],
    ];
}

function icon_svg(string $name): string
{
    $icons = [
        'grid' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h6v6H4z"/><path d="M14 4h6v6h-6z"/><path d="M4 14h6v6H4z"/><path d="M14 14h6v6h-6z"/></svg>',
        'plus' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 5v14M5 12h14"/></svg>',
        'download' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M4 19h16"/></svg>',
        'logout' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M21 4v16"/></svg>',
        'user' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="8" r="4"/></svg>',
        'image' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m8 13 2.5-2.5 4 4 2.5-2.5 3 3"/><circle cx="9" cy="9" r="1"/></svg>',
        'phone' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.9 19.9 0 0 1-8.7-3.1 19.3 19.3 0 0 1-6-6 19.9 19.9 0 0 1-3.1-8.7A2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.6a2 2 0 0 1-.5 2.1L8 9a16 16 0 0 0 7 7l.6-1.1a2 2 0 0 1 2.1-.5c.8.3 1.7.5 2.6.6a2 2 0 0 1 1.7 1.9Z"/></svg>',
        'shield' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 5 6v6c0 5 3.5 8.5 7 9 3.5-.5 7-4 7-9V6z"/></svg>',
        'cloud' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 18h10a4 4 0 0 0 .5-8A6 6 0 0 0 6 8.5 3.5 3.5 0 0 0 7 18Z"/></svg>',
        'device' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="7" y="3" width="10" height="18" rx="2"/><path d="M11 18h2"/></svg>',
    ];

    return $icons[$name] ?? $icons['grid'];
}
