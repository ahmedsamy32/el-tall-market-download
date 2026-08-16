<?php

declare(strict_types=1);

require_once __DIR__ . '/app.php';

// عدّل بيانات قاعدة البيانات قبل الرفع إلى الاستضافة.
$envHost = getenv('DB_HOST');
$envName = getenv('DB_NAME');
$envUser = getenv('DB_USER');
$envPass = getenv('DB_PASS');
$envCharset = getenv('DB_CHARSET');

define('DB_HOST', ($envHost !== false && $envHost !== '') ? $envHost : 'localhost');
define('DB_NAME', ($envName !== false && $envName !== '') ? $envName : 'u299505697_eltalmarket');
define('DB_USER', ($envUser !== false && $envUser !== '') ? $envUser : 'u299505697_Ahmed');
define('DB_PASS', ($envPass !== false && $envPass !== '') ? $envPass : 'Ah01146668812');
define('DB_CHARSET', ($envCharset !== false && $envCharset !== '') ? $envCharset : 'utf8mb4');

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (Throwable $exception) {
        http_response_code(500);
        die('Database connection failed. Please check config/database.php settings.');
    }

    return $pdo;
}
