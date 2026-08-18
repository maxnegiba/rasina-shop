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

// Modal-based entry point used by product/custom-order CTAs.
window.addEventListener('open-custom-modal', (event) => {
    const productId = event?.detail?.productId;
    pushCustomOrderStarted(productId ? 'product' : 'general', productId);
});

// Contact-page entry point currently used by the "Spre formular unicat" CTA.
// The click is the first explicit user action that enters the custom-order flow.
document.addEventListener('click', (event) => {
    const target = event.target instanceof Element ? event.target.closest('a, button') : null;

    if (!(target instanceof HTMLElement)) {
        return;
    }

    const href = target instanceof HTMLAnchorElement ? target.getAttribute('href') || '' : '';

    if (href.includes('#cerere-personalizata')) {
        pushCustomOrderStarted('contact_form');
    }
});
