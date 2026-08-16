<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$start = microtime(true);

if (!is_post()) {
    json_response([
        'success' => false,
        'message' => 'Method not allowed. Use POST with multipart/form-data.',
        'method' => (string) ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN'),
    ], 405);
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    json_response([
        'success' => false,
        'message' => 'Invalid CSRF token.',
    ], 419);
}

$file = $_FILES['probe_file'] ?? null;
if (!$file) {
    json_response([
        'success' => false,
        'message' => 'No file received in probe_file.',
        'content_length' => (int) ($_SERVER['CONTENT_LENGTH'] ?? 0),
    ], 422);
}

$error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
if ($error !== UPLOAD_ERR_OK) {
    json_response([
        'success' => false,
        'message' => upload_error_message($error),
        'error_code' => $error,
        'content_length' => (int) ($_SERVER['CONTENT_LENGTH'] ?? 0),
    ], 422);
}

$size = (int) ($file['size'] ?? 0);
$originalName = (string) ($file['name'] ?? '');
$tmpPath = (string) ($file['tmp_name'] ?? '');
$mime = 'application/octet-stream';

if ($tmpPath !== '' && is_file($tmpPath) && class_exists('finfo')) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $detected = $finfo->file($tmpPath);
    if ($detected) {
        $mime = $detected;
    }
}

ensure_upload_directory();
$probeDir = APP_UPLOAD_DIRECTORY . '/_probe';
if (!is_dir($probeDir)) {
    mkdir($probeDir, 0775, true);
}

$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
$storedName = 'probe-' . date('YmdHis') . '-' . bin2hex(random_bytes(4));
if ($extension !== '') {
    $storedName .= '.' . $extension;
}
$absolutePath = $probeDir . '/' . $storedName;

$storedTemp = false;
if ($tmpPath !== '' && is_uploaded_file($tmpPath)) {
    $storedTemp = move_uploaded_file($tmpPath, $absolutePath);
    if ($storedTemp && is_file($absolutePath)) {
        @unlink($absolutePath);
    }
}

$effectiveBytes = effective_upload_limit_bytes();
$effectiveLabel = $effectiveBytes > 0 ? format_bytes($effectiveBytes) : 'غير محدود';
$durationMs = (int) round((microtime(true) - $start) * 1000);

json_response([
    'success' => true,
    'received' => [
        'name' => $originalName,
        'size_bytes' => $size,
        'size_label' => format_bytes($size),
        'mime' => $mime,
    ],
    'server' => [
        'content_length' => (int) ($_SERVER['CONTENT_LENGTH'] ?? 0),
        'upload_max_filesize' => format_ini_limit((string) ini_get('upload_max_filesize')),
        'post_max_size' => format_ini_limit((string) ini_get('post_max_size')),
        'effective_limit' => $effectiveLabel,
        'upload_tmp_dir' => (string) (ini_get('upload_tmp_dir') ?: sys_get_temp_dir()),
    ],
    'stored_temp' => $storedTemp,
    'duration_ms' => $durationMs,
]);
