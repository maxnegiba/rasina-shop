const pushMarketingEvent = (event) => {
    if (!event || typeof event !== 'object' || !event.event) {
        return;
    }

    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(event);
};

let customOrderStarted = false;

window.addEventListener('open-custom-modal', (event) => {
    if (customOrderStarted) {
        return;
    }

    customOrderStarted = true;

    const productId = event?.detail?.productId;

    pushMarketingEvent({
        event: 'custom_order_started',
        custom_order: {
            source: productId ? 'product' : 'general',
            ...(productId ? { product_id: String(productId) } : {}),
        },
    });
});
