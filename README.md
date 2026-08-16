# سوق التل

مشروع ويب كامل للرفع المباشر على Hostinger Shared Hosting أو Business Hosting باستخدام PHP 8+ و MySQL و Tailwind CSS و JavaScript.

## الملفات الأساسية

- `index.php` الصفحة الرئيسية.
- `download.php` صفحة التحميل للمستخدمين.
- `admin/login.php` تسجيل دخول الإدارة.
- `admin/index.php` لوحة التحكم.
- `admin/version.php` إضافة أو تعديل إصدار.
- `upload.php` معالج الرفع والحذف وتعيين آخر إصدار.
- `database.sql` ملف قاعدة البيانات الجاهز للاستيراد.

## خطوات التثبيت

1. ارفع الملفات كلها إلى الاستضافة.
2. أنشئ قاعدة بيانات MySQL ثم استورد `database.sql`.
3. عدّل بيانات الاتصال داخل `config/database.php`.
4. افتح `admin/login.php` وسجّل دخولك.
5. بيانات الدخول الافتراضية هي:
   - اسم المستخدم: `admin`
   - كلمة المرور: `ChangeMe@123`

## ملاحظات

- الملفات المرفوعة تُخزن داخل مجلد `uploads`.
- الواجهة تعتمد على Tailwind CSS عبر CDN لتجنب أي build step.
- الحماية تشمل Session Authentication و CSRF وفلترة الامتدادات و MIME types.
- يمكن تعريف بيانات قاعدة البيانات عبر متغيرات البيئة: DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_CHARSET.
- يمكنك تحديث اسم المستخدم وكلمة المرور من صفحة `admin/account.php`.
- يمكنك رفع شعار الموقع وأيقونة المتصفح من صفحة `admin/branding.php`.
- عند استخدام كلمة المرور الافتراضية سيتم إجبارك على تغييرها قبل متابعة لوحة التحكم.
