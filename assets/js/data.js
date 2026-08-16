/**
 * إعدادات وبيانات موقع سوق التل المركزية
 * يمكنك تعديل الروابط والإصدارات ومعلومات التواصل والإعلانات بسهولة من هنا
 */
let siteConfig = {
    appName: "سوق التل",
    appNameEn: "eltal-market",
    description: "تحميل تطبيق سوق التل للأندرويد والآيفون مع أحدث الإصدارات والمزايا.",
    logoUrl: "assets/images/logo.svg",
    faviconUrl: "assets/images/logo.svg",
    shopUrl: "market/index.html",
    
    // أحدث الإصدارات وروابط التحميل
    release: {
        version: "1.0.0",
        releaseDate: "2026-08-16",
        fileSize: "24.5 MB",
        apkUrl: "market/index.html", // رابط تحميل APK المباشر أو متجر التطبيقات
        ipaUrl: "market/index.html", // رابط تحميل iPhone / iOS
        isReady: true
    },

    // البانرات الإعلانية في الصفحة الرئيسية
    banners: [
        {
            id: 1,
            image: "assets/images/download_banner_default.png",
            link: "download.html",
            alt: "إعلان تطبيق سوق التل"
        }
    ],

    // بانر صفحة التحميل
    downloadBanner: {
        image: "assets/images/download_banner_default.png",
        link: "download.html"
    },

    // مميزات التطبيق
    features: [
        { title: "سهولة الاستخدام", desc: "واجهة عربية عصرية وسلسة تناسب جميع المستخدمين." },
        { title: "سرعة في التصفح", desc: "أداء فائق واستجابة فورية لعرض آلاف المنتجات." },
        { title: "تنوع المتاجر والمنتجات", desc: "تسوق من أفضل التجار والمتاجر المحلية في مكان واحد." },
        { title: "تواصل مباشر", desc: "محادثة فورية وسريعة مع البائعين والاستفسار عن المنتجات." },
        { title: "توصيل سريع وآمن", desc: "شبكة توصيل موثوقة تغطي منطقتك وتصل لباب منزلك." },
        { title: "تحديثات مستمرة", desc: "تحسينات دورية للأداء وإضافة مزايا وخدمات جديدة." }
    ],

    // معلومات التواصل الرسمية
    contact: {
        email: "support@elltall.com",
        phone: "+20 123 456 7890",
        website: "https://www.elltall.com",
        address: "التل الكبير - مصر",
        workHours: "من السبت إلى الخميس، 9 صباحًا - 9 مساءً"
    },

    // حسابات التواصل الاجتماعي
    social: {
        facebook: "https://facebook.com",
        instagram: "https://instagram.com",
        tiktok: "https://tiktok.com"
    }
};

// تحميل الإعدادات المحفوظة محلياً إن وجدت لدمج التعديلات فوراً
if (typeof localStorage !== 'undefined') {
    try {
        const saved = localStorage.getItem('eltal_site_config');
        if (saved) {
            const parsed = JSON.parse(saved);
            siteConfig = Object.assign({}, siteConfig, parsed);
        }
    } catch (e) {
        console.error("Error reading saved site config", e);
    }
}

if (typeof window !== 'undefined') {
    window.siteConfig = siteConfig;
}
