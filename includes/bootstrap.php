<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/layout.php';

start_app_session();
send_security_headers();

date_default_timezone_set(APP_TIMEZONE);
ensure_admin_seeded();
ensure_settings_table();
ensure_versions_table();
