<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

if (!is_post()) {
	flash('error', 'لا يمكن تسجيل الخروج بهذه الطريقة.');
	redirect('admin');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
	flash('error', 'رمز الحماية غير صالح.');
	redirect('admin');
}

logout_admin();
flash('success', 'تم تسجيل الخروج بنجاح.');
redirect('admin/login');
