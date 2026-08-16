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
        apkUrl: "market/index.html",
        ipaUrl: "market/index.html",
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

/**
 * مدير التخزين المتقدم لتخزين الصور والبيانات الكبيرة دون قيود السعة (IndexedDB + localStorage)
 */
const SiteDB = {
    dbName: 'EltalSiteStorage',
    storeName: 'site_config_store',
    keyName: 'current_config',

    open() {
        return new Promise((resolve, reject) => {
            if (typeof indexedDB === 'undefined') return reject(new Error('IndexedDB not supported'));
            const req = indexedDB.open(this.dbName, 1);
            req.onupgradeneeded = (e) => {
                const db = e.target.result;
                if (!db.objectStoreNames.contains(this.storeName)) {
                    db.createObjectStore(this.storeName);
                }
            };
            req.onsuccess = (e) => resolve(e.target.result);
            req.onerror = (e) => reject(e.target.error);
        });
    },

    async save(configObj) {
        // 1. حفظ في IndexedDB (يدعم ملفات وصور ضخمة بدون حد 5MB)
        try {
            const db = await this.open();
            await new Promise((resolve, reject) => {
                const tx = db.transaction(this.storeName, 'readwrite');
                const store = tx.objectStore(this.storeName);
                const putReq = store.put(configObj, this.keyName);
                putReq.onsuccess = () => resolve(true);
                putReq.onerror = (e) => reject(e.target.error);
            });
        } catch (err) {
            console.warn("IndexedDB save warning:", err);
        }

        // 2. محاولة الحفظ في localStorage كنسخة سريعة
        try {
            localStorage.setItem('eltal_site_config', JSON.stringify(configObj));
        } catch (quotaErr) {
            console.warn("localStorage quota exceeded, saved in IndexedDB safely.");
        }
    },

    async load() {
        // 1. محاولة القراءة من IndexedDB
        try {
            const db = await this.open();
            const data = await new Promise((resolve, reject) => {
                const tx = db.transaction(this.storeName, 'readonly');
                const store = tx.objectStore(this.storeName);
                const getReq = store.get(this.keyName);
                getReq.onsuccess = () => resolve(getReq.result);
                getReq.onerror = (e) => reject(e.target.error);
            });
            if (data && typeof data === 'object') {
                return data;
            }
        } catch (err) {
            console.warn("IndexedDB read fallback to localStorage:", err);
        }

        // 2. القراءة البديلة من localStorage
        try {
            const saved = localStorage.getItem('eltal_site_config');
            if (saved) return JSON.parse(saved);
        } catch (e) {}

        return null;
    }
};

// تحميل فوري أولي من localStorage إن وجد
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
    window.SiteDB = SiteDB;

    // تحميل غير متزامن من IndexedDB لتحديث الصور الضخمة
    SiteDB.load().then(savedConfig => {
        if (savedConfig) {
            window.siteConfig = Object.assign({}, window.siteConfig, savedConfig);
            if (typeof window.rehydrateSiteData === 'function') {
                window.rehydrateSiteData();
            }
        }
    }).catch(() => {});
}
