const UMAMI_SCRIPT_URL = 'https://analytics.mtdart.ro/script.js';
const UMAMI_WEBSITE_ID = '0e991672-a898-4eb4-b4e6-c3c77731f470';
const TRACKED_HOSTS = new Set(['mtdart.ro', 'www.mtdart.ro']);

if (TRACKED_HOSTS.has(window.location.hostname)) {
    const existingTracker = document.querySelector(
        `script[src="${UMAMI_SCRIPT_URL}"][data-website-id="${UMAMI_WEBSITE_ID}"]`,
    );

    if (!existingTracker) {
        const script = document.createElement('script');
        script.defer = true;
        script.src = UMAMI_SCRIPT_URL;
        script.dataset.websiteId = UMAMI_WEBSITE_ID;
        script.dataset.domains = 'mtdart.ro,www.mtdart.ro';
        document.head.appendChild(script);
    }
}
