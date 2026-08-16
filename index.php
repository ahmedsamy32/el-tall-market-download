<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$latestApk = get_latest_version_by_platform('apk');
$latestIpa = get_latest_version_by_platform('ipa');
$latestRelease = $latestApk ?? $latestIpa;

render_public_header('الصفحة الرئيسية', 'تحميل تطبيق سوق التل للأندرويد والآيفون مع أحدث الإصدارات والمزايا.');
?>
<div class="bg-noise min-h-screen">
    <?php 
    $banners = site_banners(); 
    if (!empty($banners)): 
    ?>
        <!-- Full-width Banner Carousel - Directly below header -->
        <section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-6" id="banner-section">
            <div class="relative overflow-hidden rounded-[2rem] border border-white/10 shadow-glow" id="homepage-carousel">
                <!-- Carousel Container -->
                <div class="carousel-container relative h-[280px] sm:h-[380px] lg:h-[500px] w-full overflow-hidden">
                    <?php foreach ($banners as $index => $banner): ?>
                        <?php 
                        $bannerUrl = url(ltrim($banner['image'], '/')); 
                        $isActive = $index === 0;
                        ?>
                        <div class="carousel-slide<?= $isActive ? ' active' : '' ?>" data-index="<?= $index ?>">
                            <?php if ($banner['link'] !== ''): ?>
                                <a href="<?= escape($banner['link']) ?>" class="relative block w-full h-full overflow-hidden" style="display: flex; align-items: center; justify-content: center;">
                            <?php else: ?>
                                <div class="relative w-full h-full overflow-hidden" style="display: flex; align-items: center; justify-content: center;">
                            <?php endif; ?>
                                
                                    <!-- Blurred background cover -->
                                    <div class="carousel-blur-bg" style="background-image: url('<?= escape($bannerUrl) ?>');"></div>
                                    
                                    <!-- Main sharp foreground image -->
                                    <img src="<?= escape($bannerUrl) ?>" alt="إعلان سوق التل" class="carousel-main-img">
                                
                            <?php if ($banner['link'] !== ''): ?>
                                </a>
                            <?php else: ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Navigation Controls (Arrows) -->
                <?php if (count($banners) > 1): ?>
                    <button id="carousel-prev" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 flex h-10 w-10 items-center justify-center rounded-full bg-slate-950/60 border border-white/10 text-white hover:bg-slate-950 hover:border-cyan-400/50 transition">
                        ❮
                    </button>
                    <button id="carousel-next" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 flex h-10 w-10 items-center justify-center rounded-full bg-slate-950/60 border border-white/10 text-white hover:bg-slate-950 hover:border-cyan-400/50 transition">
                        ❯
                    </button>

                    <!-- Indicators (Dots) -->
                    <div class="carousel-dots-wrapper">
                        <?php foreach ($banners as $index => $banner): ?>
                            <button class="carousel-dot<?= $index === 0 ? ' active' : '' ?>" data-index="<?= $index ?>"></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (count($banners) > 1): ?>
                <script nonce="<?= escape(csp_nonce()) ?>">
                    document.addEventListener('DOMContentLoaded', () => {
                        const carousel = document.getElementById('homepage-carousel');
                        if (!carousel) return;
                        
                        const slides = carousel.querySelectorAll('.carousel-slide');
                        const dots = carousel.querySelectorAll('.carousel-dot');
                        const prevBtn = carousel.querySelector('#carousel-prev');
                        const nextBtn = carousel.querySelector('#carousel-next');
                        
                        let currentIndex = 0;
                        let intervalId = null;
                        const duration = 5000; // 5 seconds autoplay
                        
                        function showSlide(index) {
                            slides.forEach((slide, idx) => {
                                if (idx === index) {
                                    slide.classList.add('active');
                                } else {
                                    slide.classList.remove('active');
                                }
                            });
                            
                            dots.forEach((dot, idx) => {
                                if (idx === index) {
                                    dot.classList.add('active');
                                } else {
                                    dot.classList.remove('active');
                                }
                            });
                            
                            currentIndex = index;
                        }
                        
                        function nextSlide() {
                            let next = (currentIndex + 1) % slides.length;
                            showSlide(next);
                        }
                        
                        function prevSlide() {
                            let prev = (currentIndex - 1 + slides.length) % slides.length;
                            showSlide(prev);
                        }
                        
                        function startAutoplay() {
                            stopAutoplay();
                            intervalId = setInterval(nextSlide, duration);
                        }
                        
                        function stopAutoplay() {
                            if (intervalId) clearInterval(intervalId);
                        }
                        
                        prevBtn.addEventListener('click', () => {
                            prevSlide();
                            startAutoplay();
                        });
                        
                        nextBtn.addEventListener('click', () => {
                            nextSlide();
                            startAutoplay();
                        });
                        
                        dots.forEach(dot => {
                            dot.addEventListener('click', () => {
                                const index = parseInt(dot.getAttribute('data-index'), 10);
                                showSlide(index);
                                startAutoplay();
                            });
                        });
                        
                        startAutoplay();
                    });
                </script>
            <?php endif; ?>

            <?php /* Auto-resize carousel height to match image aspect ratio */ ?>
            <script nonce="<?= escape(csp_nonce()) ?>">
                (function() {
                    var container = document.querySelector('#homepage-carousel .carousel-container');
                    var firstImg = document.querySelector('#homepage-carousel .carousel-slide img');
                    if (!container || !firstImg) return;

                    function adjustHeight() {
                        var naturalW = firstImg.naturalWidth;
                        var naturalH = firstImg.naturalHeight;
                        if (!naturalW || !naturalH) return;
                        var containerW = container.offsetWidth;
                        var newH = Math.round(containerW * naturalH / naturalW);
                        // Clamp between 140px and 500px
                        newH = Math.max(140, Math.min(500, newH));
                        container.style.height = newH + 'px';
                    }

                    if (firstImg.complete && firstImg.naturalWidth) {
                        adjustHeight();
                    } else {
                        firstImg.addEventListener('load', adjustHeight);
                    }

                    window.addEventListener('resize', adjustHeight);
                })();
            </script>
        </section>
    <?php endif; ?>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <section class="grid gap-6 lg:grid-cols-[1.08fr_0.92fr] lg:items-center">
            <div class="space-y-6 text-white">
                <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-cyan-400/10 px-4 py-2 text-sm font-semibold text-cyan-100" data-reveal>
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    أحدث نسخة متاحة الآن
                </div>
                <div data-reveal>
                    <h1 class="space-y-4 text-2xl font-black leading-[1.6] sm:text-3xl lg:text-5xl">
                        <span class="block">سوق التل هو تطبيقك</span>
                        <span class="block">الأمثل للتسوق الإلكتروني</span>
                    </h1>
                    <p class="mt-5 max-w-3xl text-base leading-9 text-slate-300 sm:text-lg">يوفر لك تجربة تسوق سهلة وممتعة مع مجموعة واسعة من المنتجات من أفضل المتاجر والتجار المحليين. نقدم لك خدمة توصيل سريعة وآمنة حتى باب منزلك.</p>
                </div>
                <div class="flex flex-wrap gap-3" data-reveal>
                    <a href="<?= escape(url('download')) ?>" class="rounded-2xl bg-cyan-400 px-5 py-3.5 text-sm font-black text-slate-950 transition hover:-translate-y-0.5">تحميل التطبيق</a>
                </div>
                <div class="flex flex-wrap gap-3" data-reveal>
                    <div class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200">تحديثات مباشرة</div>
                    <div class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200">تنزيل سريع</div>
                </div>
            </div>
            <div class="glass-card rounded-[2rem] p-5 text-slate-900 shadow-glow dark:text-white" data-reveal>
                <div class="rounded-[1.7rem] bg-white p-6 dark:bg-slate-950">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <img src="<?= escape(site_logo_url()) ?>" alt="سوق التل" class="h-16 w-16 rounded-3xl bg-slate-100 p-2 dark:bg-slate-900 sm:h-20 sm:w-20 lg:h-28 lg:w-28">
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 sm:text-sm"><?= escape(APP_NAME_EN) ?></p>
                                <h2 class="text-xl font-black sm:text-2xl">واجهة التحميل</h2>
                            </div>
                        </div>
                        <button type="button" class="theme-toggle inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200" data-theme-toggle>
                            <span class="theme-toggle-icon">☾</span>
                        </button>
                    </div>
                    <div class="mt-6 rounded-[1.5rem] bg-slate-950 p-5 text-white dark:bg-slate-900">
                        <p class="text-sm text-slate-300">آخر إصدار</p>
                        <div class="mt-3 flex items-end justify-between gap-4">
                            <div>
                                <p class="text-3xl font-black"><?= $latestRelease ? escape((string) $latestRelease['version']) : '—' ?></p>
                                <p class="mt-1 text-sm text-slate-400"><?= $latestRelease ? escape(format_bytes((int) $latestRelease['file_size'])) : 'لم يتم نشر إصدار بعد' ?></p>
                            </div>
                            <span class="rounded-full bg-cyan-400 px-3 py-1 text-xs font-black text-slate-950">جاهز للتحميل</span>
                        </div>
                    </div>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <a href="<?= $latestApk ? escape(url((string) $latestApk['file_path'])) : '#' ?>" class="rounded-2xl bg-slate-950 px-4 py-3 text-center text-sm font-bold text-white dark:bg-cyan-500 dark:text-slate-950 <?= $latestApk ? '' : 'pointer-events-none opacity-40' ?>">تحميل Android</a>
                        <a href="<?= $latestIpa ? escape(url((string) $latestIpa['file_path'])) : '#' ?>" class="rounded-2xl border border-slate-200 px-4 py-3 text-center text-sm font-bold text-slate-700 dark:border-slate-800 dark:text-slate-200 <?= $latestIpa ? '' : 'pointer-events-none opacity-40' ?>">تحميل iPhone</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
<?php render_public_footer(); ?>
