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

render_public_header('الشروط والأحكام', 'شروط وأحكام استخدام تطبيق سوق التل والخدمات المقدمة.');
?>
<div class="bg-noise min-h-screen">
    <main class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <section class="space-y-8">
            <div class="text-center text-white">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-300">Terms & Conditions</p>
                <h1 class="mt-3 text-4xl font-black sm:text-5xl">الشروط والأحكام</h1>
                <p class="mt-4 text-base text-slate-300">آخر تحديث: 1 فبراير 2026</p>
            </div>

            <div class="rounded-3xl border border-amber-400/20 bg-amber-400/10 p-6 text-slate-200">
                <div class="flex items-start gap-3">
                    <span class="text-2xl">⚠️</span>
                    <p class="leading-8">باستخدامك لتطبيق &quot;سوق التل&quot; فإنك توافق على الالتزام بهذه الشروط والأحكام. يرجى قراءتها بعناية قبل استخدام خدماتنا.</p>
                </div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">1</span>
                    <h2 class="text-xl font-bold text-white">التعريفات</h2>
                </div>
                <p class="mt-3 leading-8">في هذه الشروط والأحكام، تشير المصطلحات التالية إلى:</p>
                <ul class="mt-3 list-disc space-y-2 pr-5 leading-8">
                    <li>&quot;التطبيق&quot;: تطبيق سوق التل للهواتف المحمولة.</li>
                    <li>&quot;الخدمة&quot;: جميع الخدمات المقدمة عبر التطبيق بما في ذلك البيع والتوصيل.</li>
                    <li>&quot;المستخدم&quot;: أي شخص يقوم بتسجيل حساب واستخدام التطبيق.</li>
                    <li>&quot;التاجر&quot;: أي بائع مسجل يعرض منتجاته عبر التطبيق.</li>
                    <li>&quot;الكابتن&quot;: مسؤول التوصيل المعتمد لدينا.</li>
                    <li>&quot;نحن&quot; أو &quot;الشركة&quot;: إدارة تطبيق سوق التل.</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">2</span>
                    <h2 class="text-xl font-bold text-white">شروط الاستخدام</h2>
                </div>
                <ul class="mt-3 list-disc space-y-2 pr-5 leading-8">
                    <li>يجب أن يكون عمرك 18 عامًا على الأقل لاستخدام التطبيق.</li>
                    <li>يجب تقديم معلومات صحيحة ودقيقة عند التسجيل.</li>
                    <li>أنت مسؤول عن الحفاظ على سرية بيانات حسابك.</li>
                    <li>يُحظر استخدام التطبيق لأي أغراض غير قانونية.</li>
                    <li>يُحظر إنشاء أكثر من حساب لنفس الشخص.</li>
                    <li>يجب الالتزام بالقوانين المحلية السارية عند استخدام الخدمة.</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">3</span>
                    <h2 class="text-xl font-bold text-white">الطلبات والشراء</h2>
                </div>
                <ul class="mt-3 list-disc space-y-2 pr-5 leading-8">
                    <li>جميع الأسعار المعروضة بالجنيه المصري وتشمل الضريبة (إن وجدت).</li>
                    <li>يحق لنا رفض أو إلغاء أي طلب لأسباب مشروعة.</li>
                    <li>يتم تأكيد الطلب بعد التحقق من توفر المنتج وصحة البيانات.</li>
                    <li>قد تختلف الأسعار وتوفر المنتجات دون إشعار مسبق.</li>
                    <li>رسوم التوصيل تُحسب حسب المنطقة وحجم الطلب ويتم عرضها قبل التأكيد.</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">4</span>
                    <h2 class="text-xl font-bold text-white">التوصيل</h2>
                </div>
                <ul class="mt-3 list-disc space-y-2 pr-5 leading-8">
                    <li>نسعى لتوصيل الطلبات في الوقت المحدد، لكن قد تحدث تأخيرات بسبب ظروف خارجة عن إرادتنا.</li>
                    <li>يجب أن يكون عنوان التوصيل ضمن نطاق الخدمة.</li>
                    <li>يجب تواجد المستلم في العنوان المحدد وقت التوصيل.</li>
                    <li>في حال عدم تواجد المستلم، سيتم محاولة التواصل وقد يتم إرجاع الطلب.</li>
                    <li>لا نتحمل مسؤولية التأخير الناتج عن بيانات توصيل خاطئة.</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">5</span>
                    <h2 class="text-xl font-bold text-white">المدفوعات</h2>
                </div>
                <ul class="mt-3 list-disc space-y-2 pr-5 leading-8">
                    <li>نقبل الدفع عند الاستلام والدفع الإلكتروني.</li>
                    <li>جميع المعاملات المالية مشفرة وآمنة.</li>
                    <li>في حال فشل الدفع الإلكتروني، قد يتم إلغاء الطلب تلقائيًا.</li>
                    <li>لا نحتفظ ببيانات بطاقات الدفع الخاصة بك على خوادمنا.</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">6</span>
                    <h2 class="text-xl font-bold text-white">حقوق الملكية الفكرية</h2>
                </div>
                <p class="mt-3 leading-8">جميع المحتويات المعروضة في التطبيق بما في ذلك النصوص والصور والشعارات والتصميمات هي ملكية حصرية لسوق التل أو مرخصة لنا. يُحظر نسخ أو إعادة إنتاج أو توزيع أي محتوى دون إذن كتابي مسبق.</p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">7</span>
                    <h2 class="text-xl font-bold text-white">المسؤولية والضمانات</h2>
                </div>
                <ul class="mt-3 list-disc space-y-2 pr-5 leading-8">
                    <li>نسعى لضمان دقة المعلومات المعروضة لكن لا نضمن خلوها من الأخطاء.</li>
                    <li>التاجر مسؤول عن جودة المنتجات المعروضة ومطابقتها للوصف.</li>
                    <li>لا نتحمل مسؤولية الأضرار غير المباشرة الناتجة عن استخدام الخدمة.</li>
                    <li>نحتفظ بالحق في تعليق أو إنهاء حسابك في حال مخالفة الشروط.</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">8</span>
                    <h2 class="text-xl font-bold text-white">الكوبونات والعروض</h2>
                </div>
                <ul class="mt-3 list-disc space-y-2 pr-5 leading-8">
                    <li>الكوبونات لها تاريخ صلاحية محدد ولا يمكن تمديده.</li>
                    <li>لا يمكن الجمع بين أكثر من كوبون في طلب واحد إلا إذا ذُكر خلاف ذلك.</li>
                    <li>يحق لنا إلغاء أي كوبون أو عرض في أي وقت.</li>
                    <li>الكوبونات غير قابلة للتحويل أو الاستبدال النقدي.</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">9</span>
                    <h2 class="text-xl font-bold text-white">تعديل الشروط والأحكام</h2>
                </div>
                <p class="mt-3 leading-8">نحتفظ بالحق في تعديل هذه الشروط والأحكام في أي وقت. سيتم إخطارك بأي تغييرات جوهرية عبر التطبيق أو البريد الإلكتروني. استمرارك في استخدام الخدمة بعد التعديل يعني موافقتك على الشروط الجديدة.</p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">10</span>
                    <h2 class="text-xl font-bold text-white">القانون الحاكم</h2>
                </div>
                <p class="mt-3 leading-8">تخضع هذه الشروط والأحكام لقوانين جمهورية مصر العربية. أي نزاع ينشأ عن استخدام الخدمة يخضع لاختصاص المحاكم المصرية المختصة.</p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-200">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/20 text-sm font-bold text-cyan-200">11</span>
                    <h2 class="text-xl font-bold text-white">التواصل والاستفسارات</h2>
                </div>
                <p class="mt-3 leading-8">لأي أسئلة أو استفسارات حول هذه الشروط والأحكام:</p>
                <div class="mt-3 space-y-1 text-slate-200">
                    <p>📧 البريد الإلكتروني: <a href="<?= escape($contactEmailUrl) ?>" class="underline hover:text-white"><?= escape($contactEmail) ?></a></p>
                    <p>📞 الهاتف: <a href="<?= escape($contactPhoneUrl) ?>" class="underline hover:text-white"><?= escape($contactPhone) ?></a></p>
                    <p>🌐 الموقع الإلكتروني: <a href="<?= escape($contactWebsiteUrl) ?>" class="underline hover:text-white" target="_blank" rel="noopener"><?= escape($contactWebsite) ?></a></p>
                    <p>🏢 العنوان: <?= escape($contactAddress) ?></p>
                </div>
            </div>

            <div class="rounded-3xl border border-emerald-400/20 bg-emerald-400/10 p-6 text-slate-200">
                <div class="flex items-start gap-3">
                    <span class="text-2xl">✅</span>
                    <p class="leading-8">باستخدامك للتطبيق فإنك تقر بقراءة وفهم وموافقتك على جميع الشروط والأحكام المذكورة أعلاه.</p>
                </div>
            </div>
        </section>
    </main>
</div>
<?php render_public_footer(); ?>
