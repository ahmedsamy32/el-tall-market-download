<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$contactEmail = get_setting('contact_email', 'support@elltall.com');
$contactPhone = get_setting('contact_phone', '+20 123 456 7890');
$contactWebsite = get_setting('contact_website', 'www.elltall.com');
$contactAddress = get_setting('contact_address', 'التل الكبير - مصر');
$contactEmailUrl = 'mailto:' . $contactEmail;
$contactPhoneUrl = 'tel:' . preg_replace('~[^0-9+]~', '', $contactPhone);
$contactWebsiteUrl = preg_match('~^https?://~i', $contactWebsite) ? $contactWebsite : 'https://' . $contactWebsite;

render_public_header('سياسة الخصوصية', 'سياسة الخصوصية لتطبيق سوق التل وكيفية استخدام وحماية بياناتك.');
?>
<div class="bg-noise min-h-screen">
    <main class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <section class="space-y-8">
            <div class="text-center text-white">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-300">Privacy Policy</p>
                <h1 class="mt-3 text-4xl font-black sm:text-5xl">سياسة الخصوصية</h1>
                <p class="mt-4 text-base text-slate-300">آخر تحديث: 1 فبراير 2026</p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">1</span>
                    <h2 class="text-xl font-bold text-white">مقدمة</h2>
                </div>
                <p class="mt-3 leading-8">نحن في "سوق التل" نقدّر خصوصيتك ونلتزم بحماية بياناتك الشخصية. توضح سياسة الخصوصية هذه كيف نجمع معلوماتك ونستخدمها ونحميها عند استخدامك لتطبيقنا وخدماتنا.</p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">2</span>
                    <h2 class="text-xl font-bold text-white">المعلومات التي نجمعها</h2>
                </div>
                <ul class="mt-3 list-disc space-y-2 pr-5 leading-8">
                    <li>معلومات الحساب: الاسم، البريد الإلكتروني، رقم الهاتف، وعنوان التوصيل.</li>
                    <li>معلومات الطلبات: تفاصيل المشتريات، تاريخ الطلب، وطريقة الدفع.</li>
                    <li>معلومات الموقع: لتقديم خدمة التوصيل وتحديد نطاق الخدمة.</li>
                    <li>معلومات الجهاز: نوع الجهاز، نظام التشغيل، ومعرّف الجهاز لتحسين الأداء.</li>
                    <li>بيانات الاستخدام: كيفية تفاعلك مع التطبيق لتحسين تجربة المستخدم.</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">3</span>
                    <h2 class="text-xl font-bold text-white">كيف نستخدم معلوماتك</h2>
                </div>
                <ul class="mt-3 list-disc space-y-2 pr-5 leading-8">
                    <li>معالجة وتنفيذ طلباتك وتوصيلها إليك.</li>
                    <li>التواصل معك بخصوص طلباتك وحسابك.</li>
                    <li>تحسين خدماتنا وتطوير ميزات جديدة.</li>
                    <li>إرسال عروض وتخفيضات مخصصة (بموافقتك).</li>
                    <li>الحفاظ على أمان التطبيق ومنع الاحتيال.</li>
                    <li>الامتثال للمتطلبات القانونية والتنظيمية.</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">4</span>
                    <h2 class="text-xl font-bold text-white">مشاركة المعلومات</h2>
                </div>
                <p class="mt-3 leading-8">لا نبيع أو نؤجر معلوماتك الشخصية لأي طرف ثالث. قد نشارك بعض المعلومات الضرورية مع:</p>
                <ul class="mt-3 list-disc space-y-2 pr-5 leading-8">
                    <li>شركاء التوصيل (الكابتن) لإيصال طلبك.</li>
                    <li>مزودي خدمات الدفع لمعالجة المدفوعات بأمان.</li>
                    <li>التجار المعنيين بطلبك فقط.</li>
                    <li>الجهات الحكومية عند الطلب القانوني.</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">5</span>
                    <h2 class="text-xl font-bold text-white">حماية البيانات</h2>
                </div>
                <p class="mt-3 leading-8">نستخدم أحدث تقنيات التشفير والأمان لحماية بياناتك الشخصية. يتم تخزين جميع البيانات على خوادم آمنة مع إجراءات حماية متعددة المستويات.</p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">6</span>
                    <h2 class="text-xl font-bold text-white">حقوقك</h2>
                </div>
                <p class="mt-3 leading-8">لديك الحق في:</p>
                <ul class="mt-3 list-disc space-y-2 pr-5 leading-8">
                    <li>الوصول إلى بياناتك الشخصية وطلب نسخة منها.</li>
                    <li>تصحيح أو تحديث معلوماتك الشخصية.</li>
                    <li>طلب حذف حسابك وبياناتك.</li>
                    <li>إلغاء الاشتراك من الإشعارات التسويقية.</li>
                    <li>تقييد معالجة بياناتك في حالات معينة.</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">7</span>
                    <h2 class="text-xl font-bold text-white">ملفات تعريف الارتباط (Cookies)</h2>
                </div>
                <p class="mt-3 leading-8">نستخدم ملفات تعريف الارتباط وتقنيات مشابهة لتحسين تجربتك وتقديم محتوى مخصص. يمكنك التحكم في إعدادات ملفات تعريف الارتباط من خلال إعدادات جهازك.</p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">8</span>
                    <h2 class="text-xl font-bold text-white">التواصل معنا</h2>
                </div>
                <p class="mt-3 leading-8">إذا كان لديك أي أسئلة أو استفسارات حول سياسة الخصوصية، يرجى التواصل معنا عبر:</p>
                <div class="mt-3 space-y-1 text-slate-200">
                    <p>📧 البريد الإلكتروني: <a href="<?= escape($contactEmailUrl) ?>" class="underline hover:text-white"><?= escape($contactEmail) ?></a></p>
                    <p>📞 الهاتف: <a href="<?= escape($contactPhoneUrl) ?>" class="underline hover:text-white"><?= escape($contactPhone) ?></a></p>
                    <p>🌐 الموقع الإلكتروني: <a href="<?= escape($contactWebsiteUrl) ?>" class="underline hover:text-white" target="_blank" rel="noopener"><?= escape($contactWebsite) ?></a></p>
                    <p>🏢 العنوان: <?= escape($contactAddress) ?></p>
                </div>
            </div>

            <div class="pt-6 text-center text-white">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-300">Return Policy</p>
                <h2 class="mt-3 text-3xl font-black sm:text-4xl">سياسة الاسترجاع والاستبدال</h2>
                <p class="mt-3 text-base text-slate-300">آخر تحديث: 1 فبراير 2026</p>
            </div>

            <div class="rounded-3xl border border-emerald-400/20 bg-emerald-400/10 p-6 text-slate-200">
                <p class="leading-8">نحرص في "سوق التل" على رضا عملائنا. إذا لم تكن راضيًا عن مشترياتك، يمكنك الاستفادة من سياسة الاسترجاع والاستبدال الخاصة بنا.</p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-500/20 text-sm font-bold text-emerald-200">1</span>
                    <h2 class="text-xl font-bold text-white">شروط الاسترجاع</h2>
                </div>
                <ul class="mt-3 list-disc space-y-2 pr-5 leading-8">
                    <li>يجب أن يكون المنتج في حالته الأصلية ولم يتم استخدامه.</li>
                    <li>يجب تقديم طلب الاسترجاع خلال 7 أيام من تاريخ الاستلام.</li>
                    <li>يجب إرفاق فاتورة الشراء أو رقم الطلب.</li>
                    <li>يجب أن يكون المنتج في عبوته الأصلية مع جميع الملحقات.</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-500/20 text-sm font-bold text-emerald-200">2</span>
                    <h2 class="text-xl font-bold text-white">المنتجات غير القابلة للاسترجاع</h2>
                </div>
                <p class="mt-3 leading-8">لا يمكن إرجاع المنتجات التالية:</p>
                <ul class="mt-3 list-disc space-y-2 pr-5 leading-8">
                    <li>المنتجات الغذائية القابلة للتلف والمواد سريعة الانتهاء.</li>
                    <li>المنتجات المخصصة أو المصنعة حسب الطلب.</li>
                    <li>منتجات العناية الشخصية المفتوحة لأسباب صحية.</li>
                    <li>المنتجات التي تم استخدامها أو تلفها بعد الاستلام.</li>
                    <li>البطاقات والقسائم الرقمية بعد تفعيلها.</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-500/20 text-sm font-bold text-emerald-200">3</span>
                    <h2 class="text-xl font-bold text-white">خطوات طلب الاسترجاع</h2>
                </div>
                <ol class="mt-3 list-decimal space-y-2 pr-5 leading-8">
                    <li>افتح التطبيق واذهب إلى "طلباتي السابقة".</li>
                    <li>اختر الطلب الذي تريد إرجاعه.</li>
                    <li>اضغط على "طلب استرجاع" وحدد سبب الإرجاع.</li>
                    <li>التقط صورًا واضحة للمنتج وأرفقها مع الطلب.</li>
                    <li>انتظر مراجعة طلبك من فريقنا خلال 24-48 ساعة.</li>
                    <li>في حال الموافقة، سيتم ترتيب استلام المنتج أو يمكنك إرساله.</li>
                </ol>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-500/20 text-sm font-bold text-emerald-200">4</span>
                    <h2 class="text-xl font-bold text-white">سياسة الاستبدال</h2>
                </div>
                <p class="mt-3 leading-8">يمكنك استبدال المنتج بمنتج آخر بنفس القيمة أو بفرق سعر يتم دفعه أو استرداده. تنطبق نفس شروط الاسترجاع على طلبات الاستبدال.</p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-500/20 text-sm font-bold text-emerald-200">5</span>
                    <h2 class="text-xl font-bold text-white">طريقة استرداد المبلغ</h2>
                </div>
                <ul class="mt-3 list-disc space-y-2 pr-5 leading-8">
                    <li>الدفع الإلكتروني: يتم الاسترداد إلى نفس وسيلة الدفع خلال 5-10 أيام عمل.</li>
                    <li>الدفع عند الاستلام: يتم الاسترداد إلى محفظتك في التطبيق أو عبر تحويل بنكي.</li>
                    <li>رسوم التوصيل: لا يتم استردادها إلا في حالة وجود خطأ من جانبنا.</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-500/20 text-sm font-bold text-emerald-200">6</span>
                    <h2 class="text-xl font-bold text-white">حالات الاسترجاع المجاني</h2>
                </div>
                <p class="mt-3 leading-8">نتحمل تكلفة الإرجاع في الحالات التالية:</p>
                <ul class="mt-3 list-disc space-y-2 pr-5 leading-8">
                    <li>استلام منتج خاطئ أو مختلف عن الطلب.</li>
                    <li>استلام منتج تالف أو معيب.</li>
                    <li>عدم مطابقة المنتج للوصف المعروض في التطبيق.</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-500/20 text-sm font-bold text-emerald-200">7</span>
                    <h2 class="text-xl font-bold text-white">التواصل لطلبات الاسترجاع</h2>
                </div>
                <p class="mt-3 leading-8">لأي استفسارات حول الاسترجاع والاستبدال:</p>
                <div class="mt-3 space-y-1 text-slate-200">
                    <p>📧 البريد الإلكتروني: <a href="<?= escape($contactEmailUrl) ?>" class="underline hover:text-white"><?= escape($contactEmail) ?></a></p>
                    <p>📞 الهاتف: <a href="<?= escape($contactPhoneUrl) ?>" class="underline hover:text-white"><?= escape($contactPhone) ?></a></p>
                    <p>🌐 الموقع الإلكتروني: <a href="<?= escape($contactWebsiteUrl) ?>" class="underline hover:text-white" target="_blank" rel="noopener"><?= escape($contactWebsite) ?></a></p>
                    <p>🏢 العنوان: <?= escape($contactAddress) ?></p>
                    <p>⏰ ساعات العمل: من السبت إلى الخميس، 9 صباحًا - 9 مساءً</p>
                </div>
            </div>
        </section>
    </main>
</div>
<?php render_public_footer(); ?>
