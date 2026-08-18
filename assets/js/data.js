/**
 * إعدادات وبيانات موقع سوق التل المركزية
 * تم التحديث تلقائياً وبأمان من لوحة التحكم
 */
let siteConfig = {
    "appName": "سوق التل",
    "appNameEn": "eltal-market",
    "description": "تحميل تطبيق سوق التل للأندرويد والآيفون مع أحدث الإصدارات والمزايا.",
    "logoUrl": "assets/images/logo.svg",
    "faviconUrl": "assets/images/logo.svg",
    "shopUrl": "market/",
    "release": {
        "version": "1.0.0",
        "releaseDate": "2026-08-16",
        "fileSize": "24.5 MB",
        "apkUrl": "market/index.html",
        "ipaUrl": "market/index.html",
        "isReady": true
    },
    "banners": [
        {
            "id": 1,
            "image": "assets/images/download_banner_default.png",
            "link": "download.html",
            "alt": "إعلان تطبيق سوق التل"
        }
    ],
    "downloadBanner": {
        "image": "assets/images/download_banner_default.png",
        "link": "download.html"
    },
    "features": [
        {
            "title": "سهولة الاستخدام",
            "desc": "واجهة عربية عصرية وسلسة تناسب جميع المستخدمين."
        },
        {
            "title": "سرعة في التصفح",
            "desc": "أداء فائق واستجابة فورية لعرض آلاف المنتجات."
        },
        {
            "title": "تنوع المتاجر والمنتجات",
            "desc": "تسوق من أفضل التجار والمتاجر المحلية في مكان واحد."
        },
        {
            "title": "تواصل مباشر",
            "desc": "محادثة فورية وسريعة مع البائعين والاستفسار عن المنتجات."
        },
        {
            "title": "توصيل سريع وآمن",
            "desc": "شبكة توصيل موثوقة تغطي منطقتك وتصل لباب منزلك."
        },
        {
            "title": "تحديثات مستمرة",
            "desc": "تحسينات دورية للأداء وإضافة مزايا وخدمات جديدة."
        }
    ],
    "contact": {
        "email": "info@eltal-market.com",
        "phone": "+201146668812",
        "website": "https://eltal-market.com",
        "address": "التل الكبير- الاسماعيلية - مصر",
        "workHours": "من السبت إلى الخميس، 9 صباحًا - 9 مساءً"
    },
    "social": {
        "facebook": "",
        "instagram": "",
        "tiktok": ""
    }
};

// مدير التخزين المتقدم
if (typeof localStorage !== 'undefined') {
    try {
        const saved = localStorage.getItem('eltal_site_config');
        if (saved) {
            const parsed = JSON.parse(saved);
            siteConfig = Object.assign({}, siteConfig, parsed);
        }
    } catch (e) {}
}

if (typeof window !== 'undefined') {
    window.siteConfig = siteConfig;
}
