/**
 * تطبيق سوق التل - JavaScript الرئيسي (معايير الحماية OWASP A03)
 */
(() => {
    const storageKey = 'souk_altal_theme';
    const root = document.documentElement;

    // دالة تنظيف الروابط ضد هجمات حقن الشيفرات (Anti-XSS Link Sanitization)
    const sanitizeSafeUrl = (url) => {
        if (!url) return '#';
        const trimmed = String(url).trim();
        if (/^(javascript:|data:(?!image\/)|vbscript:)/i.test(trimmed)) {
            console.warn("Blocked potentially insecure URL protocol:", trimmed);
            return '#';
        }
        return trimmed;
    };

    // 1. إدارة الوضع الليلي والنهاري (Theme Management)
    const applyTheme = (theme) => {
        if (theme === 'dark') {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
        try {
            localStorage.setItem(storageKey, theme);
        } catch (e) { }
        updateThemeIcons(theme);
    };

    const updateThemeIcons = (theme) => {
        document.querySelectorAll('.theme-toggle-icon').forEach(icon => {
            icon.textContent = theme === 'dark' ? '☀' : '☾';
        });
    };

    let storedTheme = null;
    try {
        storedTheme = localStorage.getItem(storageKey);
    } catch (e) { }

    const preferredTheme = storedTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    applyTheme(preferredTheme);

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-theme-toggle]');
        if (!button) return;
        const newTheme = root.classList.contains('dark') ? 'light' : 'dark';
        applyTheme(newTheme);
    });

    // 2. تفعيل تأثيرات الظهور (Scroll Reveal Animations)
    document.querySelectorAll('[data-reveal]').forEach((element, index) => {
        element.style.animationDelay = `${index * 75}ms`;
        element.classList.add('fade-up');
    });

    // 3. حقن البيانات المركزية في عناصر الصفحة (Dynamic Data Hydration with OWASP Sanitization)
    const hydrateData = () => {
        if (typeof window.siteConfig === 'undefined') return;
        const cfg = window.siteConfig;

        // روابط التحميل والإصدارات
        document.querySelectorAll('[data-bind-version]').forEach(el => el.textContent = cfg.release.version || '1.0.0');
        document.querySelectorAll('[data-bind-filesize]').forEach(el => el.textContent = cfg.release.fileSize || '');
        document.querySelectorAll('[data-bind-releasedate]').forEach(el => el.textContent = cfg.release.releaseDate || '');

        document.querySelectorAll('[data-apk-url]').forEach(el => {
            if (cfg.release && cfg.release.apkUrl && cfg.release.apkUrl !== '#') {
                el.setAttribute('href', sanitizeSafeUrl(cfg.release.apkUrl));
            }
        });
        document.querySelectorAll('[data-ipa-url]').forEach(el => {
            if (cfg.release && cfg.release.ipaUrl && cfg.release.ipaUrl !== '#') {
                el.setAttribute('href', sanitizeSafeUrl(cfg.release.ipaUrl));
            }
        });

        // الشعار (Logo) والأيقونات والـ Favicon في جميع أنحاء الموقع
        if (cfg.logoUrl) {
            const safeLogo = sanitizeSafeUrl(cfg.logoUrl);
            document.querySelectorAll('img[src*="logo.svg"], img[data-bind-logo], .site-logo-img, .logo-img').forEach(img => {
                img.src = safeLogo;
            });
            // تحديث Favicon المتصفح
            document.querySelectorAll('link[rel="icon"], link[rel="apple-touch-icon"]').forEach(link => {
                link.href = safeLogo;
            });
        }

        // بانر صفحة التحميل
        if (cfg.downloadBanner && cfg.downloadBanner.image) {
            const dlBannerImg = document.querySelector('.download-banner-wrapper img');
            if (dlBannerImg) dlBannerImg.src = sanitizeSafeUrl(cfg.downloadBanner.image);
        }

        // روابط التواصل
        document.querySelectorAll('[data-bind-email]').forEach(el => {
            el.textContent = cfg.contact.email || '';
            if (el.tagName === 'A' && cfg.contact.email) el.setAttribute('href', 'mailto:' + cfg.contact.email);
        });
        document.querySelectorAll('[data-bind-phone]').forEach(el => {
            el.textContent = cfg.contact.phone || '';
            if (el.tagName === 'A' && cfg.contact.phone) el.setAttribute('href', 'tel:' + cfg.contact.phone.replace(/[^0-9+]/g, ''));
        });
        document.querySelectorAll('[data-bind-website]').forEach(el => {
            el.textContent = cfg.contact.website || '';
            if (el.tagName === 'A' && cfg.contact.website) el.setAttribute('href', sanitizeSafeUrl(cfg.contact.website));
        });
        document.querySelectorAll('[data-bind-address]').forEach(el => {
            el.textContent = cfg.contact.address || '';
        });

        // روابط السوشيال ميديا
        if (cfg.social) {
            document.querySelectorAll('[data-social-facebook]').forEach(el => el.setAttribute('href', sanitizeSafeUrl(cfg.social.facebook)));
            document.querySelectorAll('[data-social-instagram]').forEach(el => el.setAttribute('href', sanitizeSafeUrl(cfg.social.instagram)));
            document.querySelectorAll('[data-social-tiktok]').forEach(el => el.setAttribute('href', sanitizeSafeUrl(cfg.social.tiktok)));
        }

        // حقن صور سلايدر البانرات ديناميكياً بأمان
        const carousel = document.getElementById('homepage-carousel');
        if (carousel && cfg.banners && cfg.banners.length > 0) {
            const container = carousel.querySelector('.carousel-container');
            if (container) {
                container.innerHTML = cfg.banners.map((b, idx) => {
                    const safeLink = sanitizeSafeUrl(b.link || 'download.html');
                    const safeImg = sanitizeSafeUrl(b.image);
                    const safeAlt = String(b.alt || 'إعلان تطبيق سوق التل').replace(/[&<>"']/g, '');
                    return `
                    <div class="carousel-slide ${idx === 0 ? 'active' : ''}" data-index="${idx}">
                        <a href="${safeLink}" class="block w-full h-full">
                            <img src="${safeImg}" alt="${safeAlt}" class="carousel-main-img">
                        </a>
                    </div>
                `}).join('');
                initCarousel();
            }
        }
    };

    // إتاحة الدالة للتحديث غير المتزامن عند اكتمال تحميل IndexedDB
    window.rehydrateSiteData = hydrateData;

    // 4. تشغيل السلايدر التفاعلي (Carousel Logic)
    let carouselInterval = null;
    const initCarousel = () => {
        const carousel = document.getElementById('homepage-carousel');
        if (!carousel) return;

        const slides = carousel.querySelectorAll('.carousel-slide');
        const dots = carousel.querySelectorAll('.carousel-dot');
        const prevBtn = carousel.querySelector('#carousel-prev');
        const nextBtn = carousel.querySelector('#carousel-next');

        if (slides.length <= 1) return;

        let currentIndex = 0;
        const duration = 5000;

        const showSlide = (index) => {
            slides.forEach((slide, idx) => {
                slide.classList.toggle('active', idx === index);
            });
            dots.forEach((dot, idx) => {
                dot.classList.toggle('active', idx === index);
            });
            currentIndex = index;
        };

        const nextSlide = () => showSlide((currentIndex + 1) % slides.length);
        const prevSlide = () => showSlide((currentIndex - 1 + slides.length) % slides.length);

        const startAutoplay = () => {
            stopAutoplay();
            carouselInterval = setInterval(nextSlide, duration);
        };

        const stopAutoplay = () => {
            if (carouselInterval) clearInterval(carouselInterval);
        };

        if (prevBtn) {
            prevBtn.onclick = () => {
                prevSlide();
                startAutoplay();
            };
        }

        if (nextBtn) {
            nextBtn.onclick = () => {
                nextSlide();
                startAutoplay();
            };
        }

        dots.forEach(dot => {
            dot.onclick = () => {
                const index = parseInt(dot.getAttribute('data-index'), 10);
                showSlide(index);
                startAutoplay();
            };
        });

        // دعم اللمس والسحب (Swipe on mobile)
        let touchStartX = 0;
        let touchEndX = 0;

        carousel.ontouchstart = (e) => {
            touchStartX = e.changedTouches[0].screenX;
            stopAutoplay();
        };

        carousel.ontouchend = (e) => {
            touchEndX = e.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 50) {
                nextSlide();
            } else if (touchEndX - touchStartX > 50) {
                prevSlide();
            }
            startAutoplay();
        };

        startAutoplay();
    };

    document.addEventListener('DOMContentLoaded', () => {
        hydrateData();
        initCarousel();
    });
})();
