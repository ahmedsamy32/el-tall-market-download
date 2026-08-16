(() => {
    const storageKey = 'souk_altal_theme';
    const root = document.documentElement;

    const applyTheme = (theme) => {
        if (theme === 'dark') {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
        localStorage.setItem(storageKey, theme);
    };

    const storedTheme = localStorage.getItem(storageKey);
    const preferredTheme = storedTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    applyTheme(preferredTheme);

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-theme-toggle]');
        if (!button) {
            return;
        }
        applyTheme(root.classList.contains('dark') ? 'light' : 'dark');
    });

    document.querySelectorAll('[data-reveal]').forEach((element, index) => {
        element.style.animationDelay = `${index * 85}ms`;
        element.classList.add('fade-up');
    });

    const qrTarget = document.querySelector('[data-qr-target]');
    if (qrTarget && window.QRCode) {
        const text = qrTarget.getAttribute('data-qr-target') || window.location.href;
        qrTarget.innerHTML = '';
        new QRCode(qrTarget, {
            text,
            width: 150,
            height: 150,
            colorDark: '#0f172a',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M,
        });
    }
})();
