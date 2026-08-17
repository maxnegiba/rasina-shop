(() => {
    'use strict';

    const ready = (callback) => {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
        } else {
            callback();
        }
    };

    ready(() => {
        const alertModal = document.querySelector('.js-lcc-modal-alert');
        const settingsModal = document.querySelector('.js-lcc-modal-settings');
        const backdrop = document.querySelector('.js-lcc-backdrop');
        const analytics = document.getElementById('lcc-checkbox-analytics');
        const marketing = document.getElementById('lcc-checkbox-marketing');

        if (!alertModal || !settingsModal || !backdrop || !analytics || !marketing) {
            return;
        }

        const values = {
            analytics: alertModal.dataset.cookieValueAnalytics || '2',
            marketing: alertModal.dataset.cookieValueMarketing || '3',
            both: alertModal.dataset.cookieValueBoth || 'true',
            none: alertModal.dataset.cookieValueNone || 'false',
        };

        const cookieKey = alertModal.dataset.cookieKey || '__cookie_consent';
        const expirationDays = Number(alertModal.dataset.cookieExpirationDays || 365);
        const gtmEvent = alertModal.dataset.gtmEvent || 'cookie_refresh';
        const cookieSecure = alertModal.dataset.cookieSecure === 'true';
        const sessionDomain = (alertModal.dataset.sessionDomain || '').trim();
        let consentChangeCallback = null;

        const readCookie = () => {
            const prefix = `${encodeURIComponent(cookieKey)}=`;
            const entry = document.cookie
                .split(';')
                .map((item) => item.trim())
                .find((item) => item.startsWith(prefix));

            return entry ? decodeURIComponent(entry.slice(prefix.length)) : null;
        };

        const consentStateFromValue = (value) => {
            const analyticsGranted = value === values.analytics || value === values.both;
            const marketingGranted = value === values.marketing || value === values.both;

            return {
                analytics_storage: analyticsGranted ? 'granted' : 'denied',
                ad_storage: marketingGranted ? 'granted' : 'denied',
                ad_user_data: marketingGranted ? 'granted' : 'denied',
                ad_personalization: marketingGranted ? 'granted' : 'denied',
            };
        };

        const hide = (element) => {
            element.style.display = 'none';
        };

        const show = (element) => {
            element.style.display = 'block';
        };

        const closeAll = () => {
            hide(alertModal);
            hide(settingsModal);
            hide(backdrop);
            document.documentElement.classList.remove('js-lcc-active');
        };

        const showBackdrop = () => {
            show(backdrop);
            document.documentElement.classList.add('js-lcc-active');
        };

        const syncCheckboxes = () => {
            const current = readCookie();
            analytics.checked = current === values.analytics || current === values.both;
            marketing.checked = current === values.marketing || current === values.both;
        };

        const openSettings = () => {
            syncCheckboxes();
            hide(alertModal);
            show(settingsModal);
            showBackdrop();
            settingsModal.querySelector('button, input')?.focus();
        };

        const openAlert = () => {
            hide(settingsModal);
            show(alertModal);
            showBackdrop();
            alertModal.querySelector('button')?.focus();
        };

        const writeConsent = (value) => {
            const expires = new Date(Date.now() + expirationDays * 86400000).toUTCString();
            const parts = [
                `${encodeURIComponent(cookieKey)}=${encodeURIComponent(value)}`,
                `expires=${expires}`,
                'path=/',
                'SameSite=Lax',
            ];

            if (sessionDomain && sessionDomain !== 'null') {
                parts.push(`domain=${sessionDomain}`);
            }

            if (cookieSecure) {
                parts.push('Secure');
            }

            document.cookie = parts.join('; ');

            if (typeof window.gtag === 'function') {
                window.gtag('consent', 'update', consentStateFromValue(value));
            }

            if (typeof consentChangeCallback === 'function') {
                consentChangeCallback(value);
            }

            if (Array.isArray(window.dataLayer)) {
                window.dataLayer.push({ event: gtmEvent, cookie_consent: value });
            }
        };

        const saveSelection = () => {
            let value = values.none;

            if (analytics.checked && marketing.checked) {
                value = values.both;
            } else if (analytics.checked) {
                value = values.analytics;
            } else if (marketing.checked) {
                value = values.marketing;
            }

            writeConsent(value);
            closeAll();
        };

        const addFooterPreferenceLink = () => {
            if (document.querySelector('[data-cookie-preferences-footer]')) {
                return;
            }

            const footer = document.querySelector('footer');
            if (!footer) {
                return;
            }

            const usefulHeading = [...footer.querySelectorAll('h3')]
                .find((heading) => heading.textContent.trim().toLowerCase().includes('informații utile'));
            const list = usefulHeading?.parentElement?.querySelector('ul');

            if (!list) {
                return;
            }

            const item = document.createElement('li');
            const link = document.createElement('a');
            link.href = '#cookie-preferences';
            link.textContent = 'Preferințe cookie';
            link.className = 'js-lcc-settings-toggle hover:text-white hover:translate-x-1 transition-all duration-300 inline-block';
            link.dataset.cookiePreferencesFooter = 'true';
            link.setAttribute('aria-label', 'Deschide preferințele pentru cookie-uri');
            item.appendChild(link);
            list.appendChild(item);
        };

        document.addEventListener('click', (event) => {
            const settingsToggle = event.target.closest('.js-lcc-settings-toggle');
            if (settingsToggle) {
                event.preventDefault();

                if (settingsModal.style.display !== 'none') {
                    closeAll();
                } else {
                    openSettings();
                }
                return;
            }

            if (event.target.closest('.js-lcc-accept')) {
                writeConsent(values.both);
                closeAll();
                return;
            }

            if (event.target.closest('.js-lcc-essentials')) {
                writeConsent(values.none);
                closeAll();
                return;
            }

            if (event.target.closest('.js-lcc-settings-save')) {
                saveSelection();
            }
        });

        backdrop.addEventListener('click', () => {
            if (settingsModal.style.display !== 'none' && readCookie() !== null) {
                closeAll();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && settingsModal.style.display !== 'none' && readCookie() !== null) {
                closeAll();
            }
        });

        window.cookieBannerConsentChange = (callback) => {
            consentChangeCallback = callback;
        };

        addFooterPreferenceLink();
        syncCheckboxes();

        if (readCookie() === null) {
            openAlert();
        }
    });
})();
