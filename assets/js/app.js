/**
 * تطبيق سوق التل - JavaScript الرئيسي
 */
(() => {
    const storageKey = 'souk_altal_theme';
    const root = document.documentElement;

    // 1. إدارة الوضع الليلي والنهاري (Theme Management)
    const applyTheme = (theme) => {
        if (theme === 'dark') {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
        localStorage.setItem(storageKey, theme);
        updateThemeIcons(theme);
    };

    const updateThemeIcons = (theme) => {
        document.querySelectorAll('.theme-toggle-icon').forEach(icon => {
            icon.textContent = theme === 'dark' ? '☀' : '☾';
        });
    };

    const storedTheme = localStorage.getItem(storageKey);
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

    // 3. حقن البيانات المركزية في عناصر الصفحة (Dynamic Data Hydration)
    const hydrateData = () => {
        if (typeof window.siteConfig === 'undefined') return;
        const cfg = window.siteConfig;

        // روابط التحميل والإصدارات
        document.querySelectorAll('[data-bind-version]').forEach(el => el.textContent = cfg.release.version);
        document.querySelectorAll('[data-bind-filesize]').forEach(el => el.textContent = cfg.release.fileSize);
        document.querySelectorAll('[data-bind-releasedate]').forEach(el => el.textContent = cfg.release.releaseDate);
        
        document.querySelectorAll('[data-apk-url]').forEach(el => {
            if (cfg.release.apkUrl && cfg.release.apkUrl !== '#') {
                el.setAttribute('href', cfg.release.apkUrl);
            }
        });
        document.querySelectorAll('[data-ipa-url]').forEach(el => {
            if (cfg.release.ipaUrl && cfg.release.ipaUrl !== '#') {
                el.setAttribute('href', cfg.release.ipaUrl);
            }
        });

        // روابط التواصل
        document.querySelectorAll('[data-bind-email]').forEach(el => {
            el.textContent = cfg.contact.email;
            if (el.tagName === 'A') el.setAttribute('href', 'mailto:' + cfg.contact.email);
        });
        document.querySelectorAll('[data-bind-phone]').forEach(el => {
            el.textContent = cfg.contact.phone;
            if (el.tagName === 'A') el.setAttribute('href', 'tel:' + cfg.contact.phone.replace(/[^0-9+]/g, ''));
        });
        document.querySelectorAll('[data-bind-website]').forEach(el => {
            el.textContent = cfg.contact.website;
            if (el.tagName === 'A') el.setAttribute('href', cfg.contact.website);
        });
        document.querySelectorAll('[data-bind-address]').forEach(el => {
            el.textContent = cfg.contact.address;
        });

        // روابط السوشيال ميديا
        document.querySelectorAll('[data-social-facebook]').forEach(el => el.setAttribute('href', cfg.social.facebook));
        document.querySelectorAll('[data-social-instagram]').forEach(el => el.setAttribute('href', cfg.social.instagram));
        document.querySelectorAll('[data-social-tiktok]').forEach(el => el.setAttribute('href', cfg.social.tiktok));
    };

    // 4. تشغيل السلايدر التفاعلي (Carousel Logic)
    const initCarousel = () => {
        const carousel = document.getElementById('homepage-carousel');
        if (!carousel) return;

        const slides = carousel.querySelectorAll('.carousel-slide');
        const dots = carousel.querySelectorAll('.carousel-dot');
        const prevBtn = carousel.querySelector('#carousel-prev');
        const nextBtn = carousel.querySelector('#carousel-next');

        if (slides.length <= 1) return;

        let currentIndex = 0;
        let intervalId = null;
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
            intervalId = setInterval(nextSlide, duration);
        };

        const stopAutoplay = () => {
            if (intervalId) clearInterval(intervalId);
        };

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                prevSlide();
                startAutoplay();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                nextSlide();
                startAutoplay();
            });
        }

        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                const index = parseInt(dot.getAttribute('data-index'), 10);
                showSlide(index);
                startAutoplay();
            });
        });

        // دعم اللمس والسحب (Swipe on mobile)
        let touchStartX = 0;
        let touchEndX = 0;

        carousel.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
            stopAutoplay();
        }, { passive: true });

        carousel.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 50) {
                // سحب لليسار
                nextSlide();
            } else if (touchEndX - touchStartX > 50) {
                // سحب لليمين
                prevSlide();
            }
            startAutoplay();
        }, { passive: true });

        startAutoplay();

        // ضبط الارتفاع التلقائي
        const container = carousel.querySelector('.carousel-container');
        const firstImg = carousel.querySelector('.carousel-slide img');
        if (container && firstImg) {
            const adjustHeight = () => {
                const naturalW = firstImg.naturalWidth;
                const naturalH = firstImg.naturalHeight;
                if (!naturalW || !naturalH) return;
                const containerW = container.offsetWidth;
                let newH = Math.round(containerW * naturalH / naturalW);
                newH = Math.max(160, Math.min(500, newH));
                container.style.height = newH + 'px';
            };

            if (firstImg.complete && firstImg.naturalWidth) {
                adjustHeight();
            } else {
                firstImg.addEventListener('load', adjustHeight);
            }
            window.addEventListener('resize', adjustHeight);
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        hydrateData();
        initCarousel();
    });
})();
