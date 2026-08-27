const UMAMI_SCRIPT_URL = 'https://analytics.mtdart.ro/script.js';
const UMAMI_WEBSITE_ID = '0e991672-a898-4eb4-b4e6-c3c77731f470';
const TRACKED_HOSTS = new Set(['mtdart.ro', 'www.mtdart.ro']);

const loadUmami = () => {
    if (!TRACKED_HOSTS.has(window.location.hostname)) {
        return;
    }

    const existingTracker = document.querySelector(
        `script[src="${UMAMI_SCRIPT_URL}"][data-website-id="${UMAMI_WEBSITE_ID}"]`,
    );

    if (existingTracker) {
        return;
    }

    const script = document.createElement('script');
    script.defer = true;
    script.src = UMAMI_SCRIPT_URL;
    script.dataset.websiteId = UMAMI_WEBSITE_ID;
    script.dataset.domains = 'mtdart.ro,www.mtdart.ro';
    document.head.appendChild(script);
};

const analyticsConsentGranted = () => {
    const modal = document.querySelector('.js-lcc-modal-alert');
    if (!modal) {
        return false;
    }

    const cookieKey = modal.dataset.cookieKey || '__cookie_consent';
    const analyticsValue = modal.dataset.cookieValueAnalytics || '2';
    const bothValue = modal.dataset.cookieValueBoth || 'true';
    const prefix = `${encodeURIComponent(cookieKey)}=`;
    const entry = document.cookie
        .split(';')
        .map((item) => item.trim())
        .find((item) => item.startsWith(prefix));

    if (!entry) {
        return false;
    }

    const value = decodeURIComponent(entry.slice(prefix.length));
    return value === analyticsValue || value === bothValue;
};

const initialize = () => {
    if (analyticsConsentGranted()) {
        loadUmami();
    }

    if (typeof window.cookieBannerConsentChange === 'function') {
        window.cookieBannerConsentChange((value) => {
            const modal = document.querySelector('.js-lcc-modal-alert');
            const analyticsValue = modal?.dataset.cookieValueAnalytics || '2';
            const bothValue = modal?.dataset.cookieValueBoth || 'true';

            if (value === analyticsValue || value === bothValue) {
                loadUmami();
            }
        });
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialize, { once: true });
} else {
    initialize();
}
