const pushMarketingEvent = (event) => {
    if (!event || typeof event !== 'object' || !event.event) {
        return;
    }

    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(event);
};

let customOrderStarted = false;

const pushCustomOrderStarted = (source = 'general', productId = null) => {
    if (customOrderStarted) {
        return;
    }

    customOrderStarted = true;

    pushMarketingEvent({
        event: 'custom_order_started',
        custom_order: {
            source,
            ...(productId ? { product_id: String(productId) } : {}),
        },
    });
};

const isWhatsAppUrl = (href) => {
    if (!href) {
        return false;
    }

    if (href.startsWith('whatsapp://')) {
        return true;
    }

    try {
        const url = new URL(href, window.location.href);
        return ['wa.me', 'www.wa.me', 'api.whatsapp.com', 'web.whatsapp.com'].includes(url.hostname);
    } catch {
        return false;
    }
};

// Modal-based entry point used by product/custom-order CTAs.
window.addEventListener('open-custom-modal', (event) => {
    const productId = event?.detail?.productId;
    pushCustomOrderStarted(productId ? 'product' : 'general', productId);
});

// Emitted by the Livewire component only after a custom request is persisted
// and its notification emails have been queued successfully.
window.addEventListener('custom-order-sent', (event) => {
    const productId = event?.detail?.productId;
    const source = event?.detail?.source || (productId ? 'product' : 'general');

    pushMarketingEvent({
        event: 'custom_order_sent',
        custom_order: {
            source,
            ...(productId ? { product_id: String(productId) } : {}),
        },
    });
});

// Capture outbound intent before navigation or another click handler can consume it.
document.addEventListener('click', (event) => {
    const target = event.target instanceof Element ? event.target.closest('a, button') : null;

    if (!(target instanceof HTMLElement)) {
        return;
    }

    if (target instanceof HTMLAnchorElement) {
        const rawHref = target.getAttribute('href') || '';
        let hash = '';

        try {
            hash = new URL(target.href, window.location.href).hash;
        } catch {
            hash = rawHref.includes('#') ? `#${rawHref.split('#').pop()}` : '';
        }

        if (hash === '#cerere-personalizata') {
            pushCustomOrderStarted('contact_form');
        }

        if (isWhatsAppUrl(rawHref || target.href)) {
            pushMarketingEvent({
                event: 'whatsapp_click',
                contact: {
                    source: window.location.pathname,
                },
            });
        }
    }
}, true);
