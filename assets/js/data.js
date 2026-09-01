/**
 * إعدادات وبيانات موقع سوق التل المركزية
 * تم التحديث تلقائياً وبأمان من لوحة التحكم
 */
let siteConfig = {
    "appName": "سوق التل",
    "appNameEn": "eltal-market",
    "description": "تحميل تطبيق سوق التل للأندرويد والآيفون مع أحدث الإصدارات والمزايا.",
    "logoUrl": "assets/images/logo.svg",
    "version": {
        "android": "1.1.9",
        "ios": "1.1.0",
        "releaseDate": "2026-03-01",
        "whatsNew": "إضافة دعم المتاجر المجاورة، تحسين سرعة التطبيق، وحل مشاكل الإشعارات بالكامل.",
        "fileSize": "107.7 MB",
        "apkUrl": "https://github.com/ahmedsamy32/el-tall-market-download/releases/download/1.1.9/Eltal-Market.apk",
        "ipaUrl": "market/index.html",
        "isReady": true
    },
    "banners": [
        {
            "id": 1,
            "image": "assets/images/download_banner_default.png",
            "link": "download.html",
            "alt": "تحميل تطبيق سوق التل"
        },
        {
            "id": 2,
            "image": "assets/images/banner_offers_default.png",
            "link": "download.html",
            "alt": "عروض حصرية وتوصيل فوري"
        }
    ],
    "features": [
        {
            "title": "سهولة الاستخدام",
            "desc": "واجهة عصرية وبسيطة تتيح لك الوصول لطلبك خلال ثوانٍ معدودة."
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
